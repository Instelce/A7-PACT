#ifndef CONFIG_H
#define CONFIG_H

#include "types.h"

typedef struct config {
    int port;
    char log_file[CHAR_SIZE];
    int ban_duration;
    int max_messages_per_minutes;
    int max_messages_per_hours;
    int max_message_length;
    int max_message_reception_block;
    char admin_api_token[API_TOKEN_SIZE];
} config_t;

void config_load(config_t* c, char* path);

void env_load(char dir_path[]);

#endif // CONFIG_H