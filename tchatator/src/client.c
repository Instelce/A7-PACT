#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/types.h>

#include "protocol.h"
#include "types.h"

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

status_t *response;

void display_menu()
{
    printf("[1] Send message\n");
    printf("[2] Connection\n");
    printf("[3] Exit\n");
}

void input(char *output)
{
    fgets(output, sizeof(output), stdin);
    output[strcspn(output, "\n")] = '\0';
}

void menu_message(int sock)
{
    command_t command = create_command(SEND_MESSAGE);
    char token[64], message[BUFFER_SIZE], message_len[10], buffer[BUFFER_SIZE];

    printf("Enter your token: ");
    input(token);

    printf("Enter your message: ");
    input(message);

    send_message(sock, token, message);
}

void menu_login(int sock)
{
    char api_token[CHAR_SIZE];

    printf("Enter your api token: ");
    input(api_token);

    send_login(sock, api_token);
}

void disconnect(int sock)
{
    write(sock, "DISCONNECTED", 12);
    close(sock);
}

int main()
{
    int sock;
    struct sockaddr_in server_addr;
    int choice;
    char token[64] = ""; 

    if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        perror("Cannot create socket");
        exit(EXIT_FAILURE);
    }

    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(SERVER_PORT);
    server_addr.sin_addr.s_addr = INADDR_ANY;

    // Connect to server
    if (connect(sock, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        perror("Cannot connect to the server");
        exit(EXIT_FAILURE);
    }

    printf("Connected to server\n");

    // Main loop
    while (1) {
        display_menu();

        scanf("%d", &choice);
        getchar();

        switch (choice) {
        case 1:
            menu_message(sock);
            break;
        case 2:
            menu_login(sock);
            break;
        case 3:
            disconnect(sock);
            return EXIT_SUCCESS;
        default:
            printf("Invalid choice, please try again.\n");
            break;
        }
    }

    close(sock);
    return EXIT_SUCCESS;
}
