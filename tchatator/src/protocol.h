#ifndef PROTOCOL_H
#define PROTOCOL_H

#include <sys/socket.h>

#include "database.h"
#include "types.h"

typedef struct
{
    int code;
    char message[6];
} response_status_t;

typedef struct
{
    char name[CHAR_SIZE];
    char value[LARGE_CHAR_SIZE];
} response_data_t;

typedef struct
{
    response_status_t status;
    response_data_t* data_list;
    int data_list_size;
} response_t;

typedef struct
{
    char name[CHAR_SIZE];
    char value[LARGE_CHAR_SIZE];
} command_param_t;

// Represent our protocol action / command
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
static const response_status_t STATUS_OK = { 200, "OK" };
static const response_status_t STATUS_DENIED = { 403, "DENIED" };
static const response_status_t STATUS_UNAUTHORIZED = { 401, "UNAUTH" };
static const response_status_t STATUS_MIS_FORMAT = { 416, "MISFMT" };
static const response_status_t STATUS_TOO_MESSAGE_RECEIVED = { 426, "TOOMRQ" };

// All commands name
static const char LOGIN[] = "LOGIN";
static const char SEND_MESSAGE[] = "SEND_MSG";
static const char UPDATE_MESSAGE[] = "UPDT_MSG";
static const char DELETE_MESSAGE[] = "DEL_MSG";
static const char DISCONNECTED[] = "DISCONNECTED";

/// Client check if a change is available
/// - new message
/// - message updated
/// - message deleted
/// - message seen (not implemented)
static const char NEW_CHANGE_AVAILABLE[] = "NEW_CHG_AVAILABLE";

/// Send info for a specific user
/// - connection status
/// - is writing a new message
/// - viewed last message
static const char USER_INFO[] = "USER_INFO";

/// Send info to the server
/// - client is writing a new message to a user
/// - present in a discussion with a user
static const char CLIENT_INFO[] = "CLIENT_INFO";

/// Block a user
/// - user_id to block
/// - for_user_id the user that block (0 for all users)
static const char BLOCK_USER[] = "BLOCK_USER";

static const char BAN_USER[] = "BAN_USER";

static const char* EXISTING_COMMANDS[] = {
    LOGIN,
    SEND_MESSAGE,
    UPDATE_MESSAGE,
    DELETE_MESSAGE,
    NEW_CHANGE_AVAILABLE,
    DISCONNECTED,
    USER_INFO,
    CLIENT_INFO,
    BLOCK_USER,
    BAN_USER,
};

static const command_def_t COMMANDS_DEFINITIONS[] = {
    { "LOGIN", 1 }, // api-token
    { "SEND_MSG", 4 }, // token,receiver-id,message-length,content
    { "UPDT_MSG", 4 }, // token,message-id,message-length,content
    { "DEL_MSG", 2 }, // token,message-id
    { "NEW_CHG_AVAILABLE", 1 }, // token
    { "USER_INFO", 1 }, // token,user-id
    { "CLIENT_INFO", 3 }, // token,is-writing,in-conversation
    { "DISCONNECTED", 0 },
    { "BLOCK_USER", 3 }, // token,user-id,for-user-id
    { "BAN_USER", 2 }, // token,user-id
};

static const int COMMANDS_COUNT = sizeof(COMMANDS_DEFINITIONS) / sizeof(command_def_t);

char* format_response(response_t response);
char* format_status(response_status_t status);
char* format_command(command_t command);
response_status_t* parse_status(char status_str[]);
response_t* parse_response(char response_str[]);

// Response function
response_t create_response(response_status_t status);
void add_response_data(response_t* response, char name[], char value[]);
char* get_response_data(response_t response, char name[]);

// Check if a command is registered
int command_exist(char name[]);

// Command related functions
command_t create_command(const char* name);
void add_command_param(command_t* command, char name[], char value[]);
command_def_t get_command_def(const char* name);
char* get_command_param_value(command_t command, char name[]);

// Helper functions to send command to the server
// Used by the client
response_t* request(int sock, char buf[]);
response_t* send_login(int sock, char api_token[]);
response_t* send_message(int sock, char token[], char message[], int receiver_id);
response_t* send_update_message(int sock, char token[], int message_id, char message[]);
response_t* send_delete_message(int sock, char token[], int message_id);
// response_t* send_get_new_message(int sock, char token[]);
response_t* send_block_user(int sock, char token[], int user_id, int for_user_id, int duration);
response_t* send_ban_user(int sock, char token[], int user_id);
void send_disconnected(int sock);

#endif // PROTOCOL_H