#include <string.h>
#include <ctype.h>
#include <openssl/sha.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/socket.h>

#include "types.h"
#include "websocket.h"
#include "crypto.h"

int is_ws_handshake(char request[]) {
    return strstr(request, "Upgrade: websocket") && strstr(request, "Connection: Upgrade");
}

int ws_parse_handshake_request(char request[], handshake_request_t* handshake_request)
{
    sscanf(request, "%s %s %s", handshake_request->method, handshake_request->path, handshake_request->http_version);

    char* header_start = strstr(request, "\r\n") + 2;
    while (header_start && *header_start != '\0') {
        char* line_end = strstr(header_start, "\r\n");
        if (!line_end)
            break;

        char header_line[LARGE_CHAR_SIZE];
        strncpy(header_line, header_start, line_end - header_start);
        header_line[line_end - header_start] = '\0';

        if (strncmp(header_line, "Host: ", 6) == 0) {
            sscanf(header_line + 6, "%s", handshake_request->host);
        } else if (strncmp(header_line, "Upgrade: ", 9) == 0) {
            sscanf(header_line + 9, "%s", handshake_request->upgrade);
        } else if (strncmp(header_line, "Connection: ", 12) == 0) {
            sscanf(header_line + 12, "%s", handshake_request->connection);
        } else if (strncmp(header_line, "Sec-WebSocket-Key: ", 19) == 0) {
            sscanf(header_line + 19, "%s", handshake_request->sec_websocket_key);
        } else if (strncmp(header_line, "Sec-WebSocket-Protocol: ", 24) == 0) {
            sscanf(header_line + 24, "%s", handshake_request->sec_websocket_protocol);
        } else if (strncmp(header_line, "Sec-WebSocket-Version: ", 23) == 0) {
            sscanf(header_line + 23, "%s", handshake_request->sec_websocket_version);
        }

        // Next line
        header_start = line_end + 2;
    }

    return 0;
}


int ws_send_handshake(int sock, handshake_request_t* handshake_request) {
    // Concatenate with GUID and compute SHA1
    char concat_key[LARGE_CHAR_SIZE];
    snprintf(concat_key, sizeof(concat_key), "%s%s", handshake_request->sec_websocket_key, WEBSOCKET_GUID);
    char sha1_result[20];
    sha1(concat_key, sha1_result);

    // Base64 encode the result
    base64_encode((unsigned char *)sha1_result, 20, handshake_request->sec_websocket_accept);

    // Send the response
    char response[LARGE_CHAR_SIZE];
    snprintf(response, sizeof(response),
             "HTTP/1.1 101 Switching Protocols\r\n"
             "Upgrade: websocket\r\n"
             "Connection: Upgrade\r\n"
             "Sec-WebSocket-Accept: %s\r\n\r\n",
             handshake_request->sec_websocket_accept);

    // printf("Send handshake\n%s\n", response);

    send(sock, response, strlen(response), 0);
}

int ws_send_text_frame(int sock, const char* message) {
    size_t message_len = strlen(message);
    unsigned char frame[2 + message_len]; // 2 bytes for the header, the rest for the message

    frame[0] = 0x81;
    frame[1] = message_len;

    memcpy(frame + 2, message, message_len);

    send(sock, frame, sizeof(frame), 0);
}
