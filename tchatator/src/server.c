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
    pid_t pid;
    char ip[CHAR_SIZE];
    user_t user;
} client_t;

volatile sig_atomic_t running = 1;
pid_t server_pid;
int clients_count;
int shmid;
client_t* clients;

// Send status message to the client
void send_status(int sock, status_t s, char message[]);
// Handle signals (SIGINT, SIGQUIT)
void signal_handler(int sig);
// Parse string command
int parse_command(char command_str[], command_t* command);

client_t init_client(int sock, pid_t pid, char ip[], user_t user);
void add_client(int sock, pid_t pid, char ip[], user_t user);
void remove_client(pid_t pid);

int main(int argc, char* argv[])
{
    int options;

    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;

    int client_pid;
    int current_clients_capacity;
    char client_ip[CHAR_SIZE];

    // Current command stuff
    char command_recv[1000];
    int command_recv_len;
    command_t command;
    int command_parsed;

    config_t* config;
    PGconn* conn;

    // Register signal
    signal(SIGINT, signal_handler);
    signal(SIGQUIT, signal_handler);
    signal(SIGCHLD, signal_handler);

    clients_count = 0;
    current_clients_capacity = CLIENT_CAPACITY_INCR;
    // clients = (client_t*)malloc(current_clients_capacity * sizeof(client_t));
    server_pid = getpid();

    key_t key = ftok("shmfile", 65);
    shmid = shmget(key, current_clients_capacity * sizeof(client_t), 0666 | IPC_CREAT);
    clients = (client_t*)shmat(shmid, (void*)0, 0);

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

    log_verbose = 1;  // only for dev

    // Load env variables
    env_load("..");

    // Initialize config
    config_load(config);

    // Set log settings
    strcpy(log_file_path, config->log_file);

    // Login to the DB
    db_login(conn);

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

        // Retrieve the client ip
        inet_ntop(AF_INET, &sock_conn_addr.sin_addr, client_ip, INET_ADDRSTRLEN);
        sprintf(client_ip + strlen(client_ip), ":%d", ntohs(sock_conn_addr.sin_port));

        // Increase the clients capacity if needed
        if (clients_count >= current_clients_capacity) {
            log_info("Increase client capacity");
            current_clients_capacity += CLIENT_CAPACITY_INCR;

            clients = (client_t*)realloc(clients, current_clients_capacity * sizeof(client_t));
        }

        // Create a new child process
        if ((client_pid = fork()) == 0) {
            close(sock);

            int client_login = 0;
            client_t client = init_client(sock_conn, getpid(), client_ip, NOT_CONNECTED_USER);

            // For log
            strcpy(log_client_ip, client_ip);

            // Child loop
            while (1) {
                memset(command_recv, 0, sizeof(command_recv));
                command_recv_len = read(sock_conn, command_recv, sizeof(command_recv));

                // Disconnect the client
                if (strcmp(command_recv, "DISCONNECTED") == 0) {
                    break;
                }

                command_parsed = parse_command(command_recv, &command);

                if (command_parsed == -1) {
                    send_status(sock_conn, STATUS_MIS_FORMAT, "Message mal formaté");
                    log_info("Invalid action received : %s", command_recv);
                } else {
                    log_info("Action received : %s", command.name);

                    // Handle the action
                    if (strcmp(command.name, LOGIN) == 0) {
                        // For log
                        strcpy(log_client_identity, get_command_param_value(command, "api-token"));

                        send_status(sock_conn, STATUS_OK, "Connexion réussie");
                    } else if (strcmp(command.name, SEND_MESSAGE) == 0) {

                        send_status(sock_conn, STATUS_OK, "Message envoyé");
                    } else if (strcmp(command.name, UPDATE_MESSAGE) == 0) {
                    } else if (strcmp(command.name, DELETE_MESSAGE) == 0) {
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
            // Add the client pid to the list
            add_client(sock_conn, client_pid, client_ip, NOT_CONNECTED_USER);
            log_info("New connection with %s (%d)", client_ip, client_pid);
        }

        printf("Clients count %d\n", clients_count);
        for (int i = 0; i < clients_count; i++) {
            printf("Client %d\n", clients[i]);
        }
    }

    close(sock_conn);
    close(sock);

    // Free memory
    free(config);

    log_info("Tchatator was shut down");

    return EXIT_SUCCESS;
}

void send_status(int sock, status_t s, char message[])
{
    char complete_message[CHAR_SIZE];

    sprintf(complete_message, "%s: %s\n", format_status(s), message);

    write(sock, complete_message, strlen(complete_message));
}

void signal_handler(int sig)
{
    pid_t self = getpid();

    if (sig == SIGCHLD) {
        int status;
        pid_t child;

        for (;;) {
            child = waitpid(0, &status, WNOHANG);
            if (child > 0 && WIFEXITED(status) && WEXITSTATUS(status) == 0) {
                log_info("Child %d succesully quit", (int)child);
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
        shmctl(shmid, IPC_RMID, NULL);
    }

    if (sig == SIGINT && server_pid == self) {
        if (clients_count > 0) {
            kill(0, SIGQUIT);

            for (int i = 0; i < clients_count; i++) {
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

    printf("Command str: %s\n", command_str);
    printf("Command str len: %d\n", strlen(command_str));

    // Check if the command exist
    // and get the command name
    while (!is_command_exist)
    {
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

            printf("Line: %s\n", line);

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

client_t init_client(int sock, pid_t pid, char ip[], user_t user) {
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

    // Check if the client is already registered
    for (int i = 0; i < clients_count; i++) {
        if (clients[i].pid == pid) {
            return;
        }
    }

    client = init_client(sock, pid, ip, user);
    clients[clients_count] = client;
    clients_count++;
}

void remove_client(pid_t pid)
{
    for (int i = 0; i < clients_count; i++) {
        printf("Client %d\n", clients[i].user.username);
        if (clients[i].pid == pid) {
            for (int j = i; j < clients_count - 1; j++) {
                clients[j] = clients[j + 1];
            }
            clients_count--;
            break;
        }
    }
}