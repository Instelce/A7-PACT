#ifndef DATABASE_H
#define DATABASE_H

#include <libpq-fe.h>

#include "types.h"

typedef struct
{
    int id;
    char sended_date[DATE_CHAR_SIZE];
    char modified_date[DATE_CHAR_SIZE];
    int sender_id;
    int receiver_id;
    int deleted;
    int seen;
    char content[LARGE_CHAR_SIZE];
} message_t;

typedef enum {
    MEMBER,
    PROFESSIONAL,
    ADMIN,
    UNKNOWN,
} user_type_t;

typedef struct {
    int id;
    // pseudo for member or denomination for professional
    char name[CHAR_SIZE];
    char email[CHAR_SIZE];
    char api_token[API_TOKEN_SIZE];
    user_type_t type;
} user_t;

typedef struct {
    user_t* users;
    int count;
} user_list_t;
typedef struct {
    message_t* messages;
    int count;
} message_list_t;

static const user_t NOT_CONNECTED_USER = { 0, "", "", "", UNKNOWN };

// Utils functions
void db_login(PGconn** conn);
void db_exit(PGconn* conn);

// Init functions
user_t init_user(int id, char email[], char api_token[]);
message_t init_message(int sender_id, int receiver_id, char content[]);

// User stuff
char* get_token_by_email(PGconn* conn, char email[]);
int db_get_user(PGconn* conn, user_t* user, int id);
int db_get_user_by_email(PGconn* conn, user_t* user, char email[]);
int db_get_user_by_api_token(PGconn* conn, user_t* user, char api_token[]);
int db_set_user_type(PGconn* conn, user_t* user);

// Message stuff
int db_get_message(PGconn* conn, int message_id, message_t* message);
void db_create_message(PGconn* conn, message_t* message);
void db_update_message(PGconn* conn, message_t* message);
void db_delete_message(PGconn* conn, int message_id);
user_list_t db_get_members(PGconn* conn, int offset, int limit);
user_list_t db_get_professionals(PGconn* conn, int offset, int limit);
message_list_t db_get_messages_by_sender(PGconn* conn, int sender_id, int offset, int limit);
message_list_t db_get_unread_messages(PGconn* conn, int receiver_id, int offset, int limit);

#endif // DATABASE_H