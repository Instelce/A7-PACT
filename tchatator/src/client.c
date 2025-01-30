
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

// -------------------------------------------------------------------------
// Structures
// -------------------------------------------------------------------------

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


// -------------------------------------------------------------------------
// Global variables
// -------------------------------------------------------------------------

volatile sig_atomic_t running = 1;

int sock;
PGconn* conn;
response_t* response;
user_t connected_user;
user_list_t pros;
user_list_t clients;
int discussion_user_id;
char error_message[CHAR_SIZE];


// -------------------------------------------------------------------------
// Functions signatures
// -------------------------------------------------------------------------

/**
 * @brief Displays a menu and returns the index of the selected action.
 *
 * @param menu The menu to display.
 * @return The index of the selected action.
 */
int display_menu(menu_t menu);

/**
 * @brief Creates a menu with a name and a list of actions (name and function).
 * The last action must have a NULL name.
 *
 * Example:
 * @code
 * create_menu(menu, "Main menu", "Action 1", action1, "Action 2", action2, NULL);
 * @endcode
 *
 * @param menu The menu to create.
 * @param name The name of the menu.
 * @param ... The list of actions (name and function).
 */
void create_menu(menu_t* menu, char name[], ...);

/**
 * @brief Adds an action to the menu.
 *
 * @param menu The menu to add the action to.
 * @param name The name of the action.
 * @param action The function associated with the action.
 * @param disabled Indicates if the action is disabled.
 */
void add_menu_action(menu_t* menu, char name[], void (*action)(), int disabled);

/**
 * @brief Sets an error message.
 *
 * @param format The format of the error message.
 * @param ... The arguments for the error message.
 */
void set_error(char format[], ...);

/**
 * @brief Prints a message at a given position.
 *
 * @param x The x position.
 * @param y The y position.
 * @param format The format of the message.
 * @param ... The arguments for the message.
 */
void goto_print(int x, int y, char format[], ...);

/**
 * @brief Displays a message.
 *
 * @param message The message to display.
 * @param align_left Indicates if the message should be left-aligned.
 * @param selected Indicates if the message is selected.
 * @return An integer indicating the result of the display.
 */
int display_message(message_t message, int align_left, int selected);

/**
 * @brief Displays a line.
 */
void display_line();

/**
 * @brief Disconnects the user.
 */
void disconnect();

/**
 * @brief Deletes and updates the menu.
 *
 * @param m The message associated with the update.
 */
void menu_delete_and_update(message_t m);

/**
 * @brief Sends a message from the menu.
 */
void menu_send_message();

/**
 * @brief Selects a discussion from the menu.
 */
void menu_select_discussion();

/**
 * @brief Handles the discussion menu.
 */
void menu_discussion();

/**
 * @brief Handles client connection.
 */
void connection_client();

/**
 * @brief Handles professional connection.
 */
void connection_pro();

/**
 * @brief Handles signals.
 *
 * @param sig The signal to handle.
 */
void signal_handler(int sig);

/**
 * @brief Writes a message with an initial content.
 *
 * @param message The message to write.
 * @param initial_content The initial content of the message.
 */
void write_message(char* message, const char* initial_content);

/**
 * @brief Reads user input.
 *
 * @param output The output buffer for the user input.
 */
void input(char* output);

/**
 * @brief Prints the logo.
 */
void print_logo();

/**
 * @brief Displays a box with a title and a color.
 *
 * @param color The color of the box.
 * @param title The title of the box.
 * @param selected Indicates if the box is selected.
 */
void display_box(color_t color, char title[], int selected);

/**
 * @brief Formats a date string.
 *
 * @param date The date string to format.
 * @return The formatted date string.
 */
char* format_date(char date[]);

void empty_action();


// -------------------------------------------------------------------------
// Main
// -------------------------------------------------------------------------

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
    config_load(config, NULL);

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


// -------------------------------------------------------------------------
// Functions
// -------------------------------------------------------------------------

void input(char* output)
{
    scanf("%s", output);
    getchar();
}

