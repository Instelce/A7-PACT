#include <fcntl.h>
#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <termio.h>
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
#define LINE_WIDTH 80

typedef struct {
    char name[CHAR_SIZE];
    int disabled;
    void (*action)();
} menu_action_t;

typedef struct {
    char name[CHAR_SIZE];
    menu_action_t* actions;
    int actions_count;
} menu_t;

PGconn* conn;
volatile sig_atomic_t running = 1;
response_status_t* response;
int sock;
user_t connected_user;
user_list_t pros;
user_list_t clients;

// Display a menu and return the index of the selected action
int display_menu(menu_t menu);

// Create a menu, with a name and a list of actions (name and function)
// The last action should have a NULL name
// Example:
// create_menu(menu, "Main menu", "Action 1", action1, "Action 2", action2, NULL);
void create_menu(menu_t* menu, char name[], ...);

void menu_add_action(menu_t* menu, char name[], void (*action)());

void input(char* output)
{
    scanf("%s", output);
    getchar();
}

void connection_pro()
{
    char mail[CHAR_SIZE], token[API_TOKEN_SIZE];
    printf("\n   Enter your email: ");
    input(mail);

    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "brehat@gmail.com");
    }

    printf("Email entered: '%s'\n", mail);

    int user_found = db_get_user_by_email(conn, &connected_user, mail);

    if (!user_found) {
        printf("User not found\n");
        return;
    }
    printf("Token: %s\n", connected_user.api_token);
    response = send_login(sock, connected_user.api_token);
}

void connection_client()
{
    char mail[CHAR_SIZE], token[API_TOKEN_SIZE];

    printf("\n   Enter your email: ");
    input(mail);

    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "eliaz.chesnel@outlook.fr");
    }

    printf("Email entered: '%s'\n", mail);
    int user_found = db_get_user_by_email(conn, &connected_user, mail);

    if (!user_found) {
        printf("User not found\n");
    }

    printf("Token: %s\n", connected_user.api_token);
    response = send_login(sock, connected_user.api_token);
}

void write_message(char* message)
{
    int index = 0;
    int line_pos = 0;
    int line_start[LARGE_CHAR_SIZE / LINE_WIDTH];
    int line_count = 1;
    char ch;

    set_raw_mode();

    line_start[0] = 0;

    while (1) {
        ch = getchar();

        if (ch == 4) { // Ctrl+D pour envoyer
            break;
        } else if (ch == 127) { // Backspace
            if (index > 0) {
                if (message[index - 1] == '\n') {
                    // Retour à la ligne précédente
                    if (line_count > 1) {
                        line_count--;
                        line_pos = index - line_start[line_count - 1];
                        printf("\033[F\033[%dC \033[D", line_pos); // Déplacer à la ligne précédente
                    }
                } else {
                    line_pos--;
                    printf("\b \b"); // Supprime le caractère précédent
                }
                index--;
            }
        } else if (ch == '\n') { // Retour à la ligne
            if (line_count < LARGE_CHAR_SIZE / LINE_WIDTH) {
                message[index++] = ch;
                line_start[line_count++] = index;
                printf("\n");
                line_pos = 0;
            }
        } else if (index < LARGE_CHAR_SIZE - 1) { // Saisie normale
            if (line_pos == LINE_WIDTH) { // Passage automatique à la ligne
                message[index++] = '\n';
                line_start[line_count++] = index;
                printf("\n");
                line_pos = 0;
            }
            message[index++] = ch;
            putchar(ch); // Affiche le caractère
            line_pos++;
        }
    }

    message[index] = '\0'; // Terminer le message

    reset_terminal_mode();
}

