#include <ctype.h>
#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <termios.h>
#include <time.h>

#include "utils.h"
#include <unistd.h>

// Generated with ChatGPT
void trim(char* s)
{
    char* p = s;
    int l = strlen(p);

    while (isspace(p[l - 1]))
        p[--l] = 0;
    while (*p && isspace(*p))
        ++p, --l;

    if (l > 0 && (p[l - 1] == '\n' || p[l - 1] == '\r'))
        p[--l] = 0;

    memmove(s, p, l + 1);
}

void set_date_now(char* date)
{
    time_t now = time(NULL);
    struct tm* tm = localtime(&now);

    sprintf(date, "%d-%02d-%02d %02d:%02d:%02d", tm->tm_year + 1900, tm->tm_mon + 1, tm->tm_mday, tm->tm_hour, tm->tm_min, tm->tm_sec);
}

char* to_string(int num)
{
    char* str = malloc(12);
    sprintf(str, "%d", num);
    return str;
}

void clear_term()
{
    printf("\033[H\033[J");
}

char* get_color_code(color_t color)
{
    char* color_code = "";

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
    case NO_COLOR:
        color_code = "\033[0m";
        break;
    }
}

char* get_text_style(text_style_t style)
{
    char* style_code = "";

    switch (style) {
    case BOLD:
        style_code = "\033[1m";
        break;
    case UNDERLINE:
        style_code = "\033[4m";
        break;
    case BLINK:
        style_code = "\033[5m";
        break;
    case REVERSE:
        style_code = "\033[7m";
        break;
    }
}

void color_printf(color_t color, char* format, ...)
{
    char* color_code = get_color_code(color);

    printf("%s", color_code);

    va_list args;
    va_start(args, format);
    vprintf(format, args);
    va_end(args);

    printf("\033[0m");
}

void style_printf(text_style_t style, char* format, ...)
{
    char* style_code = get_text_style(style);

    printf("%s", style_code);

    va_list args;
    va_start(args, format);
    vprintf(format, args);
    va_end(args);

    printf("\033[0m");
}

void cs_printf(color_t color, text_style_t style, char* format, ...)
{
    char* color_code = get_color_code(color);
    char* style_code = get_text_style(style);

    printf("%s%s", color_code, style_code);

    va_list args;
    va_start(args, format);
    vprintf(format, args);
    va_end(args);

    printf("\033[0m");
}

int get_arrow_key()
{
    int c = getchar();
    if (c == 27) { // Escape sequence starts with 27
        getchar(); // Skip the '['
        c = getchar(); // Get the actual arrow key code
        switch (c) {
        case 'A':
            return 'U'; // Up arrow
        case 'B':
            return 'D'; // Down arrow
        case 'C':
            return 'R'; // Right arrow
        case 'D':
            return 'L'; // Left arrow
        }
    }
    return c;
}

// Function to set terminal to raw mode
void set_raw_mode()
{
    struct termios raw;

    tcgetattr(STDIN_FILENO, &raw);
    raw.c_lflag &= ~(ICANON | ECHO);
    tcsetattr(STDIN_FILENO, TCSANOW, &raw);
}

// Function to restore terminal to original settings
void reset_terminal_mode()
{
    struct termios original;
    tcgetattr(STDIN_FILENO, &original);
    original.c_lflag |= (ICANON | ECHO);
    tcsetattr(STDIN_FILENO, TCSANOW, &original);
}

void hide_cursor()
{
    printf("\033[?25l");
}

void show_cursor()
{
    printf("\033[?25h");
}
