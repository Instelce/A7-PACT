#ifndef PROTOCOL_H
#define PROTOCOL_H

#include "types.h"

typedef struct {
    int code;
    char message[6];
} status_t;

typedef struct {
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

// Represent our protocol action
typedef struct {
    char name[CHAR_SIZE];
    // Array of string
    char ** params;
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

// All actions name
static const char LOGIN[] = "LOGIN";
static const char SEND_MESSAGE[] = "SEND_MSG";
static const char UPDATE_MESSAGE[] = "UPDT_MSG";
static const char DELETE_MESSAGE[] = "DEL_MSG";

// All actions definitions
static const command_def_t ACTIONS_DEF[] = {
    {"LOGIN", 1},
    {"SEND_MSG", 3},
    // {"LOGIN", {(sizeof(char) * 64)}},
    // {"SEND_MSG", {(sizeof(char) * 64), sizeof(int), -1}},
};

// s : SEND_MSG:azeazeaz,azeazeaz,azeazeazeazeaz,azeaze
// a : action
int parse_command(char s[], command_t *a);

command_def_t * get_action_def(char action_name[]);

char * format_status(status_t status);

#endif // PROTOCOL_H