void connection_pro()
{
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

    printf("   Enter your email: ");
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

void write_message(char* message, const char* initial_content)
{
    int index = 0;
    int line_pos = 0;
    int line_start[LARGE_CHAR_SIZE / LINE_WIDTH];
    int line_count = 1;
    char ch;

    set_raw_mode();

    line_start[0] = 0;

    // Copier le contenu initial dans le buffer si fourni
    if (initial_content != NULL && strlen(initial_content) > 0) {
        strcpy(message, initial_content);
        index = strlen(message);

        // Afficher le contenu initial
        printf("%s", message);

        // Calculer les positions des lignes
        for (int i = 0; i < index; i++) {
            if (message[i] == '\n') {
                line_start[line_count++] = i + 1;
                line_pos = 0;
            } else {
                line_pos++;
                if (line_pos == LINE_WIDTH) {
                    line_start[line_count++] = i + 1;
                    line_pos = 0;
                }
            }
        }
    }

    while (1) {
        ch = getchar();

        if (ch == 4) { // Ctrl+D pour envoyer
            break;
        } else if (ch == 127) { // Backspace
            if (index > 0) {
                if (message[index - 1] == '\n') {
                    if (line_count > 1) {
                        line_count--;
                        line_pos = index - line_start[line_count - 1];
                        printf("\033[F\033[%dC \033[D", line_pos); // Déplacer à la ligne précédente
                    }
                } else {
                    line_pos--;
                    printf("\b \b"); // Supprimer le caractère précédent
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
            putchar(ch); // Afficher le caractère
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
    write_message(message, 0);

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

    int selected = -1;
    int entered = 0;
    int key;

    while (running && !entered) {
        clear_term();
        set_raw_mode();
        hide_cursor();
        if (selected == -1) {
            for (int i = 0; i < messages_list.count; i++) {
                if (messages_list.messages[i].sender_id == connected_user.id) {
                    selected = i;
                    break;
                }
            }
        }

        int center_index = (selected == -1) ? 0 : selected;
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
            int is_own_message = (messages_list.messages[i].sender_id == connected_user.id);
            display_message(messages_list.messages[i], !is_own_message, (i == selected && selected != -1 && is_own_message));
        }

        if (selected == -1) {
            printf("\nYou have not sent any messages in this discussion.\n");
        }

        // goto_print(2, w.ws_row - 2, "Selected: %d", selected);
        goto_print(2, w.ws_row - 1, "Send a message: press m");
        goto_print(2, w.ws_row, "Use arrow keys to navigate, Enter to update or delete, q to quit discussion");

        key = get_arrow_key();
        switch (key) {
        case 'U':
            if (selected != -1) {
                do {
                    selected = (selected - 1 + messages_list.count) % messages_list.count;
                } while (messages_list.messages[selected].sender_id != connected_user.id);
            }
            break;

        case 'D':
            if (selected != -1) {
                do {
                    selected = (selected + 1) % messages_list.count;
                } while (messages_list.messages[selected].sender_id != connected_user.id);
            }
            break;

        case '\n':
            if (selected != -1 && messages_list.messages[selected].sender_id == connected_user.id) {
                entered = 1;
                menu_delete_and_update(messages_list.messages[selected]);
            } else if (selected == -1) {
                printf("\nYou cannot select a message because you have not sent any.\n");
                printf("\nPress Enter to continue...");
                getchar();
            } else {
                printf("\nYou can only select your own messages.\n");
                printf("\nPress Enter to continue...");
                getchar();
            }
            break;

        case 'm':
            clear_term();
            char new_message[LARGE_CHAR_SIZE] = { 0 };
            printf("Write your message (Ctrl+D to send, Backspace to delete):\n");
            write_message(new_message, 0);

            response = send_message(sock, connected_user.api_token, new_message, discussion_user_id);
            if (response->status.code == 200) {
                printf("\n\nMessage successfully sent.\n");
            } else {
                printf("Failed to send the message. Response: %d %s\n", response->status.code, response->status.message);
            }
            printf("\nPress Enter to return to the discussion...");
            getchar();
            break;

        case 'q':
            entered = 1;
            break;
        }
    }

    reset_terminal_mode();
    show_cursor();

    free(messages_list.messages);
}

void menu_delete_and_update(message_t m)
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

        display_message(m, 1, 1);

        style_printf(BOLD, "\n   Delete or Update Message");
        display_line();

        char* options[] = { "Delete", "Update", "Cancel" };
        int options_count = 3;

        for (int i = 0; i < options_count; i++) {
            if (selected == i) {
                color_printf(CYAN, "   ● ");
            } else {
                printf("   ○ ");
            }

            printf("%s\n", options[i]);
        }

        key = get_arrow_key();
        switch (key) {
        case 'U':
            selected = (selected - 1 + options_count) % options_count;
            break;
        case 'D':
            selected = (selected + 1) % options_count;
            break;
        case '\n':
            entered = 1;
            break;
        }
    }

    reset_terminal_mode();
    show_cursor();

    if (selected == 0) {

        response = send_delete_message(sock, connected_user.api_token, m.id);
        clear_term();
        if (response->status.code == 200) {
            printf("\nMessage successfully deleted.\n");
        } else {
            printf("\nFailed to delete the message. Response: %d %s\n", response->status.code, response->status.message);
        }
        printf("\nPress Enter to return...");
        getchar();
    } else if (selected == 1) {
        char new_content[LARGE_CHAR_SIZE] = { 0 };
        clear_term();
        cs_printf(NO_COLOR, BOLD, "Edit the message content \n");

        write_message(new_content, m.content);

        response = send_update_message(sock, connected_user.api_token, m.id, new_content);
        clear_term();
        if (response->status.code == 200) {
            printf("\nMessage successfully updated.\n");
        } else {
            printf("\nFailed to update the message. Response: %d %s\n", response->status.code, response->status.message);
        }
        printf("\nPress Enter to return...");
        getchar();
    } else if (selected == 2) {
        printf("\nAction cancelled.\n");
        printf("\nPress Enter to return...");
        getchar();
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

    printf("\n   ");
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
                if (message.modified_date != NULL) {
                    char* modified_date = format_date(message.modified_date);
                    color_printf(color, "Mis à jour %s", modified_date);
                    j += strlen(modified_date) + 11;
                }
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
            if (menu.actions[i].disabled) {
                color_printf(GRAY, "   ○ %s\n", menu.actions[i].name);
                continue;
            }

            if (selected == i) {
                color_printf(CYAN, "   ● ");
            } else {
                printf("   ○ ");
            }

            printf("%s\n", menu.actions[i].name);
            // display_box(selected == i ? CYAN : GRAY, menu.actions[i].name, selected == i);
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
