#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include "protocol.h"

char* format_status(status_t status)
{
    char* format = malloc(256);
    sprintf(format, "%d/%s", status.code, status.message);
    return format;
}

char* format_command(command_t command)
{
    char* format = malloc(1024);
    command_def_t command_def = get_command_def(command.name);

    sprintf(format, "%s\n", command.name);
    for (int i = 0; i < command_def.params_count; i++) {
        sprintf(format, "%s%s:%s\n", format, command.params[i].name, command.params[i].value);
    }

    return format;
}

status_t* parse_status(char status_str[])
{
    status_t* status = malloc(sizeof(status_t));
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

command_def_t get_command_def(char name[])
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

status_t* request(int sock, char buf[])
{
    char response[1024];
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

status_t* send_message(int sock, char token[], char message[], int receiver_id)
{
    command_t command = create_command(SEND_MESSAGE);
    char int_convertion[5];
    char* buf;

    add_command_param(&command, "token", token);

    sprintf(int_convertion, "%d", strlen(message));
    add_command_param(&command, "message-length", int_convertion);
    sprintf(int_convertion, "%d", receiver_id);
    add_command_param(&command, "receiver-id", int_convertion);

    add_command_param(&command, "content", message);

    buf = format_command(command);

    return request(sock, buf);
}

status_t* send_login(int sock, char api_token[])
{
    command_t command = create_command(LOGIN);
    char* buf;

    add_command_param(&command, "api-token", api_token);

    buf = format_command(command);

    return request(sock, buf);
}
