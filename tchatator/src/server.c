//   _______   _           _        _
//  |__   __| | |         | |      | |
//     | | ___| |__   __ _| |_ __ _| |_ ___  _ __
//     | |/ __| '_ \ / _` | __/ _` | __/ _ \| '__|
//     | | (__| | | | (_| | || (_| | || (_) | |
//     |_|\___|_| |_|\__,_|\__\__,_|\__\___/|_|
//

#include <errno.h>
#include <pthread.h>
#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/time.h>
#include <time.h>
#include <unistd.h>

#include <sys/ipc.h>
#include <sys/sem.h>
#include <sys/shm.h>

#include <signal.h>
#include <sys/wait.h>

#include <arpa/inet.h>
#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/types.h>

#include <libpq-fe.h>

#include "config.h"
#include "database.h"
#include "log.h"
#include "protocol.h"
#include "utils.h"
#include <bits/getopt_core.h>

#define CLIENT_CAPACITY_INCR 10

// -------------------------------------------------------------------------
// Structure
// -------------------------------------------------------------------------

typedef struct
{
    int sock;
    pid_t pid; // child pid
    char ip[CHAR_SIZE];
    user_t user;
} client_t;

typedef struct {
    // All connected clients
    client_t* clients;
    int clients_count;
    // List of user ids that are banned
    int* banned_clients;
    int banned_clients_count;
    //
    blocked_user_t* blocked_clients;
    int blocked_clients_count;
} server_data_t;

// -------------------------------------------------------------------------
// Global variables
// -------------------------------------------------------------------------

volatile sig_atomic_t running = 1;

pid_t server_pid;

// Server data, shared between the server and its children
server_data_t* server_data;
int shmid_server_data;

// -------------------------------------------------------------------------
// Functions signatures
// -------------------------------------------------------------------------

void signal_handler(int sig);
void send_response(int sock, response_status_t status, ...);
void set_sock_timeout(int sock, int timeout_ms);
int parse_command(char command_str[], command_t* command);

client_t init_client(int sock, pid_t pid, char ip[], user_t user);
void add_client(int sock, pid_t pid, char ip[], user_t user);
void remove_client(pid_t pid);
client_t get_client(pid_t pid);
int client_pid_exist(pid_t pid);
int client_connected(int user_id);
int client_banned(int user_id);
void refresh_blocked_users(server_data_t* server_data, PGconn* conn);

// -------------------------------------------------------------------------
// Main
// -------------------------------------------------------------------------

