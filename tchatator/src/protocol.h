#ifndef PROTOCOL_H
#define PROTOCOL_H

#include <sys/socket.h>

#include "types.h"

typedef struct
{
    int code;
    char message[6];
} status_t;

typedef struct
{
    int sock;
    pid_t pid;
    char identity[CHAR_SIZE];
    char ip[CHAR_SIZE];
} client_t;

typedef struct
{
    int id;
    char sended_date[DATE_CHAR_SIZE];
    char modified_date[DATE_CHAR_SIZE];
    int sender_id;
    int receiver_id;
    int deleted;
    int received;
    int sended;
    char content[1000];
} message_t;

typedef struct
{
    char name[CHAR_SIZE];
    char value[CHAR_SIZE];
} command_param_t;

// Represent our protocol action
typedef struct
{
    char name[CHAR_SIZE];
    command_param_t *params;
    int _params_count;
} command_t;

typedef struct {
    char name[CHAR_SIZE];
    int params_count;
} command_def_t;

// All status
static const status_t STATUS_OK = {200, "OK"};
static const status_t STATUS_DENIED = {403, "DENIED"};
static const status_t STATUS_UNAUTHORIZED = {401, "UNAUTH"};
static const status_t STATUS_MIS_FORMAT = {416, "MISFMT"};
static const status_t STATUS_TOO_MESSAGE_RECEIVED = {426, "TOOMRQ"};

// All commands name
static const char LOGIN[] = "LOGIN";
static const char SEND_MESSAGE[] = "SEND_MSG";
static const char UPDATE_MESSAGE[] = "UPDT_MSG";
static const char DELETE_MESSAGE[] = "DEL_MSG";

static const char *EXISTING_COMMANDS[] = {
    LOGIN,
    SEND_MESSAGE,
    UPDATE_MESSAGE,
    DELETE_MESSAGE
};

static const command_def_t COMMANDS_DEFINITIONS[] = {
    {"LOGIN", 1},           // api_token
    {"SEND_MSG", 3},        // token,message-length,content
    {"UPDT_MSG", 2},
    {"DEL_MSG", 1}
};

static const int COMMANDS_COUNT = 4;


char *format_status(status_t status);
char *format_command(command_t command);
status_t *parse_status(char status_str[]);

// Check if a command is registered
int command_exist(char name[]);

// Command related functions
command_def_t get_command_def(char name[]);
char *get_command_param_value(command_t command, char name[]);
command_t create_command(const char *name);
void add_command_param(command_t *command, char name[], char value[]);

// Helper functions to send command to the server
void request(int sock, char buf[]);
void send_message(int sock, char token[], char message[]);
void send_login(int sock, char api_token[]);

#endif // PROTOCOL_H