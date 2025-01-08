#include <string.h>
#include <stdio.h>
#include <stdlib.h>

#include "protocol.h"

int parse_command(char s[], command_t *a) {
    printf("Enter parse action\n");

    char *name;

    char *params_inline;
    char **params = NULL;
    char *param_tmp;

    command_def_t *action_def;

    if (!strstr(s, ":")) {
        return -1;
    }

    name = strtok(s, ":");
    params_inline = strtok(NULL, ":");

    printf("Params inline: %s\n", params_inline);

    action_def = get_action_def(name);

    if (action_def == NULL) {
        return -1;
    }

    params = malloc(1000);
    if (params == NULL) {
        return -1;
    }

    int i = 0;
    while (i < action_def->params_count)
    {
        params[i] = strtok(i == 0 ? params_inline : NULL, ",");
        if (params[i] == NULL) {
            free(params);
            return -1;
        }
        i++;
    }

    strcat(params[i-1], ",");
    while ((param_tmp = strtok(NULL, ",")) != NULL)
    {
        strcat(params[i-1], param_tmp);
        strcat(params[i-1], ",");
    }
    params[i-1][strlen(params[i-1])-1] = '\0';

    for (int i = 0; i < action_def->params_count; i++)
    {
        printf("Param %d: %s\n", i, params[i]);
    }

    printf("Name: %s\n", name);

    return 0;
}

command_def_t * get_action_def(char action_name[]) {
    int found = 0;
    int i = 0;
    command_def_t * action_def = malloc(sizeof(command_def_t));
    action_def = NULL;

    while (!found && i < sizeof(ACTIONS_DEF) / sizeof(command_def_t))
    {
        if (strcmp(ACTIONS_DEF[i].name, action_name) == 0) {
            action_def = &ACTIONS_DEF[i];
            found = 1;
        }
        i++;
    }

    return action_def;
}

char * format_status(status_t status) {
    char *s = malloc(256);
    sprintf(s, "%d/%s", status.code, status.message);
    return s;
}