int main(int argc, char* argv[])
{
    int options;

    // Sockets
    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;

    // Client
    int client_port;
    int client_pid;
    int current_clients_max_capacity;
    char client_ip[CHAR_SIZE];

    // Current command stuff
    char command_recv[LARGE_CHAR_SIZE];
    int command_recv_len;
    command_t command;
    int command_parsed;

    config_t* config;
    char* config_path;
    PGconn* conn;

    // Register signals
    signal(SIGINT, signal_handler);
    signal(SIGQUIT, signal_handler);
    signal(SIGCHLD, signal_handler);

    server_pid = getpid();
    current_clients_max_capacity = CLIENT_CAPACITY_INCR;

    // Setup shared memory for server data
    shmid_server_data = shmget(IPC_PRIVATE, sizeof(server_data_t), 0666 | IPC_CREAT);
    server_data = (server_data_t*)shmat(shmid_server_data, NULL, 0);

    // Initialize server data
    server_data->clients = malloc(current_clients_max_capacity * sizeof(client_t));
    server_data->clients_count = 0;
    server_data->banned_clients = NULL;
    server_data->banned_clients_count = 0;
    server_data->blocked_clients = NULL;
    server_data->blocked_clients_count = 0;

    // Setup log and config
    log_verbose = 0;
    config = malloc(sizeof(config_t));
    config_path = NULL;

    // Handles options (--help, -h, --verbose, --config, -c, ...)
    while ((options = getopt(argc, argv, "hvc:")) != -1) {
        switch (options) {
        case 'h':
            printf("Usage : server [options]\n");
            printf("Options :\n");
            printf("-v, --verbose           show logs\n");
            printf("-h, --help              shows help\n");
            printf("-c, --config <path>     specify the location of the configuration file\n");

            exit(0);
        case 'v':
            log_verbose = 1;
            break;
        case 'c':
            config_path = malloc(sizeof(optarg));
            strcpy(config_path, optarg);
            break;
        case '?':
            return 1;
        default:
            abort();
        }
    }

    // log_verbose = 1; // only for dev

    // Load env variables
    env_load(".");

    // Initialize config
    config_load(config, config_path);

    // Set log settings
    strcpy(log_file_path, config->log_file);

    log_info("Starting Tchatator");

    // Login to the DB
    db_login(&conn);

    log_info("Connection to the database established");

    // Setup the socket
    sock = socket(AF_INET, SOCK_STREAM, 0);

    // Remove the 'Address already in use' problem
    int reuse = 1;
    setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &reuse, sizeof(int));

    sock_addr.sin_addr.s_addr = INADDR_ANY;
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(config->port);

    sock_ret = bind(sock, (struct sockaddr*)&sock_addr, sizeof(sock_addr));

    if (sock_ret < 0) {
        perror("Cannot bind the socket");
        exit(1);
    }

    log_info("Listening on address \"%s\", port %d", inet_ntoa(sock_addr.sin_addr), config->port);

    sock_ret = listen(sock, 10);

    if (sock_ret) {
        perror("Cannot listen connections");
        exit(1);
    }

    log_info("Ready to accept connections");

    sock_conn_addr_size = sizeof(sock_conn_addr);

    // Load banned and blocked users
    db_get_banned_users(conn, &server_data->banned_clients, &server_data->banned_clients_count);
    db_get_blocked_users(conn, &server_data->blocked_clients, &server_data->blocked_clients_count);

    // Start the server loop
    while (running) {
        sock_conn = accept(sock, (struct sockaddr*)&sock_conn_addr, (socklen_t*)&sock_conn_addr_size);

        if (sock_conn < 0) {
            perror("Cannot accept connection");
            exit(1);
        }

        if (!running)
            break;

        // Retrieve the client ip and port
        client_port = ntohs(sock_conn_addr.sin_port);
        inet_ntop(AF_INET, &sock_conn_addr.sin_addr, client_ip, INET_ADDRSTRLEN);
        sprintf(client_ip + strlen(client_ip), ":%d", client_port); // add port to ip

        // Increase the clients capacity if needed
        if (server_data->clients_count >= current_clients_max_capacity && server_pid == getpid()) {
            log_info("Increase client capacity");
            current_clients_max_capacity += CLIENT_CAPACITY_INCR;

            server_data->clients = realloc(server_data->clients, current_clients_max_capacity * sizeof(client_t));
        }

        // Create a new child process
        if ((client_pid = fork()) == 0) {
            close(sock);

            int client_id = (server_data->clients_count) - 1;
            char api_token[API_TOKEN_SIZE];
            int client_login = 0;
            message_t message;

            // For log
            strcpy(log_client_ip, client_ip);

            // Child loop
            while (1) {
                // printf("Client id %d\n", client_id);
                // printf("C Clients count %d\n", server_data->clients_count);
                // for (int i = 0; i < server_data->clients_count; i++) {
                //     if (i == client_id) {
                //         printf("C Client (ip: %s) (id: %d) (email: %s) (logged in)\n", server_data->clients[i].ip, server_data->clients[i].user.id, server_data->clients[i].user.email);
                //     } else {
                //     printf("C Client (ip: %s) (id: %d) (email: %s)\n", server_data->clients[i].ip, server_data->clients[i].user.id, server_data->clients[i].user.email);
                //     }
                // }

                memset(command_recv, 0, sizeof(command_recv));

                // Wait for a client command
                command_recv_len = recv(sock_conn, command_recv, sizeof(command_recv), 0);

                // printf("Command received: %s\n", command_recv);

                if (command_recv_len < 0) {
                    perror("Error reading from socket");
                    exit(1);
                }

                // Parse command and check if it's valid
                command_parsed = parse_command(command_recv, &command);

                if (!command_parsed) {
                    send_response(sock_conn, STATUS_MIS_FORMAT, "message", "Message mal formaté", NULL);
                    log_info("Invalid action received");
                } else {
                    // Then handle commands logic

                    log_info("Action received [%s]", command.name);

                    // Disconnect command
                    if (strcmp(command.name, DISCONNECTED) == 0) {
                        break;
                    }

                    // Handle the login command
                    if (strcmp(command.name, LOGIN) == 0 && !client_login) {
                        strcpy(api_token, get_command_param_value(command, "api-token"));

                        user_t tmp_user;
                        int user_found = db_get_user_by_api_token(conn, &tmp_user, api_token);

                        if (!user_found) {
                            send_response(sock_conn, STATUS_DENIED, "message", "Accès refusé", NULL);
                            log_info("Access denied for %s", client_ip);
                            continue;
                        } else {
                            // Check if the user is banned
                            if (client_banned(tmp_user.id)) {
                                send_response(sock_conn, STATUS_DENIED, "message", "Utilisateur banni", NULL);
                                log_info("User %s try to connect but is banned", tmp_user.email);
                                continue;
                            }

                            // Check if the user is already connected
                            if (client_connected(tmp_user.id)) {
                                send_response(sock_conn, STATUS_DENIED, "message", "Utilisateur déjà connecté", NULL);
                                log_info("User %s already connected", tmp_user.email);
                                continue;
                            }

                            // For log
                            strcpy(log_client_identity, tmp_user.email);

                            client_login = 1;
                            server_data->clients[client_id].user = tmp_user;

                            log_info("Client (%d) logged in", getpid());
                            send_response(sock_conn, STATUS_OK, "message", "Accès autorisé", NULL);
                            continue;
                        }
                    }

                    if (client_login) {
                        // Check if the token is valid
                        if (strcmp(get_command_param_value(command, "token"), server_data->clients[client_id].user.api_token) != 0) {
                            send_response(sock_conn, STATUS_UNAUTHORIZED, "message", "Client non identifié", NULL);
                            log_info("Client not identified");
                            continue;
                        }

                        // Handle all commands that need to be logged in
                        if (strcmp(command.name, SEND_MESSAGE) == 0) {
                            message = init_message(
                                server_data->clients[client_id].user.id,
                                atoi(get_command_param_value(command, "receiver-id")),
                                get_command_param_value(command, "content"));

                            db_create_message(conn, &message);

                            log_info("Message (%d) send from %d to %d", message.id, server_data->clients[client_id].user.id, message.receiver_id);

                            send_response(sock_conn, STATUS_OK, "message", "Message bien reçu et traité", NULL);
                        } else if (strcmp(command.name, UPDATE_MESSAGE) == 0) {
                            db_get_message(conn, atoi(get_command_param_value(command, "message-id")), &message);

                            strcpy(message.content, get_command_param_value(command, "content"));

                            db_update_message(conn, &message);

                            send_response(sock_conn, STATUS_OK, "message", "Message mis à jour avec succès", NULL);

                            log_info("Message (%d) updated with success", message.id);
                        } else if (strcmp(command.name, DELETE_MESSAGE) == 0) {
                            db_delete_message(conn, atoi(get_command_param_value(command, "message-id")));

                            send_response(sock_conn, STATUS_OK, "message", "Message supprimé avec succès", NULL);
                            log_info("Message (%d) deleted with success", atoi(get_command_param_value(command, "message-id")));
                        } else if (strcmp(command.name, NEW_CHANGE_AVAILABLE) == 0) {
                        } else if (strcmp(command.name, BLOCK_USER) == 0) {
                            db_block_user(conn, atoi(get_command_param_value(command, "user-id")), atoi(get_command_param_value(command, "for-user-id")), atoi(get_command_param_value(command, "duration")));

                            server_data->blocked_clients = realloc(server_data->blocked_clients, (server_data->blocked_clients_count + 1) * sizeof(blocked_user_t));
                            server_data->blocked_clients[server_data->blocked_clients_count].user_id = atoi(get_command_param_value(command, "user-id"));
                            server_data->blocked_clients[server_data->blocked_clients_count].for_user_id = atoi(get_command_param_value(command, "for-user-id"));
                            server_data->blocked_clients[server_data->blocked_clients_count].duration = atoi(get_command_param_value(command, "duration"));
                            server_data->blocked_clients_count++;

                            send_response(sock_conn, STATUS_OK, "message", "Utilisateur bloqué avec succès", NULL);
                            log_info("User %d blocked with success for %d hours", atoi(get_command_param_value(command, "user-id")), atoi(get_command_param_value(command, "duration")));
                        } else if (strcmp(command.name, BAN_USER) == 0) {
                            db_ban_user(conn, atoi(get_command_param_value(command, "user-id")));

                            // Add to the banned list
                            server_data->banned_clients = realloc(server_data->banned_clients, (server_data->banned_clients_count + 1) * sizeof(int));
                            server_data->banned_clients[server_data->banned_clients_count] = atoi(get_command_param_value(command, "user-id"));
                            server_data->banned_clients_count++;

                            send_response(sock_conn, STATUS_OK, "message", "Utilisateur banni avec succès", NULL);
                            log_info("User %d banned with success", atoi(get_command_param_value(command, "user-id")));
                        }
                    } else {
                        send_response(sock_conn, STATUS_DENIED, "message", "Action non autorisée", NULL);
                    }
                }
            }

            shmdt(server_data);
            exit(0);
        } else if (client_pid == -1) {
            perror("Fork");
            abort();
        } else {
            // Add the new client to the list
            add_client(sock_conn, client_pid, client_ip, NOT_CONNECTED_USER);
            log_info("New connection with %s (%d)", client_ip, client_pid);
        }

        // printf("S Clients count %d\n", server_data->clients_count);
        // for (int i = 0; i < server_data->clients_count; i++) {
        //     printf("S Client %s %s\n", server_data->clients[i].ip, server_data->clients[i].user.email);
        // }
    }

    close(sock_conn);
    close(sock);

    // Free memory
    PQfinish(conn);
    free(config);

    log_info("Tchatator was shut down");

    return EXIT_SUCCESS;
}

