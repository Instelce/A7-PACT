#include <stdint.h>
#include <string.h>
#include <stdio.h>

#include "crypto.h"


void base64_encode(const unsigned char *input, size_t len, char *output) {
    static const char base64_table[65] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

    for (size_t i = 0, j = 0; i < len;) {
        uint32_t octet_a = i < len ? input[i++] : 0;
        uint32_t octet_b = i < len ? input[i++] : 0;
        uint32_t octet_c = i < len ? input[i++] : 0;

        uint32_t triple = (octet_a << 0x10) + (octet_b << 0x08) + octet_c;

        output[j++] = base64_table[(triple >> 3 * 6) & 0x3F];
        output[j++] = base64_table[(triple >> 2 * 6) & 0x3F];
        output[j++] = base64_table[(triple >> 1 * 6) & 0x3F];
        output[j++] = base64_table[(triple >> 0 * 6) & 0x3F];
    }

    for (size_t i = 0; i < 3 - len % 3; i++) {
        output[strlen(output) - 1] = '=';
    }

    output[strlen(output)] = '\0';
}

void sha1_transform(SHA1_CTX *ctx, const uint8_t data[]) {
    uint32_t a, b, c, d, e, i, j, t, m[80];
    uint32_t K[] = { 0x5A827999, 0x6ED9EBA1, 0x8F1BBCDC, 0xCA62C1D6 };

    for (i = 0, j = 0; i < 16; ++i, j += 4)
        m[i] = (data[j] << 24) | (data[j + 1] << 16) | (data[j + 2] << 8) | (data[j + 3]);
    for ( ; i < 80; ++i)
        m[i] = ROTLEFT(m[i - 3] ^ m[i - 8] ^ m[i - 14] ^ m[i - 16], 1);

    a = ctx->state[0];
    b = ctx->state[1];
    c = ctx->state[2];
    d = ctx->state[3];
    e = ctx->state[4];

    for (i = 0; i < 80; ++i) {
        if (i < 20) {
            t = ROTLEFT(a, 5) + ((b & c) | (~b & d)) + e + K[0] + m[i];
        } else if (i < 40) {
            t = ROTLEFT(a, 5) + (b ^ c ^ d) + e + K[1] + m[i];
        } else if (i < 60) {
            t = ROTLEFT(a, 5) + ((b & c) | (b & d) | (c & d)) + e + K[2] + m[i];
        } else {
            t = ROTLEFT(a, 5) + (b ^ c ^ d) + e + K[3] + m[i];
        }
        e = d;
        d = c;
        c = ROTLEFT(b, 30);
        b = a;
        a = t;
    }

    ctx->state[0] += a;
    ctx->state[1] += b;
    ctx->state[2] += c;
    ctx->state[3] += d;
    ctx->state[4] += e;
}

void sha1_init(SHA1_CTX *ctx) {
    ctx->datalen = 0;
    ctx->bitlen = 0;
    ctx->state[0] = 0x67452301;
    ctx->state[1] = 0xEFCDAB89;
    ctx->state[2] = 0x98BADCFE;
    ctx->state[3] = 0x10325476;
    ctx->state[4] = 0xC3D2E1F0;
}

void sha1_update(SHA1_CTX *ctx, const uint8_t data[], size_t len) {
    for (size_t i = 0; i < len; ++i) {
        ctx->data[ctx->datalen] = data[i];
        ctx->datalen++;
        if (ctx->datalen == 64) {
            sha1_transform(ctx, ctx->data);
            ctx->bitlen += 512;
            ctx->datalen = 0;
        }
    }
}

void sha1_final(SHA1_CTX *ctx, uint8_t hash[]) {
    size_t i = ctx->datalen;

    // Pad whatever data is left in the buffer.
    if (ctx->datalen < 56) {
        ctx->data[i++] = 0x80;
        while (i < 56)
            ctx->data[i++] = 0x00;
    } else {
        ctx->data[i++] = 0x80;
        while (i < 64)
            ctx->data[i++] = 0x00;
        sha1_transform(ctx, ctx->data);
        memset(ctx->data, 0, 56);
    }

    ctx->bitlen += ctx->datalen * 8;
    ctx->data[63] = ctx->bitlen;
    ctx->data[62] = ctx->bitlen >> 8;
    ctx->data[61] = ctx->bitlen >> 16;
    ctx->data[60] = ctx->bitlen >> 24;
    ctx->data[59] = ctx->bitlen >> 32;
    ctx->data[58] = ctx->bitlen >> 40;
    ctx->data[57] = ctx->bitlen >> 48;
    ctx->data[56] = ctx->bitlen >> 56;
    sha1_transform(ctx, ctx->data);

    // Copy final state to output hash
    for (i = 0; i < 4; ++i) {
        hash[i] = (ctx->state[0] >> (24 - i * 8)) & 0x000000FF;
        hash[i + 4] = (ctx->state[1] >> (24 - i * 8)) & 0x000000FF;
        hash[i + 8] = (ctx->state[2] >> (24 - i * 8)) & 0x000000FF;
        hash[i + 12] = (ctx->state[3] >> (24 - i * 8)) & 0x000000FF;
        hash[i + 16] = (ctx->state[4] >> (24 - i * 8)) & 0x000000FF;
    }
}

void sha1(const char *input, char *output) {
    SHA1_CTX ctx;
    uint8_t hash[SHA1_BLOCK_SIZE];

    sha1_init(&ctx);
    sha1_update(&ctx, (uint8_t *)input, strlen(input));
    sha1_final(&ctx, hash);

    for (int i = 0; i < SHA1_BLOCK_SIZE; i++) {
        sprintf(output + (i * 2), "%02x", hash[i]);
    }
    output[SHA1_BLOCK_SIZE * 2] = '\0';
}
