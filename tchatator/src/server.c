#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>

#include "database.h"
#include "log.h"
#include "protocol.h"
#include "config.h"

#include <libpq-fe.h>

int main() {
    int sock;
    int sock_conn;
    int sock_ret;
    struct sockaddr_in sock_addr;
    struct sockaddr_in sock_conn_addr;
    config_t *config;
    PGconn *conn;

    config = malloc(sizeof(config_t));

    // Handles options (--help, -h, --verbose, --config, -c, ...) with getopt()
    // ...

    // Load env variables
    env_load("..");

    // Initialize config
    config_load(config);

    // Set log settings
    log_verbose = 0;  // for now delete when options are available
    strcpy(log_file_path, config->log_file);

    // Login to the DB
    db_login(conn);
    log_info("Login to the DB");

    // Setup the socket
    sock = socket(AF_INET, SOCK_STREAM, 0);

    sock_addr.sin_addr.s_addr = INADDR_ANY;
    sock_addr.sin_family = AF_INET;
    sock_addr.sin_port = htons(config->port);

    sock_ret = bind(sock, (struct sockaddr*) &sock_addr, sizeof(sock_addr));

    if (sock_ret < 0) {
        perror("Cannot bind the socket");
        exit(1);
    }

    sock_ret = listen(sock, 1);

    if (sock_ret) {
        perror("Cannot listen");
        exit(1);
    }

    // Free memory
    free(config);

    return EXIT_SUCCESS;
}