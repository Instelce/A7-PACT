#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <signal.h>

#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/types.h>

#include "config.h"
#include "protocol.h"
#include "types.h"

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

volatile sig_atomic_t running = 1;
status_t* response;
int sock;

void display_choice_login()
{
    printf("[1] - connection client\n");
    printf("[2] - connection pro\n");
    printf("[3] - connection admin\n");
    printf("[4] - EXIT\n\n");
}

void connection()
{
    char mail[CHAR_SIZE], token[API_TOKEN_SIZE];
    printf("enter your email :");
    input(mail);
}

void display_menu_client()
{
    printf("[1] - send a message\n");
    printf("[2] - display unread messages\n");
    printf("[3] - modify a message\n");
    printf("[4] - delete a message\n");
    printf("[5] - display messages history\n");
    printf("[6] - EXIT\n\n");
}

void display_menu_pro()
{
    printf("[1] - send a message\n");
    printf("[2] - display unread messages\n");
    printf("[3] - modify a message\n");
    printf("[4] - delete a message\n");
    printf("[5] - display messages history\n");
    printf("[6] - EXIT\n\n");
}

void display_menu_admin()
{
    printf("[1] - send message\n");
    printf("[2] - block message\n");
    printf("[3] - ban user\n");
    printf("[4] - EXIT\n\n");
}
void input(char* output)
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

void disconnect(int sock)
{
    write(sock, "DISCONNECTED", 12);
    close(sock);
}

void signal_handler(int sig)
{
    if (sig == SIGINT) {
        disconnect(sock);
        running = 0;
        exit(EXIT_SUCCESS);
    }
}

int main()
{
    int sock_ret;
    struct sockaddr_in server_addr;
    int choice;
    PGconn* conn;

    config_t* config;
    config = malloc(sizeof(config_t));
    config_load(config);

    env_load("..");

    db_login(conn);

    signal(SIGINT, signal_handler);

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
    while (running) {

        display_choice_login();

        printf("Enter your choice: ");
        scanf("%d", &choice);
        getchar();
        switch (choice) {
        case 1:
            connection();
            break;
        case 2:
            connection();
            break;
        case 3:
            send_login(sock, config->admin_api_token);
            printf("Admin connected\n");
        case 4:
            disconnect(sock);
            return EXIT_SUCCESS;
        case 10:
            send_message(sock, "coucousupertoken", "monmessage\nrelou\n");
            break;
        default:
            printf("Invalid choice, please try again.\n");
            break;
        }
    }

    printf("Bye\n");

    close(sock);
    return EXIT_SUCCESS;
}