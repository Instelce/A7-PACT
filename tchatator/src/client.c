#include <fcntl.h>
#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <termio.h>
#include <time.h>
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

volatile sig_atomic_t running = 1;

int sock;
PGconn* conn;
response_t* response;
user_t connected_user;
user_list_t pros;
user_list_t clients;
int discussion_user_id;
char error_message[CHAR_SIZE];

// Display a menu and return the index of the selected action
int display_menu(menu_t menu);

// Create a menu, with a name and a list of actions (name and function)
// The last action should have a NULL name
// Example:
// create_menu(menu, "Main menu", "Action 1", action1, "Action 2", action2, NULL);
void create_menu(menu_t* menu, char name[], ...);

void add_menu_action(menu_t* menu, char name[], void (*action)(), int disabled);

void set_error(char format[], ...);

void goto_print(int x, int y, char format[], ...);

int display_message(message_t message, int align_left, int selected);

void display_line();

void disconnect();

void input(char* output)
{
    scanf("%s", output);
    getchar();
}

void connection_pro()
{
    printf("\n");
    display_line();

    char mail[CHAR_SIZE], token[API_TOKEN_SIZE];
    printf("   Enter your email: ");
    input(mail);

    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "brehat@gmail.com");
    }

    int user_found = db_get_user_by_email(conn, &connected_user, mail);

    if (!user_found) {
        set_error("User with the '%s' email does not exist", mail);
        return;
    }

    response = send_login(sock, connected_user.api_token);
}

