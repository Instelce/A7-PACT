#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include "protocol.h"
#include "types.h"
#include "utils.h"

char* format_response(response_t response)
{
    char* format = malloc(LARGE_CHAR_SIZE);
    sprintf(format, "%s\n", format_status(response.status));

    for (int i = 0; i < response.data_list_size; i++) {
        sprintf(format, "%s%s:%s\n", format, response.data_list[i].name, response.data_list[i].value);
    }

    return format;
}

char* format_status(response_status_t status)
{
    char* format = malloc(CHAR_SIZE);
    sprintf(format, "%d/%s", status.code, status.message);
    return format;
}

char* format_command(command_t command)
{
    char* format = malloc(LARGE_CHAR_SIZE);
    command_def_t command_def = get_command_def(command.name);

    sprintf(format, "%s\n", command.name);
    for (int i = 0; i < command_def.params_count; i++) {
        sprintf(format, "%s%s:%s\n", format, command.params[i].name, command.params[i].value);
    }

    return format;
}

response_status_t* parse_status(char status_str[])
{
    response_status_t* status = malloc(sizeof(response_status_t));
    char* code_str = strtok(status_str, "/");
    char* message = strtok(NULL, "/");

    status->code = atoi(code_str);
    strcpy(status->message, message);

    return status;
}

int command_exist(char name[])
{
    for (int i = 0; i < COMMANDS_COUNT; i++) {
        if (strcmp(EXISTING_COMMANDS[i], name) == 0) {
            return 1;
        }
    }
    return 0;
}

command_def_t get_command_def(const char* name)
{
    for (int i = 0; i < sizeof(COMMANDS_DEFINITIONS) / sizeof(COMMANDS_DEFINITIONS[0]); i++) {
        if (strcmp(COMMANDS_DEFINITIONS[i].name, name) == 0) {
            return COMMANDS_DEFINITIONS[i];
        }
    }
    return (command_def_t) { "", 0 };
}

char* get_command_param_value(command_t command, char name[])
{
    command_def_t command_def = get_command_def(command.name);
    for (int i = 0; i < command_def.params_count; i++) {
        if (strcmp(command.params[i].name, name) == 0) {
            return command.params[i].value;
        }
    }
    return "";
}

command_t create_command(const char* name)
{
    command_t command;
    command_def_t command_def = get_command_def(name);

    strcpy(command.name, name);
    command.params = malloc(command_def.params_count * sizeof(command_param_t));
    command._params_count = 0;

    return command;
}

void add_command_param(command_t* command, char name[], char value[])
{
    command_def_t command_def = get_command_def(command->name);

    if (command->_params_count <= command_def.params_count) {
        strcpy(command->params[command->_params_count].name, name);
        strcpy(command->params[command->_params_count].value, value);
        command->_params_count++;
    }
}

response_status_t* request(int sock, char buf[])
{
    char response[LARGE_CHAR_SIZE];
    memset(response, 0, sizeof(response));

    if (write(sock, buf, strlen(buf)) < 0) {
        perror("Error sending message");
        exit(EXIT_FAILURE);
    }

    // Wait for the server response
    if (read(sock, response, sizeof(response)) < 0) {
        perror("Error receiving response");
        exit(EXIT_FAILURE);
    }

    // printf("%s\n", response);

    return parse_status(response);
}

response_status_t* send_message(int sock, char token[], char message[], int receiver_id)
{
    command_t command = create_command(SEND_MESSAGE);
    char* buf;

    add_command_param(&command, "token", token);
    add_command_param(&command, "message-length", to_string(strlen(message)));
    add_command_param(&command, "receiver-id", to_string(receiver_id));
    add_command_param(&command, "content", message);

    buf = format_command(command);

    return request(sock, buf);
}

response_status_t* send_login(int sock, char api_token[])
{
    command_t command = create_command(LOGIN);
    char* buf;

    trim(api_token);

    add_command_param(&command, "api-token", api_token);

    buf = format_command(command);

    return request(sock, buf);
}

response_status_t* send_uptade_message(int sock, char token[], int message_id, char message[])
{
    command_t command = create_command(UPDATE_MESSAGE);

    add_command_param(&command, "token", token);
    add_command_param(&command, "message-id", to_string(message_id));
    add_command_param(&command, "message-length", to_string(strlen(message)));
    add_command_param(&command, "content", message);

    return request(sock, format_command(command));
}

response_status_t* send_delete_message(int sock, char token[], int message_id)
{
    command_t command = create_command(DELETE_MESSAGE);

    add_command_param(&command, "token", token);
    add_command_param(&command, "message-id", to_string(message_id));

    return request(sock, format_command(command));
}

response_status_t* send_get_new_message(int sock, char token[])
{
    command_t command = create_command(GET_NEW_MESSAGES);

    add_command_param(&command, "token", token);

    return request(sock, format_command(command));
}

response_status_t* send_is_connected(int sock, int user_id)
{
    command_t command = create_command(IS_CONNECTED);

    add_command_param(&command, "user-id", to_string(user_id));

    return request(sock, format_command(command));
}

void send_disconnected(int sock)
{
    command_t command = create_command(DISCONNECTED);
    char buf[LARGE_CHAR_SIZE];
    memset(buf, 0, sizeof(buf));

    strcpy(buf, format_command(command));

    if (write(sock, buf, strlen(buf)) < 0) {
        perror("Error sending message");
        exit(EXIT_FAILURE);
    }
}