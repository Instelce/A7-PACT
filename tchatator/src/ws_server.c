#include <json-c/json.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <ws.h>

#include "log.h"
#include "protocol.h"
#include "database.h"
#include "config.h"

typedef struct {
	user_t user;
	ws_cli_conn_t conn;
} client_t;

PGconn* conn;

// All connected clients
client_t* clients;
int clients_count;

// All messages received by the server during the session
message_list_t incoming_messages;

/**
 * @brief Get incoming messages for a user.
 * 
 * @return List of messages.
 */
message_list_t get_incoming_messages(user_t user) {
	message_list_t messages;
	messages.count = 0;
	messages.messages = (message_t*)malloc(20 * sizeof(message_t));

	for (int i = 0; i < incoming_messages.count; i++) {
		if (incoming_messages.messages[i].receiver_id == user.id) {
			messages.messages[messages.count] = incoming_messages.messages[i];
			messages.count++;
		}
	}

	return messages;
}

/**
 * @brief Convert a list of message to a JSON string.
 * 
 * @return JSON string.
 */
char* json_message_list(message_list_t list) {
	json_object* jobj = json_object_new_object();
	json_object* jarray = json_object_new_array();

	for (int i = 0; i < list.count; i++) {
		json_object* jmessage = json_object_new_object();
		json_object_object_add(jmessage, "id", json_object_new_int(list.messages[i].id));
		json_object_object_add(jmessage, "sended_date", json_object_new_string(list.messages[i].sended_date));
		json_object_object_add(jmessage, "modified_date", json_object_new_string(list.messages[i].modified_date));
		json_object_object_add(jmessage, "sender_id", json_object_new_int(list.messages[i].sender_id));
		json_object_object_add(jmessage, "receiver_id", json_object_new_int(list.messages[i].receiver_id));
		json_object_object_add(jmessage, "deleted", json_object_new_int(list.messages[i].deleted));
		json_object_object_add(jmessage, "seen", json_object_new_int(list.messages[i].seen));
		json_object_object_add(jmessage, "content", json_object_new_string(list.messages[i].content));

		json_object_array_add(jarray, jmessage);
	}

	json_object_object_add(jobj, "messages", jarray);

	return strdup(json_object_to_json_string(jobj));
}

/**
 * @brief Get a client_conn from a connection.
 * 
 * @param conn Connection.
 * @return Client.
 */
client_t* get_client(ws_cli_conn_t conn) {
	for (int i = 0; i < clients_count; i++) {
		if (clients[i].conn == conn) {
			return &clients[i];
		}
	}
}

/**
 * @brief Parse a JSON message.
 *
 * @param msg JSON message.
 * @return JSON object.
 */
json_object* parse_message(char* msg)
{
    json_object* jobj = json_tokener_parse(msg);
    if (jobj == NULL) {
        fprintf(stderr, "Error parsing JSON message\n");
        exit(1);
    }
    return jobj;
}

/**
 * @brief Called when a client_conn connects to the server.
 *
 * @param client_conn Client connection. The @p client_conn parameter is used
 * in order to send messages and retrieve informations about the
 * client_conn.
 */
void onopen(ws_cli_conn_t client_conn)
{
    char *cli, *port;
    cli = ws_getaddress(client_conn);
    port = ws_getport(client_conn);
    log_info("Connection opened, addr: %s, port: %s", cli, port);

	// Add the client_conn to the list
	clients[clients_count].conn = client_conn;
	clients[clients_count].user = NOT_CONNECTED_USER;
	clients_count++;
}

/**
 * @brief Called when a client_conn disconnects to the server.
 *
 * @param client_conn Client connection. The @p client_conn parameter is used
 * in order to send messages and retrieve informations about the
 * client_conn.
 */
void onclose(ws_cli_conn_t client_conn)
{
    char* cli;
    cli = ws_getaddress(client_conn);
    log_info("Connection closed, addr: %s", cli);
}

/**
 * @brief Called when a client_conn connects to the server.
 *
 * @param client_conn Client connection. The @p client_conn parameter is used
 * in order to send messages and retrieve informations about the
 * client_conn.
 *
 * @param msg Received message, this message can be a text
 * or binary message.
 *
 * @param size Message size (in bytes).
 *
 * @param type Message type.
 */
