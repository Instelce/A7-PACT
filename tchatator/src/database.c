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
        fprintf(stderr, "Error when connecting the DB: %s\n", PQerrorMessage(*conn));
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

    snprintf(query, sizeof(query), "SELECT currval('%s_id_seq')", table);

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

    snprintf(query, sizeof(query), "SELECT account_id, mail, api_token FROM user_account WHERE account_id = %d", id);

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

    snprintf(query, sizeof(query), "SELECT account_id, mail, api_token FROM user_account WHERE mail = '%s'", email);

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

    snprintf(query, sizeof(query), "SELECT account_id, mail, api_token FROM user_account WHERE api_token = '%s'", api_token);

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

    snprintf(query, sizeof(query), "SELECT id, sended_date, modified_date, sender_id, receiver_id, deleted, seen, content FROM message WHERE id = %d", message_id);
    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching message");
    }

    if (PQntuples(res) == 0) {
        PQclear(res);
        return 0;
    }

    message->id = atoi(PQgetvalue(res, 0, 0));
    strcpy(message->sended_date, PQgetvalue(res, 0, 1));
    strcpy(message->modified_date, PQgetvalue(res, 0, 2));
    message->sender_id = atoi(PQgetvalue(res, 0, 3));
    message->receiver_id = atoi(PQgetvalue(res, 0, 4));
    message->deleted = atoi(PQgetvalue(res, 0, 5));
    message->seen = atoi(PQgetvalue(res, 0, 6));
    strcpy(message->content, PQgetvalue(res, 0, 7));

    PQclear(res);

    return 1;
}

void db_create_message(PGconn* conn, message_t* message)
{
    PGresult* res;

    char sender_id_str[20], receiver_id_str[20], deleted_str[10], seen_str[10];
    snprintf(sender_id_str, sizeof(sender_id_str), "%d", message->sender_id);
    snprintf(receiver_id_str, sizeof(receiver_id_str), "%d", message->receiver_id);
    snprintf(deleted_str, sizeof(deleted_str), "%d", message->deleted);
    snprintf(seen_str, sizeof(seen_str), "%d", message->seen);

    const char* paramValues[6] = { message->sended_date, sender_id_str, receiver_id_str, deleted_str, seen_str, message->content };
    int paramLengths[6] = { 0, 0, 0, 0, 0, 0 };
    int paramFormats[6] = { 0, 0, 0, 0, 0, 0 };

    res = PQexecParams(conn,
        "INSERT INTO message (sended_date, modified_date, sender_id, receiver_id, deleted, seen, content) VALUES ($1, NULL, $2, $3, $4, $5, $6)",
        6, NULL, paramValues, paramLengths, paramFormats, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when creating message");
    }

    message->id = db_last_id(conn, "message");

    PQclear(res);
}

void db_update_message(PGconn* conn, message_t* message)
{
    PGresult* res;
    char query[LARGE_CHAR_SIZE];

    set_date_now(message->modified_date);

    snprintf(query, sizeof(query), "UPDATE message SET modified_date = '%s', deleted = %s, seen = %s, content = '%s' WHERE id = %d",
        message->modified_date, db_bool(message->deleted), db_bool(message->seen), message->content, message->id);

    // printf("%s\n", query);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when updating message");
    }

    PQclear(res);
}

