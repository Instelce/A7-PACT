#include <ctype.h>
#include <string.h>
#include <stdio.h>
#include <stdarg.h>
#include <time.h>
#include <stdlib.h>

#include "utils.h"

// Generated with ChatGPT
void trim(char *s) {
    char *p = s;
    int l = strlen(p);

    while(isspace(p[l - 1])) p[--l] = 0;
    while(*p && isspace(*p)) ++p, --l;

    if (l > 0 && (p[l - 1] == '\n' || p[l - 1] == '\r')) p[--l] = 0;

    memmove(s, p, l + 1);
}

void set_date_now(char *date) {
    time_t now = time(NULL);
    struct tm *tm = localtime(&now);

    sprintf(date, "%d-%02d-%02d %02d:%02d:%02d", tm->tm_year + 1900, tm->tm_mon + 1, tm->tm_mday, tm->tm_hour, tm->tm_min, tm->tm_sec);
}

void clear_term() {
    printf("\033[H\033[J");
}

void cprintf(color_t color, char *format, ...) {
    char *color_code = "";
    switch (color) {
        case BLACK:
            color_code = "\033[0;30m";
            break;
        case RED:
            color_code = "\033[0;31m";
            break;
        case GREEN:
            color_code = "\033[0;32m";
            break;
        case YELLOW:
            color_code = "\033[0;33m";
            break;
        case BLUE:
            color_code = "\033[0;34m";
            break;
        case MAGENTA:
            color_code = "\033[0;35m";
            break;
        case CYAN:
            color_code = "\033[0;36m";
            break;
        case WHITE:
            color_code = "\033[0;37m";
            break;
        case GRAY:
            color_code = "\033[0;90m";
            break;
    }

    printf("%s", color_code);

    va_list args;
    va_start(args, format);
    vprintf(format, args);
    va_end(args);

    printf("\033[0m");
}