#ifndef PROTOCOL_H
#define PROTOCOL_H

#define RESPONSE_OK "200/OK\n"
#define RESPONSE_DENIED "403/DENIED\n"
#define RESPONSE_UNAUTH "401/UNAUTH\n"
#define RESPONSE_MISFMT "416/MISFMT\n"
#define RESPONSE_TOOMRQ "426/TOOMRQ\n"

#define MAX_MESSAGE_SIZE 1000
typedef struct {
    char command[16]; 
    char token[64];
    char message[MAX_MESSAGE_SIZE]; 
} request_t;


void handle_request(int client_sock, PGconn *db_conn);
void process_login(int client_sock, PGconn *db_conn, const char *api_key);
void process_message(int client_sock, PGconn *db_conn, const char *token, const char *message);
void send_response(int client_sock, const char *response);

#endif // PROTOCOL_H