void connection_client()
{
    display_line();

    char mail[CHAR_SIZE], token[API_TOKEN_SIZE];

    printf("\n   Enter your email: ");
    input(mail);

    // For testing
    if (strcmp(mail, "o") == 0) {
        strcpy(mail, "eliaz.chesnel@outlook.fr");
    }

    int user_found = db_get_user_by_email(conn, &connected_user, mail);

    if (!user_found) {
        set_error("User with the '%s' email does not exist", mail);
        return;
    }

    response = send_login(sock, connected_user.api_token);

    if (response->status.code == 403) {
        connected_user = NOT_CONNECTED_USER;
        set_error(get_response_data(*response, "message"));
    }
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

    printf("\nSend a Message\n\n");

    printf("Write your message (Ctrl+D to send, Backspace to delete):\n");
    write_message(message);

    // Set menu name
    if (connected_user.type == MEMBER) {
        strcpy(receiver_menu.name, "Select the professional recipient of the message");
    } else if (connected_user.type == PROFESSIONAL) {
        strcpy(receiver_menu.name, "select the member recipient of the message");
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
            // add_menu_action(&receiver_menu, receivers.users[i].name, NULL, 0);
            strcpy(receiver_menu.actions[i].name, receivers.users[i].name);
            receiver_menu.actions[i].disabled = 0;
            receiver_menu.actions[i].action = NULL;
        }

        // Add action for next and previous page
        // add_menu_action(&receiver_menu, "Next page", NULL, 0);
        // add_menu_action(&receiver_menu, "Previous page", NULL, offset == 0);
        strcpy(receiver_menu.actions[receivers.count].name, "Next Page");
        receiver_menu.actions[receivers.count].disabled = (receivers.count < limit);
        receiver_menu.actions[receivers.count].action = NULL;

        strcpy(receiver_menu.actions[receivers.count + 1].name, "Previous Page");
        receiver_menu.actions[receivers.count + 1].disabled = (offset == 0);
        receiver_menu.actions[receivers.count + 1].action = NULL;

        selected_index = display_menu(receiver_menu);

        if (selected_index < receivers.count) {
            int receiver_id = receivers.users[selected_index].id;

            response = send_message(sock, connected_user.api_token, message, receiver_id);

            if (response->status.code == 200) {
                printf("Message successfully sent to %s.\n", receivers.users[selected_index].name);
            } else {
                printf("Failed to send the message. Response: %d %s\n", response->status.code, response->status.message);
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

// Choose a discussion with a specific user
void menu_select_discussion()
{
    user_list_t receiver_user_list = db_get_all_receiver_users_of_user(conn, connected_user.id);
    int selected_index = -1;
    menu_t user_menu;
    user_menu.actions = NULL;

    // Setup menu with users
    strcpy(user_menu.name, "Choose user to discuss with");
    for (int i = 0; i < receiver_user_list.count; i++) {
        add_menu_action(&user_menu, receiver_user_list.users[i].email, NULL, 0);
    }

    add_menu_action(&user_menu, "Back", NULL, 0);

    // Display menu
    selected_index = display_menu(user_menu);

    if (selected_index < receiver_user_list.count) {
        discussion_user_id = receiver_user_list.users[selected_index].id;
    }
}

void menu_discussion()
{
    struct winsize w;
    ioctl(STDOUT_FILENO, TIOCGWINSZ, &w);

    int message_chunk_size = 5;
    message_list_t messages_list = db_get_messages_between_users(conn, connected_user.id, discussion_user_id, 0, message_chunk_size);

    int selected = 0;
    int entered = 0;
    int key;

    set_raw_mode();
    hide_cursor();

    while (running && !entered) {
        clear_term();

        int center_index = selected;
        int start_index = center_index;
        int end_index = center_index;
        int above_height = 0;
        int below_height = 0;

        while (start_index > 0 && above_height < w.ws_row - 8) {
            above_height += display_message(messages_list.messages[start_index - 1], 1, 0);
            if (above_height < w.ws_row - 8) {
                start_index--;
            }
        }

        while (end_index < messages_list.count - 1 && below_height < w.ws_row - 8) {
            below_height += display_message(messages_list.messages[end_index + 1], 1, 0);
            if (below_height < w.ws_row - 8) {
                end_index++;
            }
        }

        clear_term();
        for (int i = start_index; i <= end_index; i++) {
            display_message(messages_list.messages[i], messages_list.messages[i].sender_id != connected_user.id, i == selected);
        }

        goto_print(2, w.ws_row - 2, "Selected: %d", selected);
        goto_print(2, w.ws_row - 1, "Send a message: Ctrl+M");
        goto_print(2, w.ws_row, "Use arrow keys to navigate, Enter to select, Ctrl+C to quit discussion");

        key = get_arrow_key();
        switch (key) {
        case 'U':
            if (selected > 0) {
                selected--;
            }
            break;
        case 'D':
            if (selected < messages_list.count - 1) {
                selected++;
            } else {
                // Load more messages
                message_list_t new_messages_list = db_get_messages_between_users(conn, connected_user.id, discussion_user_id, messages_list.count, message_chunk_size);
                if (new_messages_list.count > 0) {
                    int old_count = messages_list.count;
                    messages_list.count += new_messages_list.count;
                    messages_list.messages = realloc(messages_list.messages, messages_list.count * sizeof(message_t));
                    for (int i = 0; i < new_messages_list.count; i++) {
                        messages_list.messages[old_count + i] = new_messages_list.messages[i];
                    }
                    free(new_messages_list.messages);
                } else {
                    if (messages_list.count % message_chunk_size != 0) {
                        selected = 0;
                    }
                }
            }

            break;
        case '\n':
            entered = 1;
            break;
        }
    }

    reset_terminal_mode();
    show_cursor();
}

void menu_delete_message()
{
    clear_term();
    menu_t delete_message_menu;
    int selected_index = -1;
    int offset = 0;
    const int limit = 5;
    message_list_t messages;

    printf("\nDelete a Message\n");
    strcpy(delete_message_menu.name, "Select a Message to Delete");
    while (1) {
        messages = db_get_messages_by_sender(conn, connected_user.id, offset, limit);

        if (messages.count == 0) {
            printf("No more messages to display.\n");
            break;
        }

        delete_message_menu.actions = malloc((messages.count + 2) * sizeof(menu_action_t));
        delete_message_menu.actions_count = messages.count + 2;

        for (int i = 0; i < messages.count; i++) {
            strcpy(delete_message_menu.actions[i].name, messages.messages[i].content);
        }

        strcpy(delete_message_menu.actions[messages.count].name, "Next Page");

        strcpy(delete_message_menu.actions[messages.count + 1].name, "Previous Page");

        selected_index = display_menu(delete_message_menu);

        if (selected_index < messages.count) {
            int message_id = messages.messages[selected_index].id;

            response = send_delete_message(sock, connected_user.api_token, message_id);

            if (response->status.code == 200) {
                printf("Message successfully deleted.\n");
            } else {
                printf("Failed to delete the message. Response: %d %s\n", response->status.code, response->status.message);
            }
            free(delete_message_menu.actions);
            free(messages.messages);
            break;
        } else if (selected_index == messages.count) {

            offset += limit;
        } else if (selected_index == messages.count + 1 && offset > 0) {
            offset -= limit;
        }

        free(delete_message_menu.actions);
        free(messages.messages);
    }
}

void menu_display_unread_messages()
{
    clear_term();
    menu_t unread_messages_menu;
    int selected_index = -1;
    int offset = 0;
    const int limit = 5;
    message_list_t messages;

    printf("\nDisplay Unread Messages\n");
    strcpy(unread_messages_menu.name, "Select a Message to Mark as Read");
    while (1) {
        messages = db_get_unread_messages(conn, connected_user.id, offset, limit);

        if (messages.count == 0) {
            printf("No more messages to display.\n");
            break;
        }

        unread_messages_menu.actions = malloc((messages.count + 2) * sizeof(menu_action_t));
        unread_messages_menu.actions_count = messages.count + 2;

        for (int i = 0; i < messages.count; i++) {
            strcpy(unread_messages_menu.actions[i].name, messages.messages[i].content);
        }

        strcpy(unread_messages_menu.actions[messages.count].name, "Next Page");

        strcpy(unread_messages_menu.actions[messages.count + 1].name, "Previous Page");

        selected_index = display_menu(unread_messages_menu);

        if (selected_index < messages.count) {
            int message_id = messages.messages[selected_index].id;

            response = send_uptade_message(sock, connected_user.api_token, message_id, "seen");

            if (response->status.code == 200) {
                printf("Message successfully marked as read.\n");
            } else {
                printf("Failed to mark the message as read. Response: %d %s\n", response->status.code, response->status.message);
            }
            free(unread_messages_menu.actions);
            free(messages.messages);
            break;
        } else if (selected_index == messages.count) {

            offset += limit;
        } else if (selected_index == messages.count + 1 && offset > 0) {
            offset -= limit;
        }

        free(unread_messages_menu.actions);
        free(messages.messages);
    }
}

void disconnect()
{
    running = 0;
    send_disconnected(sock);
    printf("\n   Disconnected, bye bye !\n");
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

void display_box(color_t color, char title[], int selected)
{
    struct winsize w;
    ioctl(STDOUT_FILENO, TIOCGWINSZ, &w);
    // int margin = selected ? 3 : 6;
    int margin = 6;
    int width = w.ws_col - margin;
    int height = 3;

    for (int i = 0; i < height; i++) {
        for (int j = 0; j < margin / 2; j++) {
            printf(" ");
        }

        for (int j = 0; j < width; j++) {
            if (i == 0) {
                if (j == 0) {
                    color_printf(color, "╭");
                } else if (j == width - 1) {
                    color_printf(color, "╮");
                } else {
                    color_printf(color, "─");
                }
            } else if (i == height - 1) {
                if (j == 0) {
                    color_printf(color, "╰");
                } else if (j == width - 1) {
                    color_printf(color, "╯");
                } else {
                    color_printf(color, "─");
                }
            } else {
                if (j == 0 || j == width - 1) {
                    color_printf(color, "│");
                } else {
                    color_printf(color, " ");
                }
            }

            if (i == height / 2 && j == 1) {
                if (selected) {
                    color_printf(color, "%s", title);
                } else {
                    printf("%s", title);
                }
                j += strlen(title);
            }

            // Right
            if (i == 0 && j == width - 2) {
                color_printf(color, "╖");
                j++;
            }

            if (i != 0 && i != height - 1 && j == width - 2) {
                color_printf(color, "║");
                j++;
            }

            if (i == height - 1 && j == width - 2) {
                color_printf(color, "╜");
                j++;
            }
        }
        printf("\n");
    }
}

void display_line()
{
    struct winsize w;
    ioctl(STDOUT_FILENO, TIOCGWINSZ, &w);

    printf("   ");
    for (int i = 0; i < w.ws_col - 6; i++) {
        color_printf(GRAY, "╴");
    }
    printf("\n\n");
}

/// @brief Format a date to a human readable format
/// @param date date "YYYY-MM-DD HH:MM:SS"
char* format_date(char date[])
{
    char* formatted_date = malloc(CHAR_SIZE);
    int min = 0;
    int hour = 0;
    int day = 0;
    int month = 0;
    int year = 0;
    int sec = 0;

    time_t now;
    struct tm tm;
    time(&now);
    tm = *localtime(&now);

    sscanf(date, "%d-%d-%d %d:%d:%d", &year, &month, &day, &hour, &min, &sec);

    if (tm.tm_year + 1900 == year && tm.tm_mon + 1 == month && tm.tm_mday == day) {
        if (tm.tm_hour == hour) {
            if (tm.tm_min == min) {
                if (tm.tm_sec == sec) {
                    strcpy(formatted_date, "il y a quelques secondes");
                } else {
                    strcpy(formatted_date, "il y a quelques minutes");
                }
            } else {
                strcpy(formatted_date, "il y a ");
                if (tm.tm_min - min == 15 || tm.tm_min - min == 30 || tm.tm_min - min == 45) {
                    strcat(formatted_date, to_string(tm.tm_min - min));
                    strcat(formatted_date, " min");
                } else {
                    strcat(formatted_date, to_string(tm.tm_min - min));
                    strcat(formatted_date, " min");
                }
            }
        } else {
            strcpy(formatted_date, "il y a ");
            strcat(formatted_date, to_string(tm.tm_hour - hour));
            strcat(formatted_date, " h");
        }
    } else {
        strcpy(formatted_date, "il y a ");
        strcat(formatted_date, to_string(tm.tm_mday - day));
        strcat(formatted_date, " jours");
    }

    return formatted_date;
}

void goto_print(int x, int y, char format[], ...)
{
    va_list args;
    va_start(args, format);
    printf("\033[%d;%dH", y, x);
    vprintf(format, args);
    printf("\033[0m");
    va_end(args);
}

/// @brief Display a card for a message
/// @param message
/// @param align_left if true, align the message to the left, otherwise to the right
/// @param selected if true, highlight the message
/// @return int the height of the message
int display_message(message_t message, int align_left, int selected)
{
    struct winsize w;
    ioctl(STDOUT_FILENO, TIOCGWINSZ, &w);

    // Estimate the height of the message
    // And split the message into lines
    char c;
    int line_count = 0;
    char* lines[CHAR_SIZE];

    lines[0] = malloc(CHAR_SIZE);
    memset(lines[0], 0, CHAR_SIZE);

    // printf("size %d\n", (w.ws_col / 2 - 2));

    for (int i = 0; i < strlen(message.content); i++) {
        c = message.content[i];

        // printf("c: %c\n", c);
        // printf("%d\n", strlen(lines[line_count]));

        if (c != '\n') {
            strncat(lines[line_count], &c, 1);
        }

        if (c == '\n' || strlen(lines[line_count]) >= (w.ws_col / 2 - 6)) {
            line_count++;
            lines[line_count] = malloc(CHAR_SIZE);
            memset(lines[line_count], 0, CHAR_SIZE);
        }
    }

    int width = w.ws_col / 2 - 2;
    int height = line_count + 8;
    int left_space = align_left ? 0 : w.ws_col / 2;
    color_t color = selected ? CYAN : WHITE;

    user_t receiver_user;
    if (connected_user.id == message.receiver_id) {
        receiver_user = connected_user;
    } else {
        db_get_user(conn, &receiver_user, message.receiver_id);
        db_set_user_type(conn, &receiver_user);
    }

    user_t sender_user;
    if (connected_user.id == message.sender_id) {
        sender_user = connected_user;
    } else {
        db_get_user(conn, &sender_user, message.sender_id);
        db_set_user_type(conn, &sender_user);
    }

    char* sender = connected_user.id == message.sender_id ? "You" : sender_user.name;
    char* sended_date = format_date(message.sended_date);
    char* modified_date = format_date(message.modified_date);

    int printed_line_count = 0;

    for (int i = 0; i < height; i++) {
        for (int j = 0; j < left_space; j++) {
            printf(" ");
        }
        for (int j = 0; j < width; j++) {
            if (i == 0) {
                if (j == 0) {
                    color_printf(color, "╭");
                } else if (j == width - 1) {
                    color_printf(color, "╮");
                } else {
                    color_printf(color, "─");
                }
            } else if (i == height - 1) {
                if (j == 0) {
                    color_printf(color, "╰");
                } else if (j == width - 1) {
                    color_printf(color, "╯");
                } else {
                    color_printf(color, "─");
                }
            } else {
                if (j == 0 || j == width - 1) {
                    color_printf(color, "│");
                } else {
                    color_printf(color, " ");
                }
            }

            if (i == 1 && j == 1) {
                cs_printf(color, BOLD, "%s", sender);
                j += strlen(sender);
            }

            // Show message content
            if (i == 3 + printed_line_count && j == 1 && printed_line_count <= line_count) {
                printf("%s", lines[printed_line_count]);
                j += strlen(lines[printed_line_count]);
                printed_line_count++;
            }

            if (i == 5 + line_count && j == 1) {
                color_printf(color, "Envoyé %s", sended_date);
                j += strlen(sended_date) + 7;
            }
            if (i == 6 + line_count && j == 1) {
                color_printf(color, "Mis à jour %s", sended_date);
                j += strlen(sended_date) + 11;
            }
        }
        printf("\n");
    }

    return height;
}

void print_logo()
{
    color_printf(RED, "     _______   _           _        _\n");
    color_printf(RED, "    |__   __| | |         | |      | |\n");
    color_printf(RED, "       | | ___| |__   __ _| |_ __ _| |_ ___  _ __\n");
    color_printf(RED, "       | |/ __| '_ \\ / _` | __/ _` | __/ _ \\| '__|\n");
    color_printf(RED, "       | | (__| | | | (_| | || (_| | || (_) | |\n");
    color_printf(RED, "       |_|\\___|_| |_|\\__,_|\\__\\__,_|\\__\\___/|_|\n\n");
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
    discussion_user_id = -1;

    // Create all menus that require a choice
    menu_t menu_login;
    create_menu(
        &menu_login, "Login",
        "Connection client", connection_client,
        "Connection pro", connection_pro,
        "Connection admin", empty_action,
        "Disconnect", disconnect,
        NULL);
    menu_t menu_client;
    create_menu(
        &menu_client, "Client",
        "Send a message", menu_send_message,
        "Discussions", menu_select_discussion,
        "Disconnect", disconnect,
        NULL);
    menu_t menu_pro;
    create_menu(
        &menu_pro, "Professional",
        "Send a message", menu_send_message,
        "Discussions", menu_select_discussion,
        "Disconnect", disconnect,
        NULL);
    menu_t menu_admin;
    create_menu(
        &menu_admin, "Admin",
        "Block a message", empty_action,
        "Ban a user", empty_action,
        "Disconnect", disconnect,
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

        if (discussion_user_id != -1) {
            menu_discussion();
        }

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
    struct winsize w;
    ioctl(STDOUT_FILENO, TIOCGWINSZ, &w);

    int selected = 0;
    int entered = 0;
    int key;

    set_raw_mode();
    hide_cursor();

    while (running && !entered) {
        clear_term();

        style_printf(BOLD, "\n   %s", menu.name);

        if (memcmp(&connected_user, &NOT_CONNECTED_USER, sizeof(user_t)) != 0) {
            for (int i = 0; i < w.ws_col - 6 - 13 - strlen(connected_user.name) - strlen(menu.name); i++) {
                color_printf(GRAY, " ");
            }
            color_printf(CYAN, "Connected as ");
            cs_printf(CYAN, BOLD, "%s\n\n", connected_user.name);
        } else {
            printf("\n\n");
        }

        display_line();

        for (int i = 0; i < menu.actions_count; i++) {
            // if (menu.actions[i].disabled) {
            //     color_printf(GRAY, "   ○ %s\n", menu.actions[i].name);
            //     continue;
            // }

            // if (selected == i) {
            //     color_printf(CYAN, "   ● ");
            // } else {
            //     printf("   ○ ");
            // }

            // if (selected == i) {
            //     color_printf(CYAN, "%s\n", menu.actions[i].name);
            // } else {
            // }
            // printf("%s\n", menu.actions[i].name);
            display_box(selected == i ? CYAN : GRAY, menu.actions[i].name, selected == i);
        }

        // Show error message
        if (strlen(error_message) > 0) {
            color_printf(RED, "\n   %s\n", error_message);
        }

        // if (response != NULL) {
        //     printf("\nResponse: %d %s\n", response->status.code, response->status.message);
        // }

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
    show_cursor();

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
        // printf("Action name: %s\n", action_name);

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

void add_menu_action(menu_t* menu, char name[], void (*action)(), int disabled)
{
    // Allocate more space
    if (menu->actions == NULL) {
        menu->actions = malloc(sizeof(menu_action_t));
        menu->actions_count = 0;
    } else {
        menu->actions = realloc(menu->actions, (menu->actions_count + 1) * sizeof(menu_action_t));
    }

    menu->actions[menu->actions_count].disabled = disabled;
    strcpy(menu->actions[menu->actions_count].name, name);
    menu->actions[menu->actions_count].action = action;
    menu->actions_count++;
}

void set_error(char format[], ...)
{
    va_list args;
    va_start(args, format);
    vsprintf(error_message, format, args);
    va_end(args);
}
