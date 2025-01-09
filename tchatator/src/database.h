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
    int received;
    int sended;
    char content[LARGE_CHAR_SIZE];
} message_t;


typedef struct {
    int id;
    char username[CHAR_SIZE];
    char api_token[API_TOKEN_SIZE];
} user_t;


// Utils functions
void db_login(PGconn *conn);
void db_exit(PGconn *conn);


user_t init_user(int id, char username[], char api_token[]);
message_t init_message(int sender_id, int receiver_id, char content[]);

#endif // DATABASE_H