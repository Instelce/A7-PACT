#ifndef PROTOCOL_H
#define PROTOCOL_H

#include <sys/socket.h>

#include "database.h"
#include "types.h"

typedef struct
{
    int code;
    char message[6];
} status_t;

typedef struct
{
    char name[CHAR_SIZE];
    char value[CHAR_SIZE];
} command_param_t;

// Represent our protocol action
typedef struct
{
    char name[CHAR_SIZE];
    command_param_t* params;
    int _params_count;
} command_t;

typedef struct {
    char name[CHAR_SIZE];
    int params_count;
} command_def_t;

// All status
static const status_t STATUS_OK = { 200, "OK" };
static const status_t STATUS_DENIED = { 403, "DENIED" };
static const status_t STATUS_UNAUTHORIZED = { 401, "UNAUTH" };
static const status_t STATUS_MIS_FORMAT = { 416, "MISFMT" };
static const status_t STATUS_TOO_MESSAGE_RECEIVED = { 426, "TOOMRQ" };

// All commands name
static const char LOGIN[] = "LOGIN";
static const char SEND_MESSAGE[] = "SEND_MSG";
static const char UPDATE_MESSAGE[] = "UPDT_MSG";
static const char DELETE_MESSAGE[] = "DEL_MSG";
static const char GET_NEW_MESSAGES[] = "GET_MSGS";
static const char IS_CONNECTED[] = "IS_CONN";

static const char* EXISTING_COMMANDS[] = {
    LOGIN,
    SEND_MESSAGE,
    UPDATE_MESSAGE,
    DELETE_MESSAGE,
    GET_NEW_MESSAGES,
    IS_CONNECTED
};

static const command_def_t COMMANDS_DEFINITIONS[] = {
    { "LOGIN", 1 }, // api_token
    { "SEND_MSG", 4 }, // token,message-length,receiver-id,content
    { "UPDT_MSG", 2 }, // token,message-id,content
    { "DEL_MSG", 1 }, // token,message-id
    { "GET_MSGS", 1 }, // token
    { "IS_CONN", 1 } // user-id
};

static const int COMMANDS_COUNT = sizeof(COMMANDS_DEFINITIONS) / sizeof(command_def_t);

static const user_t NOT_CONNECTED_USER = { 0, "", "" };

char* format_status(status_t status);
char* format_command(command_t command);
status_t* parse_status(char status_str[]);

// Check if a command is registered
int command_exist(char name[]);

// Command related functions
command_def_t get_command_def(char name[]);
char* get_command_param_value(command_t command, char name[]);
command_t create_command(const char* name);
void add_command_param(command_t* command, char name[], char value[]);

// Helper functions to send command to the server
// Used by the client
status_t* request(int sock, char buf[]);
status_t* send_login(int sock, char api_token[]);
status_t* send_message(int sock, char token[], char message[], int receiver_id);

#endif // PROTOCOL_H