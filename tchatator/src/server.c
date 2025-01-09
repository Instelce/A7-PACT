//   _______   _           _        _
//  |__   __| | |         | |      | |
//     | | ___| |__   __ _| |_ __ _| |_ ___  _ __
//     | |/ __| '_ \ / _` | __/ _` | __/ _ \| '__|
//     | | (__| | | | (_| | || (_| | || (_) | |
//     |_|\___|_| |_|\__,_|\__\__,_|\__\___/|_|
//

#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <sys/ipc.h>
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

#define CLIENT_CAPACITY_INCR 10

typedef struct
{
    int sock;
    pid_t pid; // child pid
    char ip[CHAR_SIZE];
    user_t user;
} client_t;

volatile sig_atomic_t running = 1;

pid_t server_pid;

client_t* clients;
int* clients_count;
int shmid_clients;
int shmid_clients_count;

// Send status message to the client
void send_status(int sock, status_t s, char message[]);
// Handle signals (SIGINT, SIGQUIT)
void signal_handler(int sig);
// Parse string command
int parse_command(char command_str[], command_t* command);

client_t init_client(int sock, pid_t pid, char ip[], user_t user);
void add_client(int sock, pid_t pid, char ip[], user_t user);
void remove_client(pid_t pid);
client_t get_client(pid_t pid);
int client_exist(pid_t pid);
int client_connected(int user_id);

