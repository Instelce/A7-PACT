// Made with the help of https://www.postgresql.org/docs/current/libpq-example.html

#include <stdlib.h>
#include <libpq-fe.h>

#include "database.h"


void db_login(PGconn *conn) {
    conn = PQsetdbLogin(getenv("DB_HOST"), getenv("DB_PORT"), NULL, NULL, getenv("DB_NAME"), getenv("DB_USER"), getenv("DB_PASSWORD"));

    if (PQstatus(conn) != CONNECTION_OK) {
        perror("Error when connecting the DB");
        db_exit(conn);
    }
}

void db_exit(PGconn *conn) {
    PQfinish(conn);
    exit(1);
}