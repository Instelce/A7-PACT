#include <json-c/json.h>
#include <stdarg.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <ws.h>

#include "config.h"
#include "database.h"
#include "log.h"
#include "protocol.h"

typedef struct {
    user_t user;
    ws_cli_conn_t conn;
    bool is_writing;
    int in_conversation_with; // user id
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
message_list_t get_incoming_messages(user_t user)
{
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
 * @brief Check if a user is connected.
 * 
 * @param user_id User.
 */
int is_user_connected(int user_id)
{
    for (int i = 0; i < clients_count; i++) {
        if (clients[i].user.id == user_id) {
            return 1;
        }
    }
    return 0;
}

/**
 * @brief Convert a list of message to a JSON string.
 *
 * @return JSON string.
 */
char* json_message_list(message_list_t list)
{
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
 * @brief Send a json object to a client.
 *
 * @param client_conn Client connection.
 * @param command_name Command that the server responds to.
 * @param ... All the message parameters. Alternate between the key and the value. The last argument must be NULL.
 */
void send_json(ws_cli_conn_t client_conn, char command_name[], ...)
{
    json_object* jobj = json_object_new_object();
    va_list args;
    char* key;
    char* value;

    // Add the command
    json_object_object_add(jobj, "command", json_object_new_string(command_name));

    va_start(args, command_name);

    while ((key = va_arg(args, char*)) != NULL) {
        if ((value = va_arg(args, char*)) == NULL) {
            break;
        }

        json_object_object_add(jobj, key, json_object_new_string(value));
    }

    va_end(args);

    // Send
    ws_sendframe_txt(client_conn, json_object_to_json_string(jobj));
}

/**
 * @brief Get client data from a connection.
 *
 * @param conn Connection.
 * @return Client.
 */
client_t* get_client_with_conn(ws_cli_conn_t conn)
{
    for (int i = 0; i < clients_count; i++) {
        if (clients[i].conn == conn) {
            return &clients[i];
        }
    }
    return NULL;
}

/**
 * @brief Get a client data from a user id.
 *
 * @param user_id User id.
 * @return Client.
 */
client_t* get_client_with_user_id(int user_id)
{
    for (int i = 0; i < clients_count; i++) {
        if (clients[i].user.id == user_id) {
            return &clients[i];
        }
    }
    return NULL;
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

    client_t* client = get_client_with_conn(client_conn);
    int is_connected = memcmp(&client->user, &NOT_CONNECTED_USER, sizeof(user_t)) != 0;

    // printf("Is connected: %d\n", is_connected);

    // Parse the message
    json_object* jobj = parse_message((char*)msg);

    // Get the command name
    json_object* jcmd;
    if (!json_object_object_get_ex(jobj, "command", &jcmd)) {
        fprintf(stderr, "Error: command not found\n");
        return;
    }
    const char* cmd = json_object_get_string(jcmd);

    // printf("Command: %s\n", cmd);

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
		// - receiver
		// - content
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

		// Update message
		// - message_id
		if (strcmp(cmd, UPDATE_MESSAGE) == 0) {
			// Get the message id
			json_object* jmessage_id;
			if (!json_object_object_get_ex(jobj, "message_id", &jmessage_id)) {
				fprintf(stderr, "Error: message_id not found\n");
				return;
			}
			int message_id = json_object_get_int(jmessage_id);

			// Get the new message content
			json_object* jcontent;
			if (!json_object_object_get_ex(jobj, "content", &jcontent)) {
				fprintf(stderr, "Error: content not found\n");
				return;
			}
			const char* content = json_object_get_string(jcontent);

			// Get the message
			message_t message;
			db_get_message(conn, message_id, &message);

			// Update the message
			strcpy(message.content, content);
			db_update_message(conn, &message);
            log_info("Message updated succesfuly: %s", message.content);
		}

		// Delete message
		// - message_id
		if (strcmp(cmd, DELETE_MESSAGE) == 0) {
			// Get the message id
			json_object* jmessage_id;
			if (!json_object_object_get_ex(jobj, "message_id", &jmessage_id)) {
				fprintf(stderr, "Error: message_id not found\n");
				return;
			}
			int message_id = json_object_get_int(jmessage_id);

			// Delete the message
			db_delete_message(conn, message_id);
		}

        // New message available
        // Send all available messages to the client
        if (strcmp(cmd, NEW_MESSAGE_AVAILABLE) == 0) {
            message_list_t messages = get_incoming_messages(client->user);

            // Send the messages
            char* json = json_message_list(messages);
            ws_sendframe_txt(client_conn, json);
        }

        // The client use this command to get information about another user
		// - user_id
        if (strcmp(cmd, USER_INFO) == 0) {
            // Get user id
            json_object* juser_id;
            if (!json_object_object_get_ex(jobj, "user_id", &juser_id)) {
                fprintf(stderr, "Error: user_id not found\n");
                return;
            }
            int user_id = json_object_get_int(juser_id);

            // printf("User id: %d\n", user_id);

            client_t* target_client = get_client_with_user_id(user_id);

            if (target_client == NULL) {
                send_json(client_conn, cmd, "connected", "false", NULL);
            } else {
                // printf("Target client: %d %d\n", target_client->user.id, target_client->in_conversation_with);
                char* is_connect = is_user_connected(user_id) ? "true" : "false";
                char* is_writing = (target_client->is_writing && target_client->in_conversation_with == client->user.id) ? "true" : "false";
                char* in_conversation = target_client->in_conversation_with == client->user.id ? "true" : "false";

                send_json(client_conn, cmd, "connected", is_connect, "is_writing", is_writing, "viewed_last_message", in_conversation, NULL);
            }
        }

		// Client info
		if (strcmp(cmd, CLIENT_INFO) == 0) {
			// Get the client info
			json_object* jis_writing;
			if (!json_object_object_get_ex(jobj, "is_writing", &jis_writing)) {
				fprintf(stderr, "Error: is_writing not found\n");
				return;
			}
			bool is_writing = json_object_get_boolean(jis_writing);

			json_object* jin_conversation;
			if (!json_object_object_get_ex(jobj, "in_conversation_with", &jin_conversation)) {
				fprintf(stderr, "Error: in_conversation_with not found\n");
				return;
			}

			int in_conversation = json_object_get_int(jin_conversation);

            // printf("%d In conversation with: %d\n", client->user.id, in_conversation);

            client->is_writing = is_writing;

			if (in_conversation) {
                client->in_conversation_with = in_conversation;
			} else {
				client->in_conversation_with = -1;
			}
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
    // ws_sendframe_bcast(8080, (char*)msg, size, type);
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
