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

typedef struct {
    int id;
    char email[CHAR_SIZE];
    char api_token[API_TOKEN_SIZE];
} user_t;

typedef enum {
    MEMBER,
    PROFESSIONAL,
    ADMIN,
    UNKNOWN,
} user_type_t;

// Utils functions
void db_login(PGconn** conn);
void db_exit(PGconn* conn);

user_t init_user(int id, char email[], char api_token[]);
message_t init_message(int sender_id, int receiver_id, char content[]);

char* get_token_by_email(PGconn* conn, char email[]);
int db_get_user(PGconn* conn, user_t* user, int id);
int db_get_user_by_email(PGconn* conn, user_t* user, char email[]);
int db_get_user_by_api_token(PGconn* conn, user_t* user, char api_token[]);
user_type_t db_get_user_type(PGconn* conn, int id);

int db_get_message(PGconn* conn, int message_id, message_t* message);
void db_create_message(PGconn* conn, message_t* message);
void db_update_message(PGconn* conn, message_t* message);
void db_delete_message(PGconn* conn, int message_id);

#endif // DATABASE_H