void onmessage(ws_cli_conn_t client_conn,
    const unsigned char* msg, uint64_t size, int type)
{
    char* cli;
    cli = ws_getaddress(client_conn);
    log_info("I receive a message: %s (size: %" PRId64 ", type: %d), from: %s",
        msg, size, type, cli);

	client_t* client = get_client(client_conn);
    int is_connected = memcmp(&client->user, &NOT_CONNECTED_USER, sizeof(user_t)) != 0;

	printf("Is connected: %d\n", is_connected);

	// Parse the message
	json_object* jobj = parse_message((char*)msg);

	// Get the command name
	json_object* jcmd;
	if (!json_object_object_get_ex(jobj, "command", &jcmd)) {
		fprintf(stderr, "Error: command not found\n");
		return;
	}
	const char* cmd = json_object_get_string(jcmd);

	printf("Command: %s\n", cmd);

	// Login command
	if (strcmp(cmd, LOGIN) == 0 && !is_connected) {
		// Get the api token
		json_object* jtoken;
		if (!json_object_object_get_ex(jobj, "token", &jtoken)) {
			fprintf(stderr, "Error: token not found\n");
			return;
		}

		const char* token = json_object_get_string(jtoken);

		// Get the user data
		db_get_user_by_api_token(conn, &client->user, token);
		db_set_user_type(conn, &client->user);
	}

	if (is_connected) {
		// All commands here require a token
		json_object* jtoken;
		if (!json_object_object_get_ex(jobj, "token", &jtoken)) {
			fprintf(stderr, "Error: token not found\n");
			return;
		}
		const char* token = json_object_get_string(jtoken);

		// Check if the token is valid
		if (strcmp(token, client->user.api_token) != 0) {
			fprintf(stderr, "Error: invalid token\n");
			return;
		}

		// Send message
		if (strcmp(cmd, SEND_MESSAGE) == 0) {
			// Get the message content
			json_object* jcontent;
			if (!json_object_object_get_ex(jobj, "content", &jcontent)) {
				fprintf(stderr, "Error: content not found\n");
				return;
			}
			const char* content = json_object_get_string(jcontent);

			// Get the message receiver
			json_object* jreceiver;
			if (!json_object_object_get_ex(jobj, "receiver", &jreceiver)) {
				fprintf(stderr, "Error: receiver not found\n");
				return;
			}
			const char* receiver = json_object_get_string(jreceiver);

			// Create the message in the DB
			message_t message;
			message = init_message(client->user.id, atoi(receiver), content);
			db_create_message(conn, &message);
		}

		// New message available
		// Send all available messages to the client
		if (strcmp(cmd, NEW_MESSAGE_AVAILABLE) == 0) {
			message_list_t messages = get_incoming_messages(client->user);

			// Send the messages
			char* json = json_message_list(messages);
			ws_sendframe_txt(client_conn, json);
		}
	}

    /**
     * Mimicks the same frame type received and re-send it again
     *
     * Please note that we could just use a ws_sendframe_txt()
     * or ws_sendframe_bin() here, but we're just being safe
     * and re-sending the very same frame type and content
     * again.
     *
     * Alternative functions:
     *   ws_sendframe()
     *   ws_sendframe_txt()
     *   ws_sendframe_txt_bcast()
     *   ws_sendframe_bin()
     *   ws_sendframe_bin_bcast()
     */
    ws_sendframe_bcast(8080, (char*)msg, size, type);
}

/**
 * @brief Main routine.
 *
 * @note After invoking @ref ws_socket, this routine never returns,
 * unless if invoked from a different thread.
 */
int main(void)
{
	// Load environment variables
	env_load("..");

	// Setup log
	log_verbose = 1;
	strcpy(log_file_path, "tchatator.log");

	// Setup database
	db_login(&conn);

	// Setup clients
	clients = (client_t*)malloc(20 * sizeof(client_t));
	clients_count = 0;

    ws_socket(&(struct ws_server) {
        .host = "0.0.0.0",
        .port = 4242,
        .thread_loop = 0,
        .timeout_ms = 1000,
        .evs.onopen = &onopen,
        .evs.onclose = &onclose,
        .evs.onmessage = &onmessage });

    /*
     * If you want to execute code past ws_socket(), set
     * .thread_loop to '1'.
     */

    return (0);
}