// -------------------------------------------------------------------------
// Functions
// -------------------------------------------------------------------------

/// @brief Send status message to the client
/// @param sock client socket
/// @param status response status
/// @param ... represents the data to send, the last argument must be NULL
///
/// Example:
///
/// ```
/// send_response(sock, STATUS_OK, "name", "Victor", NULL);
/// ```
void send_response(int sock, response_status_t status, ...)
{
    char buf[CHAR_SIZE];
    response_t response = create_response(status);
    va_list args;
    char* name;
    char* value;

    va_start(args, status);

    while ((name = va_arg(args, char*)) != NULL) {
        if ((value = va_arg(args, char*)) == NULL) {
            break;
        }

        add_response_data(&response, name, value);
    }

    va_end(args);

    strcpy(buf, format_response(response));

    send(sock, buf, strlen(buf), 0);
}

/// @brief Handle signals (SIGINT, SIGQUIT, SIGCHLD)
/// @param sig
void signal_handler(int sig)
{
    pid_t self = getpid();

    if (sig == SIGCHLD) {
        int status;
        pid_t child;
        client_t client;

        for (;;) {
            child = waitpid(0, &status, WNOHANG);
            client = get_client(child);
            if (child > 0 && WIFEXITED(status) && WEXITSTATUS(status) == 0) {
                log_info("Deconnection of %s (%d)", client.ip, child);
                remove_client(child);
            } else if (child < 0 && errno == EINTR) {
                continue;
            } else {
                break;
            }
        }
    }

    if (sig == SIGINT || sig == SIGQUIT) {
        running = 0;
        shmdt(server_data);
        shmctl(shmid_server_data, IPC_RMID, NULL);
    }

    if (sig == SIGINT && server_pid == self) {
        if (server_data->clients_count > 0) {
            kill(0, SIGQUIT);

            for (int i = 0; i < server_data->clients_count; i++) {
                int status;

                for (;;) {
                    pid_t child = wait(&status);
                    if (child > 0 && WIFEXITED(status) && WEXITSTATUS(status) == 0) {
                        log_info("Child %d succesully quit", (int)child);
                    } else if (child < 0 && errno == EINTR) {
                        continue;
                    } else {
                        perror("Wait");
                        break;
                    }
                    break;
                }
            }

            exit(0); // FIXME
        }
    }

    if (sig == SIGQUIT) {
        // Kill children
        if (server_pid != self) {
            // printf("Child %d kill itself\n", (int)self);
            _exit(0);
        }
    }
}