void db_delete_message(PGconn* conn, int message_id)
{
    PGresult* res;
    char query[CHAR_SIZE];

    snprintf(query, sizeof(query), "UPDATE message SET deleted = true WHERE id = %d", message_id);

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
    char query[CHAR_SIZE];

    snprintf(query, sizeof(query), "SELECT user_id, pseudo FROM member_user WHERE user_id = %d", user->id);
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

    snprintf(query, sizeof(query), "SELECT user_id, denomination FROM professional_user WHERE user_id = %d", user->id);
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

    snprintf(query, sizeof(query), "SELECT user_id, pseudo FROM administrator_user WHERE user_id = %d", user->id);
    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching user type");
    }

    if (PQntuples(res) == 1) {
        user->type = ADMIN;
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
    char query[CHAR_SIZE];
    snprintf(query, sizeof(query),
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
    char query[CHAR_SIZE];

    snprintf(query, sizeof(query),
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

message_list_t db_get_messages_between_users(PGconn* conn, int user1, int user2, int offset, int limit)
{
    PGresult* res;
    message_list_t messages_list;
    char query[CHAR_SIZE];

    snprintf(query, sizeof(query), "SELECT id, sended_date, modified_date, sender_id, receiver_id, deleted, seen, content FROM message WHERE ((sender_id = %d AND receiver_id = %d) OR (sender_id = %d AND receiver_id = %d)) AND deleted = false ORDER BY sended_date DESC LIMIT %d OFFSET %d", user1, user2, user2, user1, limit, offset);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching messages");
    }

    messages_list.count = PQntuples(res);
    messages_list.messages = malloc(messages_list.count * sizeof(message_t));

    for (int i = 0; i < messages_list.count; i++) {
        messages_list.messages[i].id = atoi(PQgetvalue(res, i, 0));
        strcpy(messages_list.messages[i].sended_date, PQgetvalue(res, i, 1));
        strcpy(messages_list.messages[i].modified_date, PQgetvalue(res, i, 2));
        messages_list.messages[i].sender_id = atoi(PQgetvalue(res, i, 3));
        messages_list.messages[i].receiver_id = atoi(PQgetvalue(res, i, 4));
        messages_list.messages[i].deleted = atoi(PQgetvalue(res, i, 5));
        messages_list.messages[i].seen = atoi(PQgetvalue(res, i, 6));
        strcpy(messages_list.messages[i].content, PQgetvalue(res, i, 7));
    }

    PQclear(res);

    return messages_list;
}

user_list_t db_get_all_receiver_users_of_user(PGconn* conn, int user_id)
{
    PGresult* res;
    user_list_t user_list;
    char query[CHAR_SIZE];

    snprintf(query, sizeof(query), "SELECT DISTINCT sender_id, receiver_id FROM message WHERE sender_id = %d OR receiver_id = %d", user_id, user_id);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching discussion users");
    }

    user_list.users = NULL;
    user_list.count = 0;

    for (int i = 0; i < PQntuples(res); i++) {
        int sender_id = atoi(PQgetvalue(res, i, 0));
        int receiver_id = atoi(PQgetvalue(res, i, 1));

        if (receiver_id == user_id) {
            if (!user_list_contains(user_list, sender_id)) {
                user_list.count++;
                user_list.users = realloc(user_list.users, user_list.count * sizeof(user_t));
                user_list.users[user_list.count - 1] = init_user(receiver_id, "", "");
                db_get_user(conn, &user_list.users[user_list.count - 1], sender_id);
            }
        } else {
            if (!user_list_contains(user_list, receiver_id)) {
                user_list.count++;
                user_list.users = realloc(user_list.users, user_list.count * sizeof(user_t));
                user_list.users[user_list.count - 1] = init_user(receiver_id, "", "");
                db_get_user(conn, &user_list.users[user_list.count - 1], receiver_id);
            }
        }
    }

    PQclear(res);

    return user_list;
}

int user_list_contains(user_list_t user_list, int user_id)
{
    for (int i = 0; i < user_list.count; i++) {
        if (user_list.users[i].id == user_id) {
            return 1;
        }
    }

    return 0;
}

message_list_t db_get_messages_by_sender(PGconn* conn, int sender_id, int offset, int limit)
{
    message_list_t message_list;
    PGresult* res;
    char query[256];

    snprintf(query, sizeof(query),
        "SELECT id, sended_date, modified_date, receiver_id, deleted, seen, content "
        "FROM message "
        "WHERE sender_id = %d "
        "LIMIT %d OFFSET %d",
        sender_id, limit, offset);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching messages by sender");
    }

    message_list.count = PQntuples(res);
    message_list.messages = malloc(message_list.count * sizeof(message_t));

    for (int i = 0; i < message_list.count; i++) {
        message_list.messages[i] = (message_t) {
            .id = atoi(PQgetvalue(res, i, 0)),
        };
        strcpy(message_list.messages[i].sended_date, PQgetvalue(res, i, 1));
        strcpy(message_list.messages[i].modified_date, PQgetvalue(res, i, 2));
        message_list.messages[i].receiver_id = atoi(PQgetvalue(res, i, 3));
        message_list.messages[i].deleted = atoi(PQgetvalue(res, i, 4));
        message_list.messages[i].seen = atoi(PQgetvalue(res, i, 5));
        strcpy(message_list.messages[i].content, PQgetvalue(res, i, 6));
    }

    PQclear(res);

    return message_list;
}

message_list_t db_get_unread_messages(PGconn* conn, int receiver_id, int offset, int limit)
{
    message_list_t message_list;
    PGresult* res;
    char query[512];

    snprintf(query, sizeof(query),
        "SELECT id, sended_date, modified_date, sender_id, deleted, seen, content "
        "FROM message "
        "WHERE receiver_id = %d AND seen = false AND deleted = false "
        "ORDER BY sended_date DESC "
        "LIMIT %d OFFSET %d",
        receiver_id, limit, offset);

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error when fetching unread messages");
    }

    message_list.count = PQntuples(res);
    message_list.messages = malloc(message_list.count * sizeof(message_t));

    for (int i = 0; i < message_list.count; i++) {
        message_list.messages[i].id = atoi(PQgetvalue(res, i, 0));
        strcpy(message_list.messages[i].sended_date, PQgetvalue(res, i, 1));
        strcpy(message_list.messages[i].modified_date, PQgetvalue(res, i, 2));
        message_list.messages[i].sender_id = atoi(PQgetvalue(res, i, 3));
        message_list.messages[i].deleted = atoi(PQgetvalue(res, i, 4));
        message_list.messages[i].seen = atoi(PQgetvalue(res, i, 5));
        strcpy(message_list.messages[i].content, PQgetvalue(res, i, 6));
    }

    PQclear(res);

    return message_list;
}

