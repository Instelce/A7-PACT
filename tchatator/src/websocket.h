#ifndef WEBSOCKET_H
#define WEBSOCKET_H

#include "types.h"

#define WEBSOCKET_GUID "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"

typedef struct
{
    char method[16];
    char path[CHAR_SIZE];
    char http_version[16];
    char host[CHAR_SIZE];
    char upgrade[32];
    char connection[32];
    char sec_websocket_key[64];
    char sec_websocket_version[16];
    char sec_websocket_protocol[64];
    char sec_websocket_accept[64];
} handshake_request_t;

typedef enum {
    CONTINUATION_FRAME = 0x0,
    TEXT_FRAME = 0x1,
    BINARY_FRAME = 0x2,
    CLOSE_FRAME = 0x8,
    PING_FRAME = 0x9,
    PONG_FRAME = 0xA
} ws_opcode_t;

int is_ws_handshake(char request[]);

int ws_parse_handshake_request(char request[], handshake_request_t* handshake_request);

int ws_send_handshake(int sock, handshake_request_t* handshake_request);

int ws_send_text_frame(int sock, const char* message);

#endif // WEBSOCKET_H