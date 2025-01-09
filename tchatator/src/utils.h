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

// Term utils functions
void clear_term();
void cprintf(color_t color, char *format, ...);

#endif // UTILS_H