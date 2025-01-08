#include "types.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/types.h>

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

void display_menu()
{
    printf("[1] Send message\n");
    printf("[2] Connection\n");
    printf("[3] Exit\n");
    printf("Choose an option: ");
}

void send_message(int sock)
{
    char token[64], message[BUFFER_SIZE], buffer[BUFFER_SIZE];

    printf("Enter your token: ");
    fgets(token, sizeof(token), stdin);
    token[strcspn(token, "\n")] = '\0';

    printf("Enter your message: ");
    fgets(message, sizeof(message), stdin);
    message[strcspn(message, "\n")] = '\0';

    // Create the MSG command
    snprintf(buffer, sizeof(buffer), "MSG\ntoken:%s\nmessage-lenght:%lu\ncontent:%s\n", token, strlen(message), message);

    // Send the command to the server
    if (send(sock, buffer, strlen(buffer), 0) < 0) {
        perror("Error sending message");
        return;
    }

    // Receive response from the server
    memset(buffer, 0, sizeof(buffer));
    if (recv(sock, buffer, sizeof(buffer) - 1, 0) < 0) {
        perror("Error receiving response");
        return;
    }

    printf("Server response: %s\n", buffer);
}
void handle_login(int sock)
{
    char mail[CHAR_SIZE], password[CHAR_SIZE], buffer[BUFFER_SIZE];

    printf("Enter your mail: ");
    fgets(mail, sizeof(mail), stdin);
    mail[strcspn(mail, "\n")] = '\0';

    printf("Enter your password: ");
    fgets(password, sizeof(password), stdin);
    password[strcspn(password, "\n")] = '\0';

    // Create the LOGIN command
    snprintf(buffer, sizeof(buffer), "LOGIN\nmail:%s\npassword:%s\n", mail, password);

    // Send the command to the server
    if (send(sock, buffer, strlen(buffer), 0) < 0) {
        perror("Error sending login");
        return;
    }

    // Receive response from the server
    memset(buffer, 0, sizeof(buffer));
    if (recv(sock, buffer, sizeof(buffer) - 1, 0) < 0) {
        perror("Error receiving response");
        return;
    }

    printf("Server response: %s\n", buffer);
}
int main()
{
    int sock;
    int sock_ret;
    struct sockaddr_in server_addr;
    int choice;

    // Create socket
    if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        perror("Cannot create socket");
        exit(EXIT_FAILURE);
    }

    // Config server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(SERVER_PORT);
    server_addr.sin_addr.s_addr = INADDR_ANY;

    if ((sock_ret = connect(sock, (struct sockaddr*)&server_addr, sizeof(server_addr))) < 0) {
        perror("Cannot connect to the server socket");
        exit(EXIT_FAILURE);
    }

    printf("Connected to server\n");

    // Main menu loop
    while (1) {
        display_menu();

        printf("Enter your choice: ");
        scanf("%d", &choice);
        getchar(); // consume newline

        switch (choice) {
        case 1:
            send_message(sock);
            break;
        case 2:
            handle_login(sock);
            break;
        case 3:
            printf("Exiting...\n");
            close(sock);
            return EXIT_SUCCESS;
        default:
            printf("Invalid choice, please try again.\n");
            break;
        }
    }

    close(sock);
    return EXIT_SUCCESS;
}