#ifndef UTILS_H
#define UTILS_H

typedef enum
{
    BLACK,
    RED,
    GREEN,
    YELLOW,
    BLUE,
    MAGENTA,
    CYAN,
    WHITE,
    GRAY
} color_t;

void trim(char *s);

void set_date_now(char *date);

char* to_string(int num);

// Term utils functions
void clear_term();
void cprintf(color_t color, char *format, ...);
int get_arrow_key();
void set_raw_mode();
void reset_terminal_mode();

#endif // UTILS_H