// Made with the help of https://www.postgresql.org/docs/current/libpq-example.html

#include <libpq-fe.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include "database.h"
#include "utils.h"

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

char* db_bool(int value)
{
    return value ? "true" : "false";
}

int db_last_id(PGconn* conn, const char* table)
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT currval('%s_id_seq')", table);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching last inserted ID");
    }

    int id = atoi(PQgetvalue(res, 0, 0));

    PQclear(res);

    return id;
}

user_t init_user(int id, char email[], char api_token[])
{
    user_t user;

    user.id = id;
    strcpy(user.name, "");
    strcpy(user.email, email);
    strcpy(user.api_token, api_token);
    user.type = UNKNOWN;

    return user;
}

message_t init_message(int sender_id, int receiver_id, char content[])
{
    message_t message;
    time_t now = time(NULL);
    struct tm* tm = localtime(&now);

    set_date_now(message.sended_date);
    set_date_now(message.modified_date);
    message.sender_id = sender_id;
    message.receiver_id = receiver_id;
    message.deleted = 0;
    message.seen = 0;
    strcpy(message.content, content);

    return message;
}

int db_get_user(PGconn* conn, user_t* user, int id)
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
        return 0;
    }

    *user = init_user(atoi(PQgetvalue(res, 0, 0)), PQgetvalue(res, 0, 1), PQgetvalue(res, 0, 2));

    PQclear(res);

    return 1;
}

int db_get_user_by_email(PGconn* conn, user_t* user, char email[])
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
        return 0;
    }

    *user = init_user(atoi(PQgetvalue(res, 0, 0)), PQgetvalue(res, 0, 1), PQgetvalue(res, 0, 2));

    PQclear(res);

    return 1;
}

int db_get_user_by_api_token(PGconn* conn, user_t* user, char api_token[])
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT account_id, mail, api_token FROM user_account WHERE api_token = '%s'", api_token);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return 0;
    }

    *user = init_user(atoi(PQgetvalue(res, 0, 0)), PQgetvalue(res, 0, 1), PQgetvalue(res, 0, 2));

    PQclear(res);

    return 1;
}

int db_get_message(PGconn* conn, int message_id, message_t* message)
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT sended_date, modified_date, sender_id, receiver_id, deleted, seen, content FROM message WHERE id = %d", message_id);
    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching message");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return 0;
    }

    strcpy(message->sended_date, PQgetvalue(res, 0, 0));
    strcpy(message->modified_date, PQgetvalue(res, 0, 1));
    message->sender_id = atoi(PQgetvalue(res, 0, 2));
    message->receiver_id = atoi(PQgetvalue(res, 0, 3));
    message->deleted = atoi(PQgetvalue(res, 0, 4));
    message->seen = atoi(PQgetvalue(res, 0, 5));
    strcpy(message->content, PQgetvalue(res, 0, 6));

    PQclear(res);

    return 1;
}

void db_create_message(PGconn* conn, message_t* message)
{
    PGresult* res;
    char query[256];

    sprintf(query, "INSERT INTO message (sended_date, modified_date, sender_id, receiver_id, deleted, seen, content) VALUES ('%s', '%s', %d, %d, '%s', '%s', '%s')",
        message->sended_date, message->modified_date, message->sender_id, message->receiver_id, db_bool(message->deleted), db_bool(message->seen), message->content);

    // printf("%s\n", query);

    res = PQexec(conn, query);

    // printf("%s\n", PQresultErrorMessage(res));

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when creating message");
    }

    // printf("Last inserted ID: %d\n", db_last_id(conn, "message"));

    message->id = db_last_id(conn, "message");

    PQclear(res);
}

void db_update_message(PGconn* conn, message_t* message)
{
    PGresult* res;
    char query[256];

    set_date_now(message->modified_date);

    sprintf(query, "UPDATE message SET modified_date = '%s', deleted = '%s', seen = '%s', content = '%s' WHERE id = %d",
        message->modified_date, db_bool(message->deleted), db_bool(message->seen), message->content, message->id);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when updating message");
    }

    PQclear(res);
}

void db_delete_message(PGconn* conn, int message_id)
{
    PGresult* res;
    char query[256];

    sprintf(query, "UPDATE message SET deleted = true WHERE id = %d", message_id);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when deleting message");
    }

    PQclear(res);
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

int db_set_user_type(PGconn* conn, user_t* user)
{
    PGresult* res;
    char query[256];

    sprintf(query, "SELECT user_id, pseudo FROM member_user WHERE user_id = %d", user->id);
    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user type");
    }

    if (PQntuples(res) == 1) {
        user->type = MEMBER;
        strcpy(user->name, PQgetvalue(res, 0, 1));
        PQclear(res);
        return 1;
    }

    sprintf(query, "SELECT user_id, denomination FROM professional_user WHERE user_id = %d", user->id);
    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user type");
    }

    if (PQntuples(res) == 1) {
        user->type = PROFESSIONAL;
        strcpy(user->name, PQgetvalue(res, 0, 1));
        PQclear(res);
        return 1;
    }

    return 0;
}

user_list_t db_get_members(PGconn* conn, int offset, int limit)
{
   user_list_t user_list;
   PGresult* res;
   char query[256];
   sprintf(query,
       "SELECT ua.account_id, mu.pseudo, ua.mail, ua.api_token "
       "FROM user_account ua "
       "JOIN member_user mu ON ua.account_id = mu.user_id "
       "LIMIT %d OFFSET %d",
       limit, offset);


   res = PQexec(conn, query);


   if (PQresultStatus(res) != PGRES_TUPLES_OK) {
       db_error(conn, "Error when fetching members");
   }


   user_list.count = PQntuples(res);
   user_list.users = malloc(user_list.count * sizeof(user_t));


   for (int i = 0; i < user_list.count; i++) {
       user_list.users[i] = (user_t) {
           .id = atoi(PQgetvalue(res, i, 0)),
           .type = MEMBER,
       };
       strcpy(user_list.users[i].name, PQgetvalue(res, i, 1));
       strcpy(user_list.users[i].email, PQgetvalue(res, i, 2));
       strcpy(user_list.users[i].api_token, PQgetvalue(res, i, 3));
   }


   PQclear(res);


   return user_list;
}


user_list_t db_get_professionals(PGconn* conn, int offset, int limit)
{
   user_list_t user_list;
   PGresult* res;
   char query[256];


   sprintf(query,
       "SELECT ua.account_id, pu.denomination, ua.mail, ua.api_token "
       "FROM user_account ua "
       "JOIN professional_user pu ON ua.account_id = pu.user_id "
       "LIMIT %d OFFSET %d",
       limit, offset);


   res = PQexec(conn, query);


   if (PQresultStatus(res) != PGRES_TUPLES_OK) {
       db_error(conn, "Error when fetching professionals");
   }


   user_list.count = PQntuples(res);
   user_list.users = malloc(user_list.count * sizeof(user_t));


   for (int i = 0; i < user_list.count; i++) {
       user_list.users[i] = (user_t) {
           .id = atoi(PQgetvalue(res, i, 0)),
           .type = PROFESSIONAL,
       };
       strcpy(user_list.users[i].name, PQgetvalue(res, i, 1));
       strcpy(user_list.users[i].email, PQgetvalue(res, i, 2));
       strcpy(user_list.users[i].api_token, PQgetvalue(res, i, 3));
   }


   PQclear(res);


   return user_list;
}
