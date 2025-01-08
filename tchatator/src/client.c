#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

int main() {
    int sock;
    int sock_ret;
    struct sockaddr_in server_addr;
    

    // Create socket
    if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        perror("Cannot create socket");
        exit(EXIT_FAILURE);
    }

    // Config server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(SERVER_PORT);
    server_addr.sin_addr.s_addr = INADDR_ANY;

    if ((sock_ret = connect(sock, (struct sockaddr *) &server_addr, sizeof(server_addr))) < 0) {
        perror("Cannot connect to the server socket");
        exit(EXIT_FAILURE);
    }

    printf("Connected to server\n");

    while (1)
    {
        
    }

    close(sock);

    return EXIT_SUCCESS;
}