void set_sock_timeout(int sock, int timeout_ms)
{
    struct timeval tv;
    tv.tv_sec = 0;
    tv.tv_usec = timeout_ms;
    setsockopt(sock, SOL_SOCKET, SO_RCVTIMEO, (const char*)&tv, sizeof(struct timeval));
}

int parse_command(char command_str[], command_t* command)
{
    // printf("\nEnter parse action\n");

    int i = 0;
    char c;
    char line[CHAR_SIZE];
    int is_command_exist = 0;

    command_def_t command_def;
    int param_index = 0;

    char* param_name;
    char* param_value;

    memset(line, 0, CHAR_SIZE);

    if (strstr(command_str, "\n") == NULL) {
        return -1;
    }

    // printf("Command str: %s\n", command_str);
    // printf("Command str len: %d\n", strlen(command_str));

    // Check if the command exist
    // and get the command name
    while (!is_command_exist) {
        c = command_str[i];

        if (c != '\n') {
            strncat(line, &c, 1);
        } else {
            trim(line);

            // printf("Line: %s\n", line);

            if (!command_exist(line)) {
                return -1;
            }

            strcpy(command->name, line);
            command_def = get_command_def(command->name);
            is_command_exist = 1;

            memset(line, 0, CHAR_SIZE);
        }

        i++;
    }

    if (!is_command_exist) {
        return -1;
    }

    // Allocate memory for params
    command->params = malloc(command_def.params_count * sizeof(command_param_t));

    // Parse params
    while (param_index < command_def.params_count) {
        c = command_str[i];

        if (c != '\n') {
            strncat(line, &c, 1);
        } else {
            // Get all last caracters of command_str
            if (param_index == command_def.params_count - 1) {
                while (i < strlen(command_str)) {
                    c = command_str[i];
                    strncat(line, &c, 1);
                    i++;
                }
            }

            trim(line);

            // printf("Line: %s\n", line);

            param_name = strtok(line, ":");
            param_value = strtok(NULL, ":");

            strcpy(command->params[param_index].name, param_name);
            strcpy(command->params[param_index].value, param_value);

            memset(line, 0, CHAR_SIZE);
            param_index++;
        }

        i++;
    }

    // for (int i = 0; i < command_def.params_count; i++) {
    //     printf("Param %d: %s %s\n", i, command->params[i].name, command->params[i].value);
    // }

    // printf("Exit parse action\n");

    return is_command_exist;
}

