// Made with the help of https://www.postgresql.org/docs/current/libpq-example.html

#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

#include "database.h"


void db_login(PGconn *conn) {
    conn = PQsetdbLogin(getenv("DB_HOST"), getenv("DB_PORT"), NULL, NULL, getenv("DB_NAME"), getenv("DB_USER"), getenv("DB_PASSWORD"));

    if (PQstatus(conn) != CONNECTION_OK) {
        perror("Error when connecting the DB");
        db_exit(conn);
    }
}

void db_exit(PGconn *conn) {
    PQfinish(conn);
    exit(1);
}


user_t init_user(int id, char username[], char api_token[]) {
    user_t user;

    user.id = id;
    strcpy(user.username, username);
    strcpy(user.api_token, api_token);

    return user;
}

message_t init_message(int sender_id, int receiver_id, char content[]) {
    message_t message;

    // TODO initialize other field
    message.sender_id = sender_id;
    message.receiver_id = receiver_id;
    strcpy(message.content, content);

    return message;
}
