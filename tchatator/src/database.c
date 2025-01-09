// Made with the help of https://www.postgresql.org/docs/current/libpq-example.html

#include <libpq-fe.h>
#include <stdlib.h>
#include <string.h>

#include "database.h"

void db_login(PGconn** conn)
{
    *conn = PQsetdbLogin(getenv("DB_HOST"), getenv("DB_PORT"), NULL, NULL, getenv("DB_NAME"), getenv("DB_USER"), getenv("DB_PASSWORD"));

    if (PQstatus(*conn) != CONNECTION_OK) {
        perror("Error when connecting the DB");
        db_exit(*conn);
    }
}

void db_exit(PGconn* conn)
{
    PQfinish(conn);
    exit(1);
}

void db_error(PGconn* conn, const char* message)
{
    fprintf(stderr, "%s: %s\n", message, PQerrorMessage(conn));
    db_exit(conn);
}

user_t init_user(int id, char email[], char api_token[])
{
    user_t user;

    user.id = id;
    strcpy(user.email, email);
    strcpy(user.api_token, api_token);

    return user;
}

message_t init_message(int sender_id, int receiver_id, char content[])
{
    message_t message;

    // TODO initialize other field
    message.sender_id = sender_id;
    message.receiver_id = receiver_id;
    strcpy(message.content, content);

    return message;
}

int get_user(PGconn* conn, user_t* user, int id)
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT account_id, mail, api_token FROM user_account WHERE account_id = %d", id);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return -1;
    }

    *user = init_user(atoi(PQgetvalue(res, 0, 0)), PQgetvalue(res, 0, 1), PQgetvalue(res, 0, 2));

    PQclear(res);

    return 0;
}

int get_user_by_email(PGconn* conn, user_t* user, char email[])
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT account_id, mail, api_token FROM user_account WHERE mail = '%s'", email);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return -1;
    }

    *user = init_user(atoi(PQgetvalue(res, 0, 0)), PQgetvalue(res, 0, 1), PQgetvalue(res, 0, 2));

    PQclear(res);

    return 0;
}

char* get_token_by_email(PGconn* conn, char email[])
{
    PGresult* res;
    const char* paramValues[1] = { email };

    res = PQexecParams(conn,
        "SELECT api_token FROM user_account WHERE mail = $1",
        1, NULL, paramValues, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching token by email");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return NULL;
    }

    char* token = strdup(PQgetvalue(res, 0, 0));
    PQclear(res);
    return token;
}