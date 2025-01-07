#ifndef DATABASE_H
#define DATABASE_H

#include <libpq-fe.h>

void db_login(PGconn *conn);
void db_exit(PGconn *conn);

#endif // DATABASE_H