client_t init_client(int sock, pid_t pid, char ip[], user_t user)
{
    client_t client;

    client.sock = sock;
    client.pid = pid;
    strcpy(client.ip, ip);
    client.user = user;

    return client;
}

void add_client(int sock, pid_t pid, char ip[], user_t user)
{
    client_t client;

    if (!client_pid_exist(pid)) {
        // printf("Add client %d\n", server_data->clients_count);

        client = init_client(sock, pid, ip, user);
        server_data->clients[server_data->clients_count] = client;
        (server_data->clients_count)++;
    }
}

void remove_client(pid_t pid)
{
    for (int i = 0; i < server_data->clients_count; i++) {
        if (server_data->clients[i].pid == pid) {
            // printf("Remove client %d\n", i);
            for (int j = i; j < server_data->clients_count - 1; j++) {
                server_data->clients[j] = server_data->clients[j + 1];
            }
            (server_data->clients_count)--;
            break;
        }
    }
}

client_t get_client(pid_t pid)
{
    for (int i = 0; i < server_data->clients_count; i++) {
        if (server_data->clients[i].pid == pid) {
            return server_data->clients[i];
        }
    }

    return init_client(-1, -1, "", NOT_CONNECTED_USER);
}

int client_pid_exist(pid_t pid)
{
    for (int i = 0; i < server_data->clients_count; i++) {
        if (server_data->clients[i].pid == pid) {
            return 1;
        }
    }

    return 0;
}

int client_connected(int user_id)
{
    for (int i = 0; i < server_data->clients_count; i++) {
        if (server_data->clients[i].user.id == user_id) {
            return 1;
        }
    }

    return 0;
}

int client_banned(int user_id)
{
    for (int i = 0; i < server_data->banned_clients_count; i++) {
        if (server_data->banned_clients[i] == user_id) {
            return 1;
        }
    }

    return 0;
}
