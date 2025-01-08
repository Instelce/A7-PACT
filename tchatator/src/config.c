#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <string.h>
#include <ctype.h>

#include "config.h"
#include "utils.h"

#define MAX_LINE_SIZE 100

void config_load(config_t *c) {
    int fd;
    int len;
    char buf;
    char line[MAX_LINE_SIZE];
    char *var_name;
    char *var_value;

    // Open config file
    fd = open("config", O_RDONLY);

    // Read config file character by character
    while ((len = read(fd, &buf, 1)) > 0) {
        if (buf != '\n') {
            // Add the caracter to the line
            strncat(line, &buf, 1);
        } else {

            // Check if it is not a commented line 
            // and which contains a equal
            if (line[0] != '#' && strstr(line, "=")) {

                // Split the line to get the name and value of the variable
                var_name = strtok(line, "=");
                var_value = strtok(NULL, "=");

                // printf("%s %s\n", var_name, var_value);

                // Assign value to config attribute
                if (strcmp(var_name, "port") == 0) {
                    c->port = atoi(var_value);
                } else if (strcmp(var_name, "log-file") == 0) {
                    strcpy(c->log_file, var_value);
                } else if (strcmp(var_name, "ban-duration") == 0) {
                    c->ban_duration = atoi(var_value);
                } else if (strcmp(var_name, "max-messages-per-minutes") == 0) {
                    c->max_messages_per_minutes = atoi(var_value);
                } else if (strcmp(var_name, "max-messages-per-hours") == 0) {
                    c->max_messages_per_hours = atoi(var_value);
                } else if (strcmp(var_name, "max-message-length") == 0) {
                    c->max_message_length = atoi(var_value);
                } else if (strcmp(var_name, "max-message-reception-block") == 0) {
                    c->max_message_reception_block = atoi(var_value);
                }
            }

            // Reset the line
            memset(line, 0, MAX_LINE_SIZE);
        }
    };

    if (len < 0) {
        perror("Cannot read the config file");
        exit(1);
    }
}

void env_load(char dir_path[]) {
    int fd;
    int len;
    char buf;
    char line[MAX_LINE_SIZE] = {0};
    char *var_name;
    char var_value[CHAR_SIZE];
    char *var_value_tmp;
    char file_path[CHAR_SIZE];

    sprintf(file_path, "%s/.env", dir_path);

    fd = open(file_path, O_RDONLY);

    while ((len = read(fd, &buf, 1)) > 0) {
        if (buf != '\n') {
            strncat(line, &buf, 1);
        } else {
            if (line[0] != '#' && strstr(line, "=")) {
                var_name = strtok(line, "=");

                while ((var_value_tmp = strtok(NULL, "=")) != NULL)
                {
                    strcat(var_value, var_value_tmp);
                    strcat(var_value, "=");
                }

                // Remove the last =
                var_value[strlen(var_value) - 1] = '\0';

                trim(var_name);
                trim(var_value);

                setenv(var_name, var_value, 1);

                // printf("%s, %s\n", var_name, var_value);
            }

            memset(var_value, 0, CHAR_SIZE);
            memset(line, 0, MAX_LINE_SIZE);
        }
    }

    if (len < 0) {
        perror("Cannot read the env file");
        exit(1);
    }
}