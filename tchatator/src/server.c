#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>

#include <signal.h>

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <libpq-fe.h>

#include "database.h"
#include "log.h"
#include "protocol.h"
#include "config.h"
#include "utils.h"


volatile sig_atomic_t running = 1;

void send_status(int sock, status_t s, char message[]);
void signal_handler(int sig);

int main() {
    int sock;
    int sock_conn;
    int sock_ret;
    int sock_conn_addr_size;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;
    char client_ip[CHAR_SIZE];

    char action_recv[1000];
    int action_recv_len;
    command_t action;
    int action_parsed;

    config_t *config;
    PGconn *conn;

    // Register signal
    signal(SIGINT, signal_handler);

    config = malloc(sizeof(config_t));

    // Handles options (--help, -h, --verbose, --config, -c, ...) with getopt()
    // ...
    int options; // claims the options on the command
      
    // put ':' in the starting of the 
    // string so that program can  
    //distinguish between '?' and ':'  
    while((options = getopt(argc, argv, “:if:hvc”)) != -1)  
    {  
        switch(options)  
        {  
            case ‘i’:  
            case ‘l’:  
            case ‘h’:  
                printf(“option help : %c\n”, opt);


                // Usage : gcc [options] fichier…
                // Options :
                // -pass-exit-codes         Quitter avec le plus grand code d’erreur d’une phase.
                // --help                   Afficher cette aide.
                // --target-help            Afficher les options de ligne de commande spécifiques à la cible (y compris les options de l'assembleur et de l'éditeur de liens).



                break;  
            case ‘v’:  
                printf(“option verbose : %c\n”, opt);  
                break;  
            case ‘c’:  
                printf(“option config: %c\n”, opt); 
                break;  
        }  
    }  

    // Load env variables
    env_load("..");

    // Initialize config
    config_load(config);

    // Set log settings
    log_verbose = 1;  // for now delete when options are available
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

    sock_ret = bind(sock, (struct sockaddr*) &sock_addr, sizeof(sock_addr));

    if (sock_ret < 0) {
        perror("Cannot bind the socket");
        exit(1);
    }

    log_info("Listening on address \"%s\", port %d", inet_ntoa(sock_addr.sin_addr), config->port);
    log_info("Ready to accept connections");

    sock_ret = listen(sock, 1);

    if (sock_ret) {
        perror("Cannot listen connections");
        exit(1);
    }

    sock_conn_addr_size = sizeof(sock_conn_addr);
    sock_conn = accept(sock, (struct sockaddr*) &sock_conn_addr, (socklen_t *) &sock_conn_addr_size);

    if (sock_conn < 0) {
        perror("Cannot accept connection");
        exit(1);
    }

    // Retrieve the client ip
    inet_ntop(AF_INET, &sock_conn_addr.sin_addr, client_ip, INET_ADDRSTRLEN);
    log_info("New connection with %s", client_ip);

    write(sock_conn, "Hello\n", 6);

    while (running)
    {
        action_recv_len = read(sock_conn, action_recv, sizeof(action_recv));

        // Format received string
        action_recv[action_recv_len] = '\0';
        trim(action_recv);
        action_recv_len = strlen(action_recv);

        log_info("Action received : %s %d", action_recv, action_recv_len);

        action_parsed = parse_command(action_recv, &action);

        if (action_parsed == -1) {
            send_status(sock_conn, STATUS_MIS_FORMAT, "Message mal formaté");
        } else {
            
        }
    }

    log_info("Tchatator was shut down");

    close(sock_conn);
    close(sock);

    // Free memory
    free(config);

    return EXIT_SUCCESS;
}

void send_status(int sock, status_t s, char message[]) {
    char complete_message[CHAR_SIZE];

    sprintf(complete_message, "%s: %s\n", format_status(s), message);

    write(sock, complete_message, strlen(complete_message));
}

void signal_handler(int sig) {
    if (sig == SIGINT) {
        running = 0;
    }
}