#ifndef LOG_H
#define LOG_H

#include <signal.h>
#include "types.h"


// Verbose mode
extern int log_verbose;

// Path of the log file
extern char log_file_path[CHAR_SIZE];

extern char log_client_ip[CHAR_SIZE];

void log_info(char *format, ...);

#endif // LOG_H