void menu_send_message()
{
    clear_term();
    char message[LARGE_CHAR_SIZE] = { 0 };
    menu_t receiver_menu;
    int selected_index = -1;
    int offset = 0;
    const int limit = 5;
    user_list_t receivers;

    printf("\nSend a Message\n");

    printf("Write your message (Ctrl+D to send, Backspace to delete):\n");
    write_message(message);

    if (connected_user.type == MEMBER) {
        strcpy(receiver_menu.name, "Select a Professional to Send the Message");
    } else if (connected_user.type == PROFESSIONAL) {
        strcpy(receiver_menu.name, "Select a Member to Send the Message");
    }

    while (1) {

        if (connected_user.type == MEMBER) {
            receivers = db_get_professionals(conn, offset, limit);

        } else if (connected_user.type == PROFESSIONAL) {
            receivers = db_get_members(conn, offset, limit);
        } else {
            printf("Error: User type not supported for sending messages.\n");
            return;
        }
        if (receivers.count == 0) {
            printf("No more users to display.\n");
            break;
        }
        receiver_menu.actions = malloc((receivers.count + 2) * sizeof(menu_action_t));
        receiver_menu.actions_count = receivers.count + 2;

        for (int i = 0; i < receivers.count; i++) {
            strcpy(receiver_menu.actions[i].name, receivers.users[i].name);
            receiver_menu.actions[i].disabled = 0;
            receiver_menu.actions[i].action = NULL;
        }
        // add action
        strcpy(receiver_menu.actions[receivers.count].name, "Next Page");
        receiver_menu.actions[receivers.count].disabled = 0;
        receiver_menu.actions[receivers.count].action = NULL;

        strcpy(receiver_menu.actions[receivers.count + 1].name, "Previous Page");
        receiver_menu.actions[receivers.count + 1].disabled = (offset == 0);
        receiver_menu.actions[receivers.count + 1].action = NULL;

        selected_index = display_menu(receiver_menu);

        if (selected_index < receivers.count) {
            int receiver_id = receivers.users[selected_index].id;

            response = send_message(sock, connected_user.api_token, message, receiver_id);

            if (response->code == 200) {
                printf("Message successfully sent to %s.\n", receivers.users[selected_index].name);
            } else {
                printf("Failed to send the message. Response: %d %s\n", response->code, response->message);
            }
            free(receiver_menu.actions);
            free(receivers.users);
            break;
        } else if (selected_index == receivers.count) {
            offset += limit;
        } else if (selected_index == receivers.count + 1 && offset > 0) {

            offset -= limit;
        }

        free(receiver_menu.actions);
        free(receivers.users);
    }
}
void menu_delete_message()
}
int message_id;
// display all message sender_id === connecteduser id to choose w arrows
}
void disconnect()
{
    running = 0;
    send_disconnected(sock);
    printf("\n   Disconnected\n");
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

void print_logo()
{
    cprintf(RED, "     _______   _           _        _\n");
    cprintf(RED, "    |__   __| | |         | |      | |\n");
    cprintf(RED, "       | | ___| |__   __ _| |_ __ _| |_ ___  _ __\n");
    cprintf(RED, "       | |/ __| '_ \\ / _` | __/ _` | __/ _ \\| '__|\n");
    cprintf(RED, "       | | (__| | | | (_| | || (_| | || (_) | |\n");
    cprintf(RED, "       |_|\\___|_| |_|\\__,_|\\__\\__,_|\\__\\___/|_|\n\n");
}

void empty_action()
{
}

int main()
{
    print_logo();
    int sock_ret;
    struct sockaddr_in server_addr;
    int choice;
    int is_connected = 0;
    connected_user = NOT_CONNECTED_USER;

    // Create all menus that require a choice
    menu_t menu_login;
    create_menu(
        &menu_login, "Login",
        "Connection client", connection_client,
        "Connection pro", connection_pro,
        "Connection admin", empty_action,
        "Exit", disconnect,
        NULL);
    menu_t menu_client;
    create_menu(
        &menu_client, "Client",
        "Send a message", menu_send_message,
        "Display unread messages", empty_action,
        "Modify a message", empty_action,
        "Delete a message", menu_delete_message,
        "Display messages history", empty_action,
        "Exit", disconnect,
        NULL);
    menu_t menu_pro;
    create_menu(
        &menu_pro, "Professional",
        "Send a message", menu_send_message,
        "Display unread messages", empty_action,
        "Modify a message", empty_action,
        "Delete a message", menu_delete_message,
        "Display messages history", empty_action,
        "Exit", disconnect,
        NULL);
    menu_t menu_admin;
    create_menu(
        &menu_admin, "Admin",
        "Send a message", menu_send_message,
        "Block a message", empty_action,
        "Ban a user", empty_action,
        "Exit", disconnect,
        NULL);

    config_t* config;
    config = malloc(sizeof(config_t));
    config_load(config);

    env_load("..");

    db_login(&conn);

    signal(SIGINT, signal_handler);

    if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        perror("Cannot create socket");
        exit(EXIT_FAILURE);
    }

    // Configure server address
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(SERVER_PORT);
    server_addr.sin_addr.s_addr = INADDR_ANY;

    if ((sock_ret = connect(sock, (struct sockaddr*)&server_addr, sizeof(server_addr))) < 0) {
        perror("Cannot connect to the server socket");
        disconnect(sock);
        exit(EXIT_FAILURE);
    } else {
        printf("Connected to server !\n");
    }

    pros = db_get_professionals(conn, 0, 5);
    clients = db_get_members(conn, 0, 5);
    while (running) {
        choice = 0;
        is_connected = memcmp(&connected_user, &NOT_CONNECTED_USER, sizeof(user_t)) != 0;

        // printf("Connected: %d\n", is_connected);
        // printf("User type: %d\n", connected_user.type);
        // printf("User name: %s\n", connected_user.name);

        if (is_connected) {
            if (connected_user.type == UNKNOWN) {
                db_set_user_type(conn, &connected_user);
            }
            if (connected_user.type == MEMBER) {
                choice = display_menu(menu_client);
                menu_client.actions[choice].action();
            } else if (connected_user.type == PROFESSIONAL) {
                choice = display_menu(menu_pro);
                menu_pro.actions[choice].action();
            } else if (connected_user.type == ADMIN) {
                choice = display_menu(menu_admin);
                menu_admin.actions[choice].action();
            }
        } else {
            choice = display_menu(menu_login);
            menu_login.actions[choice].action();
        }
    }

    close(sock);
    return EXIT_SUCCESS;
}

