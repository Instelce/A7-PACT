#ifndef SHA1_H
#define SHA1_H

#include <stdint.h>
#include <stdio.h>

#define SHA1_BLOCK_SIZE 20  // SHA1 outputs a 20 byte digest

// Circular left shift macro
#define ROTLEFT(a, b) ((a << b) | (a >> (32 - b)))

typedef struct {
    uint8_t data[64];
    uint32_t datalen;
    uint64_t bitlen;
    uint32_t state[5];
} SHA1_CTX;

void base64_encode(const unsigned char *input, size_t len, char *output);
void sha1_transform(SHA1_CTX *ctx, const uint8_t data[]);
void sha1_init(SHA1_CTX *ctx);
void sha1_update(SHA1_CTX *ctx, const uint8_t data[], size_t len);
void sha1_final(SHA1_CTX *ctx, uint8_t hash[]);
void sha1(const char *input, char *output);

#endif // SHA1_H