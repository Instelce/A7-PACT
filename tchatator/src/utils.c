#include <ctype.h>
#include <string.h>

// Generated with ChatGPT
void trim(char *s) {
    char *p = s;
    int l = strlen(p);

    while(isspace(p[l - 1])) p[--l] = 0;
    while(*p && isspace(*p)) ++p, --l;

    if (l > 0 && (p[l - 1] == '\n' || p[l - 1] == '\r')) p[--l] = 0;

    memmove(s, p, l + 1);
}
