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

volatile sig_atomic_t running = 1;
pid_t server_pid;
int clients_count;
pid_t* clients_pid;

// Send status message to the client
void send_status(int sock, status_t s, char message[]);
// Handle signals (SIGINT, SIGQUIT)
void signal_handler(int sig);
// Parse string command
int parse_command(char command_str[], command_t* command);

void add_client_pid(pid_t pid);
void remove_client_pid(pid_t pid);

int main(int argc, char* argv[])
{

    // int options; // claims the options on the command

    int options;

    // put ':' in the starting of the
    // string so that program can
    // distinguish between '?' and ':'

    while ((options = getopt(argc, argv, ":if:hvc")) != -1) {
        // getopt_long() permettrait d'avoir des options en mot complet (genre verbose, help, config, et même de décider si plus de paramètres sont nécessaire)
        switch (options) {
        case 'h':
            printf("\nUsage : build server --[options]\nLaunch the server and allows communication between client and professionnal\nOptions :\n--v, verbose     explains what is currently happening, giving more details\n-h, --help      shows help on the command\n-c --config  ");

            // Usage : gcc [options] fichier…
            // Options :
            // -pass-exit-codes         Quitter avec le plus grand code d’erreur d’une phase.
            // --help                   Afficher cette aide.
            // --target-help            Afficher les options de ligne de commande spécifiques à la cible (y compris les options de l'assembleur et de l'éditeur de liens).

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

    log_verbose = 1;

    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;
    char client_ip[CHAR_SIZE];

    int client_pid;
    int current_clients_capacity;

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
    clients_pid = (pid_t*)malloc(current_clients_capacity * sizeof(pid_t));
    server_pid = getpid();

    config = malloc(sizeof(config_t));

    // Handles options (--help, -h, --verbose, --config, -c, ...) with getopt()
    // ...

    // Load env variables
    env_load("..");

    // Initialize config
    config_load(config);

    // Set log settings
    // log_verbose = 1;  // for now delete when options are available
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

            clients_pid = (pid_t*)realloc(clients_pid, current_clients_capacity * sizeof(pid_t));
        }

        // Create a new child process
        if ((client_pid = fork()) == 0) {
            close(sock);

            client_t client;
            client.sock = sock_conn;
            client.pid = getpid();
            strcpy(client.identity, ""); // TODO
            strcpy(client.ip, client_ip);

            // For log
            strcpy(log_client_ip, client.ip);

            // Child loop
            while (1) {
                command_recv_len = read(sock_conn, command_recv, sizeof(command_recv));

                // Format received string
                command_recv[command_recv_len] = '\0';
                trim(command_recv);
                command_recv_len = strlen(command_recv);

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
                    } else if (strcmp(command.name, SEND_MESSAGE) == 0) {
                    } else if (strcmp(command.name, UPDATE_MESSAGE) == 0) {
                    } else if (strcmp(command.name, DELETE_MESSAGE) == 0) {
                    } else {
                        send_status(sock_conn, STATUS_DENIED, "Action non autorisée");
                    }
                }

                send_status(sock_conn, STATUS_OK, "Message envoyé");
            }

            exit(0);
        } else if (clients_pid[clients_count - 1] == -1) {
            perror("Fork");
            abort();
        } else {
            // Add the client pid to the list
            add_client_pid(client_pid);
            log_info("New connection with %s (%d)", client_ip, client_pid);
        }

        printf("Clients count %d\n", clients_count);
        for (int i = 0; i < clients_count; i++) {
            printf("Client %d\n", clients_pid[i]);
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
                remove_client_pid(child);
            } else if (child < 0 && errno == EINTR) {
                continue;
            } else {
                break;
            }
        }
    }

    if (sig == SIGINT || sig == SIGQUIT) {
        running = 0;
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

    while (!is_command_exist)
    {
        c = command_str[i];

        // printf("Char: %c %d\n", c, i);

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

            printf("Command name: %s\n", command->name);

            is_command_exist = 1;

            memset(line, 0, CHAR_SIZE);
        }

        i++;
    }

    while (param_index < command_def.params_count && i < strlen(command_str)) {
        c = command_str[i];

        // printf("Char: %c %d\n", c, i);

        if (c != '\n') {
            strncat(line, &c, 1);
        } else {
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

    if (strlen(line) > 0) {
        trim(line);

        printf("Line: %s\n", line);

        param_name = strtok(line, ":");
        param_value = strtok(NULL, ":");

        strcpy(command->params[param_index].name, param_name);
        strcpy(command->params[param_index].value, param_value);
    }

    // for (int i = 0; i < command_def.params_count; i++) {
    //     printf("Param %d: %s %s\n", i, command->params[i].name, command->params[i].value);
    // }

    // printf("Exit parse action\n");

    return 0;
}

void add_client_pid(pid_t pid)
{
    // Check if the client is already registered
    for (int i = 0; i < clients_count; i++) {
        if (clients_pid[i] == pid) {
            return;
        }
    }

    clients_pid[clients_count] = pid;
    clients_count++;
}

void remove_client_pid(pid_t pid)
{
    for (int i = 0; i < clients_count; i++) {
        printf("Client %d\n", clients_pid[i]);
        if (clients_pid[i] == pid) {
            for (int j = i; j < clients_count - 1; j++) {
                clients_pid[j] = clients_pid[j + 1];
            }
            clients_count--;
            break;
        }
    }
}