// Display a menu and return the index of the selected action
int display_menu(menu_t menu)
{
    int selected = 0;
    int entered = 0;
    int key;

    set_raw_mode();

    while (running && !entered) {
        clear_term();

        printf("\n   %s\n\n", menu.name);

        if (memcmp(&connected_user, &NOT_CONNECTED_USER, sizeof(user_t)) != 0) {
            cprintf(CYAN, "   Connected as %s\n\n", connected_user.name);
        }

        for (int i = 0; i < menu.actions_count; i++) {
            if (menu.actions[i].disabled) {
                cprintf(GRAY, "○ %s\n", menu.actions[i].name);
                continue;
            }

            if (selected == i) {
                cprintf(CYAN, " ● ");
            } else {
                printf(" ○ ");
            }

            // if (selected == i) {
            //     cprintf(CYAN, "%s\n", menu.actions[i].name);
            // } else {
            // }
            printf("%s\n", menu.actions[i].name);
        }

        if (response != NULL) {
            printf("\nResponse: %d %s\n", response->code, response->message);
        }

        key = get_arrow_key();
        switch (key) {
        case 'U':
            selected = (selected - 1 + menu.actions_count) % menu.actions_count;
            break;
        case 'D':
            selected = (selected + 1) % menu.actions_count;
            break;
        case '\n':
            entered = 1;
            break;
        }
    }

    reset_terminal_mode();

    return selected;
}

// Create a menu, with a name and a list of actions (name and function)
// The last action should have a NULL name
// Example:
// create_menu(menu, "Main menu", "Action 1", action1, "Action 2", action2, NULL);
void create_menu(menu_t* menu, char name[], ...)
{
    va_list args;
    va_start(args, name);

    menu->actions_count = 0;
    strcpy(menu->name, name);

    while (1) {
        char* action_name = va_arg(args, char*);
        printf("Action name: %s\n", action_name);

        if (action_name == NULL) {
            break;
        }

        menu->actions_count++;
    }

    if (menu->actions_count == 0) {
        menu->actions = NULL;
        va_end(args);
        return;
    }

    menu->actions_count /= 2;
    menu->actions = malloc((menu->actions_count + 1) * sizeof(menu_action_t));

    va_start(args, name);

    for (int i = 0; i < menu->actions_count; i++) {
        char* action_name = va_arg(args, char*);
        menu->actions[i].disabled = 0;
        strcpy(menu->actions[i].name, action_name);
        menu->actions[i].action = va_arg(args, void (*)());
    }

    va_end(args);
}

void menu_add_action(menu_t* menu, char name[], void (*action)())
{
    menu->actions[menu->actions_count].disabled = 0;
    strcpy(menu->actions[menu->actions_count].name, name);
    menu->actions[menu->actions_count].action = action;
    menu->actions_count++;
}