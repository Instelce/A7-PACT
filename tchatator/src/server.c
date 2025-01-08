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

int main(int argc, char* argv[])
{
    int options;

    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;
    char client_ip[CHAR_SIZE];

    int client_pid;
    int current_clients_capacity;

    char action_recv[1000];
    int action_recv_len;
    command_t action;
    int action_parsed;

    config_t* config;
    PGconn* conn;

    // Register signal
    signal(SIGINT, signal_handler);
    signal(SIGQUIT, signal_handler);

    clients_count = 0;
    current_clients_capacity = CLIENT_CAPACITY_INCR;
    clients_pid = (pid_t*)malloc(current_clients_capacity * sizeof(pid_t));
    server_pid = getpid();

    log_verbose = 0;

    config = malloc(sizeof(config_t));

    // Handles options (--help, -h, --verbose, --config, -c, ...) with getopt()
    while ((options = getopt(argc, argv, ":if:hvc")) != -1) {
        switch (options) {
        case 'h':
            printf("Tchatator\n\nLaunch the server and allows communication between client and professionnal\n\nUsage : tachatator [options]\nOptions :\n  -v, --verbose \texplains what is currently happening, giving more details\n  -h, --help \tshows help on the command\n  -c --config  ");

            exit(0);
        case 'v':
            // printf("option verbose : ON\n");
            log_verbose = 1;
            break;
        case 'c':
            // printf("option config: %c\n", options);
            break;
        }
    }

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
        log_info("New connection with %s", client_ip);

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
            strcpy(client.identity, ""); // TODO
            strcpy(client.ip, client_ip);

            // For log
            strcpy(log_client_ip, client.ip);

            // Child loop
            while (1) {
                action_recv_len = read(sock_conn, action_recv, sizeof(action_recv));

                // Format received string
                action_recv[action_recv_len] = '\0';
                trim(action_recv);
                action_recv_len = strlen(action_recv);

                log_info("Action received : %s", action_recv);

                action_parsed = parse_command(action_recv, &action);

                if (action_parsed == -1) {
                    send_status(sock_conn, STATUS_MIS_FORMAT, "Message mal formaté");
                } else {
                    // Parse commands
                    // ...

                }
            }
        } else if (clients_pid[clients_count - 1] == -1) {
            perror("Fork");
            abort();
        } else {
            int already_registered = 0;
            for (int i = 0; i < clients_count; i++) {
                if (clients_pid[i] == client_pid) {
                    already_registered = 1;
                    break;
                }
            }
            if (!already_registered) {
                clients_pid[clients_count] = client_pid;
                // printf("Register child (%d)\n", clients_count);
                clients_count++;
            } else {
                // printf("child already exists\n");
            }
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

    running = 0;

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

            exit(0);
        }
    }

    if (sig == SIGQUIT) {
        if (server_pid != self) {
            // printf("Child %d kill itself\n", (int)self);
            _exit(0);
        }
    }
}