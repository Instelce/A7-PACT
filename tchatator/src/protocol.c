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

response_t* parse_response(char response_str[])
{
    char* response_str_copy = strdup(response_str);
    response_t* response = malloc(sizeof(response_t));
    char* status_str = strtok(response_str_copy, "\n");

    response->status = *parse_status(status_str);
    response->data_list = NULL;
    response->data_list_size = 0;

    char* data_start = strstr(response_str, "\n");
    if (data_start) {
        data_start += 1;
    } else {
        return response;
    }

    while (data_start && *data_start != '\0') {
        char* line_end = strstr(data_start, "\n");
        if (!line_end)
            break;

        char data_line[LARGE_CHAR_SIZE];
        strncpy(data_line, data_start, line_end - data_start);
        data_line[line_end - data_start] = '\0';

        char* name = strtok(data_line, ":");
        char* value = strtok(NULL, ":");

        add_response_data(response, name, value);

        data_start = line_end + 1;
    }

    return response;
}

response_t create_response(response_status_t status)
{
    response_t response;
    response.status = status;
    response.data_list = NULL;
    response.data_list_size = 0;
    return response;
}

void add_response_data(response_t* response, char name[], char value[])
{
    // Allocate more memory
    response->data_list = realloc(response->data_list, (response->data_list_size + 1) * sizeof(response_data_t));

    strcpy(response->data_list[response->data_list_size].name, name);
    strcpy(response->data_list[response->data_list_size].value, value);
    response->data_list_size++;
}

char* get_response_data(response_t response, char name[])
{
    for (int i = 0; i < response.data_list_size; i++) {
        if (strcmp(response.data_list[i].name, name) == 0) {
            return response.data_list[i].value;
        }
    }
    return "";
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

response_t* request(int sock, char buf[])
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

    return parse_response(response);
}

response_t* send_message(int sock, char token[], char message[], int receiver_id)
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

response_t* send_login(int sock, char api_token[])
{
    command_t command = create_command(LOGIN);
    char* buf;

    trim(api_token);

    add_command_param(&command, "api-token", api_token);

    buf = format_command(command);

    return request(sock, buf);
}

response_t* send_uptade_message(int sock, char token[], int message_id, char message[])
{
    command_t command = create_command(UPDATE_MESSAGE);

    add_command_param(&command, "token", token);
    add_command_param(&command, "message-id", to_string(message_id));
    add_command_param(&command, "message-length", to_string(strlen(message)));
    add_command_param(&command, "content", message);

    return request(sock, format_command(command));
}

response_t* send_delete_message(int sock, char token[], int message_id)
{
    command_t command = create_command(DELETE_MESSAGE);

    add_command_param(&command, "token", token);
    add_command_param(&command, "message-id", to_string(message_id));

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