user_list_t db_get_users_who_sent_messages(PGconn* conn)
{
    PGresult* res;
    user_list_t user_list = { 0 };
    const char* query = "SELECT DISTINCT m.sender_id "
                        "FROM message m "
                        "JOIN member_user mu ON m.sender_id = mu.user_id;";

    res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Erreur lors de la récupération des expéditeurs");
    }

    user_list.count = PQntuples(res);
    user_list.users = malloc(user_list.count * sizeof(user_t));

    for (int i = 0; i < user_list.count; i++) {
        int sender_id = atoi(PQgetvalue(res, i, 0));
        db_get_user(conn, &user_list.users[i], sender_id);
        db_set_user_type(conn, &user_list.users[i]);
    }

    PQclear(res);
    return user_list;
}

void add_message(message_list_t* messages, message_t message)
{
    if (messages->messages == NULL) {
        messages->messages = malloc(sizeof(message_t));
        messages->count = 0;
    } else {
        messages->messages = realloc(messages->messages, (messages->count + 1) * sizeof(message_t));
    }

    messages->messages[messages->count] = message;
    messages->count++;
}

void remove_message(message_list_t* messages, int message_id)
{
    for (int i = 0; i < messages->count; i++) {
        if (messages->messages[i].id == message_id) {
            for (int j = i; j < messages->count - 1; j++) {
                messages->messages[j] = messages->messages[j + 1];
            }
            messages->count--;
            break;
        }
    }
}

void db_get_banned_users(PGconn* conn, int** user_ids, int* count)
{
    PGresult* res;
    const char* query = "SELECT user_id FROM banned_user";

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error fetching banned users");
    }

    *count = PQntuples(res);
    if (*count == 0) {
        *user_ids = NULL;
        PQclear(res);
        return;
    }

    *user_ids = malloc(*count * sizeof(int));
    if (!*user_ids) {
        db_error(conn, "Memory allocation error");
    }

    for (int i = 0; i < *count; i++) {
        (*user_ids)[i] = atoi(PQgetvalue(res, i, 0));
    }

    PQclear(res);
}
void db_ban_user(PGconn* conn, int user_id)
{
    PGresult* res;
    char query[256];

    snprintf(query, sizeof(query), "INSERT INTO banned_user (user_id) VALUES (%d) ON CONFLICT (user_id) DO NOTHING", user_id);

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when banning user");
    }

    PQclear(res);
}

void db_unban_user(PGconn* conn, int user_id)
{
    PGresult* res;
    char query[256];

    snprintf(query, sizeof(query), "DELETE FROM banned_user WHERE user_id = %d", user_id);

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when unbanning user");
    }

    PQclear(res);
}

void db_clean_expired_blocks(PGconn* conn)
{
    PGresult* res;
    const char* query = "DELETE FROM blocked_user WHERE NOW() > blocked_date + (duration_seconds || ' seconds')::INTERVAL";

    res = PQexec(conn, query);
    PQclear(res);
}

void db_get_blocked_users(PGconn* conn, blocked_user_t** blocked_users, int* count)
{
    PGresult* res;
    const char* query = "SELECT user_id, for_user_id FROM blocked_user WHERE NOW() < blocked_date + (duration_seconds || ' seconds')::INTERVAL";

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        db_error(conn, "Error fetching blocked users");
    }

    *count = PQntuples(res);
    if (*count == 0) {
        *blocked_users = NULL;
        PQclear(res);
        return;
    }

    *blocked_users = malloc(*count * sizeof(blocked_user_t));
    if (!*blocked_users) {
        db_error(conn, "Memory allocation error");
    }

    for (int i = 0; i < *count; i++) {
        (*blocked_users)[i].user_id = atoi(PQgetvalue(res, i, 0));
        (*blocked_users)[i].for_user_id = atoi(PQgetvalue(res, i, 1));
    }

    PQclear(res);
}

void db_block_user(PGconn* conn, int user_id, int for_user_id, int duration_seconds)
{
    PGresult* res;
    char query[256];

    snprintf(query, sizeof(query),
        "INSERT INTO blocked_user (user_id, for_user_id, duration_seconds, blocked_date) VALUES (%d, %d, %d, NOW()) "
        "ON CONFLICT (user_id, for_user_id) DO UPDATE SET duration_seconds = %d, blocked_date = NOW()",
        user_id, for_user_id, duration_seconds, duration_seconds);

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when blocking user");
    }

    PQclear(res);
}

void db_unblock_user(PGconn* conn, int user_id, int for_user_id)
{
    PGresult* res;
    char query[256];

    snprintf(query, sizeof(query), "DELETE FROM blocked_user WHERE user_id = %d AND for_user_id = %d", user_id, for_user_id);

    res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        db_error(conn, "Error when unblocking user");
    }

    PQclear(res);
}
