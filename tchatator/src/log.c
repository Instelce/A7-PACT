#include <stdlib.h>
#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <time.h>
#include <string.h>

#include "types.h"

int log_verbose;
char log_file_path[CHAR_SIZE];

// TODO - Add client identity and client IP
void log_info(char message[]) {
    int fd;
    time_t t;
    struct tm tm;
    char log_line[CHAR_SIZE];

    t = time(NULL);
    tm = *localtime(&t);

    fd = open(log_file_path, O_CREAT | O_WRONLY | O_APPEND);

    if (fd < 0) {
        perror("Cannot open log file");
        exit(1);
    }

    sprintf(log_line, "%02d-%02d-%d %02d:%02d:%02d %s\n", tm.tm_mday, tm.tm_mon + 1, tm.tm_year + 1900, tm.tm_hour, tm.tm_min, tm.tm_sec, message);
    write(fd, log_line, strlen(log_line));

    if (log_verbose) {
        printf("%s", log_line);
    }
}