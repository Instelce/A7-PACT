CC = cc
CFLAGS = -Wall

output = bin
utils = src/database.c src/protocol.c src/log.c src/config.c src/utils.c

server: $(utils) src/server.c
	$(CC) $^ -o $(output)/server -I/usr/include -L/usr/lib -lpq
	./$(output)/server

client: $(utils) src/client.c
	$(CC) $^ -o $(output)/client -I/usr/include -L/usr/lib -lpq
	./$(output)/client

clean:
	rm -f $(output)/*

build-server: $(utils) src/server.c
	$(CC) $^ -o $(output)/server -I/usr/include -L/usr/lib -lpq

server-vic: $(utils) src/server.c
	$(CC) $^ -o $(output)/server -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
	./$(output)/server

client-vic: $(utils) src/client.c
	$(CC) $^ -o $(output)/client -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
	./$(output)/client
