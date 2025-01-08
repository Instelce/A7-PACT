#ifndef LOG_H
#define LOG_H

#include "types.h"

// Verbose mode
extern int log_verbose;

// Path of the log file
extern char log_file_path[CHAR_SIZE];

void log_info(char *format, ...);

#endif // LOG_H