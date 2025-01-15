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

typedef enum {
    BOLD,
    UNDERLINE,
    BLINK,
    REVERSE,
} text_style_t;

void trim(char *s);

void set_date_now(char *date);

char* to_string(int num);

// Term utils functions
void clear_term();
void color_printf(color_t color, char *format, ...);
void style_printf(text_style_t style, char *format, ...);
void cs_printf(color_t color, text_style_t style, char* format, ...);
int get_arrow_key();
void set_raw_mode();
void reset_terminal_mode();
void hide_cursor();
void show_cursor();

#endif // UTILS_H