int main(int argc, char* argv[])
{
    int options;

    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;

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
    PGconn* conn;

    // Register signal
    signal(SIGINT, signal_handler);
    signal(SIGQUIT, signal_handler);
    signal(SIGCHLD, signal_handler);

    server_pid = getpid();
    current_clients_max_capacity = CLIENT_CAPACITY_INCR;

    key_t key = ftok(".", 65);
    shmid_clients = shmget(key, current_clients_max_capacity * sizeof(client_t), 0666 | IPC_CREAT);
    clients = (client_t*)shmat(shmid_clients, (void*)0, 0);

    key_t key_count = ftok(".", 66);
    shmid_clients_count = shmget(key_count, sizeof(int), 0666 | IPC_CREAT);
    clients_count = (int*)shmat(shmid_clients_count, (void*)0, 0);
    *clients_count = 0;

    log_verbose = 0;
    config = malloc(sizeof(config_t));

    // Handles options (--help, -h, --verbose, --config, -c, ...) with getopt()
    while ((options = getopt(argc, argv, ":if:hvc")) != -1) {
        switch (options) {
        case 'h':
            printf("\nUsage : server --[options]\n");
            printf("Launch the server and allows communication between client and professional\n");
            printf("Options :\n");
            printf("--v, verbose     explains what is currently happening, giving more details\n");
            printf("-h, --help       shows help on the command\n");
            printf("-c, --config     specify the configuration file\n");

            break;
        case 'v':
            printf("option verbose : ON\n");
            log_verbose = 1;
            break;
        case 'c':
            printf("option config: %c\n", options);
            break;
        }
    }

    log_verbose = 1; // only for dev

    // Load env variables
    env_load("..");

    // Initialize config
    config_load(config);

    // Set log settings
    strcpy(log_file_path, config->log_file);

    // Login to the DB
    db_login(&conn);

    // test db
    // user_t user;
    // user_t user2;
    // printf("Get user\n");
    // db_get_user(conn, &user, 2);
    // printf("%s %s %d\n", user.email, user.api_token, user.id);
    // db_get_user_by_email(conn, &user2, "rouevictor@gmail.com");
    // printf("%s %d\n", user2.email, user2.id);

    log_info("Starting Tchatator");

    // Setup the socket
    sock = socket(AF_INET, SOCK_STREAM, 0);

    // Remove the 'Address already in use' pb
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

    while (running) {
        sock_conn = accept(sock, (struct sockaddr*)&sock_conn_addr, (socklen_t*)&sock_conn_addr_size);

        if (sock_conn < 0) {
            perror("Cannot accept connection");
            exit(1);
        }

        if (!running) {
            break;
        }

        // Retrieve the client ip and port
        client_port = ntohs(sock_conn_addr.sin_port);
        inet_ntop(AF_INET, &sock_conn_addr.sin_addr, client_ip, INET_ADDRSTRLEN);
        sprintf(client_ip + strlen(client_ip), ":%d", client_port); // add port to ip

        // Increase the clients capacity if needed
        if (*clients_count >= current_clients_max_capacity && server_pid == getpid()) {
            log_info("Increase client capacity");
            current_clients_max_capacity += CLIENT_CAPACITY_INCR;

            shmdt(clients);
            shmid_clients = shmget(key, current_clients_max_capacity * sizeof(client_t), 0666 | IPC_CREAT);
            clients = (client_t*)shmat(shmid_clients, (void*)0, 0);
        }

        // Create a new child process
        if ((client_pid = fork()) == 0) {
            close(sock);

            int client_id = *clients_count;
            char api_token[API_TOKEN_SIZE];
            int client_login = 0;

            // For log
            strcpy(log_client_ip, client_ip);

            // Child loop
            while (1) {
                // printf("C Clients count %d\n", *clients_count);
                // for (int i = 0; i < *clients_count; i++) {
                //     printf("C Client %d %s\n", clients[i].ip, clients[i].user.email);
                // }

                // Wait for a command
                memset(command_recv, 0, sizeof(command_recv));
                command_recv_len = recv(sock_conn, command_recv, sizeof(command_recv), 0);

                // printf("Command received: %s\n", command_recv);

                if (command_recv_len < 0) {
                    perror("Error reading from socket");
                    exit(1);
                }

                // Disconnect the client
                if (strcmp(command_recv, "DISCONNECTED") == 0) {
                    break;
                }

                command_parsed = parse_command(command_recv, &command);

                if (command_parsed == -1) {
                    send_status(sock_conn, STATUS_MIS_FORMAT, "Message mal formaté");
                    log_info("Invalid action received");
                    continue;
                } else {
                    log_info("Action received [%s]", command.name);

                    // Handle the login command
                    if (strcmp(command.name, LOGIN) == 0 && !client_login) {
                        strcpy(api_token, get_command_param_value(command, "api-token"));

                        user_t user;
                        int user_found = db_get_user_by_api_token(conn, &user, api_token);

                        if (!user_found) {
                            send_status(sock_conn, STATUS_DENIED, "Accès refusé");
                        } else {
                            // For log
                            strcpy(log_client_identity, user.email);

                            client_login = 1;
                            clients[client_id].user = user;

                            log_info("Client (%d) logged in", getpid());
                            send_status(sock_conn, STATUS_OK, "Accès autorisé");
                            continue;
                        }
                    }

                    // Handle all commands that need to be logged in
                    if (client_login) {
                        if (strcmp(command.name, SEND_MESSAGE) == 0) {
                            // Check if the token is valid
                            if (strcmp(get_command_param_value(command, "token"), clients[client_id].user.api_token) != 0) {
                                send_status(sock_conn, STATUS_UNAUTHORIZED, ": Client non identifié");
                                continue;
                            }

                            message_t message;

                            message = init_message(
                                clients[client_id].user.id,
                                atoi(get_command_param_value(command, "receiver-id")),
                                get_command_param_value(command, "content"));

                            db_create_message(conn, &message);

                            log_info("Message (%d) send from %d to %d", message.id, clients[client_id].user.id, message.receiver_id);

                            send_status(sock_conn, STATUS_OK, "Message bien reçu et traité");
                        } else if (strcmp(command.name, UPDATE_MESSAGE) == 0) {
                        } else if (strcmp(command.name, DELETE_MESSAGE) == 0) {
                        }
                    } else {
                        send_status(sock_conn, STATUS_DENIED, "Action non autorisée");
                    }
                }
            }

            exit(0);
        } else if (client_pid == -1) {
            perror("Fork");
            abort();
        } else {
            // Add the client to the list
            add_client(sock_conn, client_pid, client_ip, NOT_CONNECTED_USER);
            log_info("New connection with %s (%d)", client_ip, client_pid);
        }

        // printf("S Clients count %d\n", *clients_count);
        // for (int i = 0; i < *clients_count; i++) {
        //     printf("S Client %d %s\n", clients[i].ip, clients[i].user.email);
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

void send_status(int sock, status_t s, char message[])
{
    char complete_message[CHAR_SIZE];

    sprintf(complete_message, "%s: %s\n", format_status(s), message);

    send(sock, complete_message, strlen(complete_message), 0);
}

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
        shmdt(clients);
        shmctl(shmid_clients, IPC_RMID, NULL);
        shmdt(clients_count);
        shmctl(shmid_clients_count, IPC_RMID, NULL);
    }

    if (sig == SIGINT && server_pid == self) {
        if (*clients_count > 0) {
            kill(0, SIGQUIT);

            for (int i = 0; i < *clients_count; i++) {
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

            if (!command_exist(line)) {
                return -1;
            }

            strcpy(command->name, line);

            command_def = get_command_def(command->name);

            command->params = malloc(command_def.params_count * sizeof(command_param_t));

            // printf("Command name: %s\n", command->name);

            is_command_exist = 1;

            memset(line, 0, CHAR_SIZE);
        }

        i++;
    }

    if (!is_command_exist) {
        return -1;
    }

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

    return 0;
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

    if (!client_exist(pid)) {
        printf("Add client %d\n", *clients_count);

        client = init_client(sock, pid, ip, user);
        clients[*clients_count] = client;
        (*clients_count)++;
    }
}

void remove_client(pid_t pid)
{
    for (int i = 0; i < *clients_count; i++) {
        if (clients[i].pid == pid) {
            printf("Remove client %d\n", i);
            for (int j = i; j < *clients_count - 1; j++) {
                clients[j] = clients[j + 1];
            }
            (*clients_count)--;
            break;
        }
    }
}

client_t get_client(pid_t pid)
{
    for (int i = 0; i < *clients_count; i++) {
        if (clients[i].pid == pid) {
            return clients[i];
        }
    }

    return init_client(-1, -1, "", NOT_CONNECTED_USER);
}

int client_exist(pid_t pid)
{
    for (int i = 0; i < *clients_count; i++) {
        if (clients[i].pid == pid) {
            return 1;
        }
    }

    return 0;
}

int client_connected(int user_id)
{
    for (int i = 0; i < *clients_count; i++) {
        if (clients[i].user.id == user_id) {
            return 1;
        }
    }

    return 0;
}