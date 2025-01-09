#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include <signal.h>

#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/types.h>

#include "config.h"
#include "database.h"
#include "protocol.h"
#include "types.h"
#include "utils.h"

#define SERVER_PORT 4242
#define BUFFER_SIZE 1024

PGconn* conn;
volatile sig_atomic_t running = 1;
status_t* response;
int sock;

void input(char* output)
{
    scanf("%s", output);
    getchar();
}

void display_choice_login()
{
    printf("[1] - connection client\n");
    printf("[2] - connection pro\n");
    printf("[3] - connection admin\n");
    printf("[4] - EXIT\n\n");
}

void connection_pro()
{
    char mail[CHAR_SIZE], token[API_TOKEN_SIZE] = { 0 };
    printf("Enter your email: ");
    input(mail);

    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "brehat@gmail.com");
    }

    printf("Email entered: '%s'\n", mail);

    char* temp_token = get_token_by_email(conn, mail);
    if (temp_token == NULL) {
        printf("User not found\n");
        return;
    }

    strcpy(token, temp_token);
    printf("Token: %s\n", token);
    send_login(sock, token);
    printf("Connected\n");
}

void connection_client()
{
    char mail[CHAR_SIZE], token[API_TOKEN_SIZE] = { 0 };
    printf("Enter your email: ");
    input(mail);

    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "DavidJohnson151@gmail.com");
    }

    printf("Email entered: '%s'\n", mail);
    char* temp_token = get_token_by_email(conn, mail);
    if (temp_token == NULL) {
        printf("User not found\n");
        return;
    }

    strcpy(token, temp_token);
    printf("Token: %s\n", token);
    send_login(sock, token);
    printf("Connected\n");
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

void menu_message(int sock)
{
    command_t command = create_command(SEND_MESSAGE);
    char token[64], message[BUFFER_SIZE], message_len[10], buffer[BUFFER_SIZE];

    printf("Enter your token: ");
    input(token);

    printf("Enter your message: ");
    input(message);

    response = send_message(sock, token, message, 1);
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
    // PGconn* conn;

    config_t* config;
    config = malloc(sizeof(config_t));
    config_load(config);

    env_load("..");

    db_login(&conn);

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

        if (response != NULL) {
            printf("\nResponse: %d %s\n", response->code, response->message);
        }

        printf("Enter your choice: ");
        scanf("%d", &choice);
        getchar();
        switch (choice) {
        case 1:
            connection_client();
            break;
        case 2:
            connection_pro();
            break;
        case 3:
            send_login(sock, config->admin_api_token);
            printf("Admin connected\n");
        case 4:
            disconnect(sock);
            return EXIT_SUCCESS;
        case 10:
            response = send_login(sock, "06b8df93cd94c728ef92ad4d8bd8f907513e95f4c10b2858112913b1d86cfae5");
            break;
        case 11:
            response = send_message(sock, "coucousupertoken", "monmessage\nrelou\n", 3);
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