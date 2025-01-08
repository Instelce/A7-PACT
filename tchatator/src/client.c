#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <arpa/inet.h>

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

// Display menu
void display_menu() {
    printf("\n====== MENU ======\n");
    printf("[1] Send Message\n");
    printf("[2] Login\n");
    printf("[3] Exit\n");
    printf("==================\n");
    printf("Enter your choice: ");
}

// Handle login
void handle_login(int sock, char *token) {
    char email[BUFFER_SIZE], password[BUFFER_SIZE], buffer[BUFFER_SIZE];

    printf("Enter your email: ");
    fgets(email, sizeof(email), stdin);
    email[strcspn(email, "\n")] = '\0'; // Remove newline

    printf("Enter your password: ");
    fgets(password, sizeof(password), stdin);
    password[strcspn(password, "\n")] = '\0'; // Remove newline

    // Build LOGIN command
    snprintf(buffer, sizeof(buffer), "LOGIN\nmail:%s\npassword:%s\n", email, password);

    // Send command to server
    if (send(sock, buffer, strlen(buffer), 0) < 0) {
        perror("Error sending login");
        return;
    }

    // Read server response
    memset(buffer, 0, sizeof(buffer));
    if (recv(sock, buffer, sizeof(buffer) - 1, 0) < 0) {
        perror("Error receiving response");
        return;
    }

    // Display response
    printf("Server response: %s\n", buffer);

    // Extract token on success
    if (strncmp(buffer, "200/OK", 6) == 0) {
        sscanf(buffer, "200/OK\ntoken:%63s\n", token);
        printf("Logged in successfully! Token: %s\n", token);
    } else {
        printf("Login failed! Please check your credentials.\n");
    }
}

void send_message(int sock, const char *token) {
    char message[BUFFER_SIZE], buffer[BUFFER_SIZE];


    // if (strlen(token) == 0) {
    //     printf("You need to login first!\n");
    //     return;
    // }
    printf("Enter your message: ");
    fgets(message, sizeof(message), stdin);
    message[strcspn(message, "\n")] = '\0'; // Remove newline

    // Build MSG command
    snprintf(buffer, sizeof(buffer), "MSG\ntoken:%s\nmessage-length:%lu\ncontent:%s\n", 
             token, strlen(message), message);

    // Send command to server
    if (send(sock, buffer, strlen(buffer), 0) < 0) {
        perror("Error sending message");
        return;
    }

    // Read server response
    memset(buffer, 0, sizeof(buffer));
    if (recv(sock, buffer, sizeof(buffer) - 1, 0) < 0) {
        perror("Error receiving response");
        return;
    }

    printf("Server response: %s\n", buffer);
}

int main() {
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
            send_message(sock, token);
            break;
        case 2:
            handle_login(sock, token);
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
