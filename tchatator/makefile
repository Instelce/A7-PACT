CC = cc
CFLAGS += -Wall
CFLAGS += -I/usr/include -L/usr/lib -lpq # postgreSQL

lib_dir = lib
output = bin
utils = src/database.c src/protocol.c src/log.c src/config.c src/utils.c

# Install dependencies for websocket server only
# - wsServer : https://github.com/Theldus/wsServer
# - json-c: https://github.com/json-c/json-c
deps:
	mkdir $(lib_dir)
	cd $(lib_dir) && git clone https://github.com/Theldus/wsServer
	cd $(lib_dir)/wsServer && make
	cd $(lib_dir) && git clone https://github.com/json-c/json-c
	cd $(lib_dir)/json-c && cmake . && make

server: $(utils) src/server.c
	mkdir -p $(output)
	$(CC) $(CFLAGS) $^ -o $(output)/server
	./$(output)/server

client: $(utils) src/client.c
	mkdir -p $(output)
	$(CC) $(CFLAGS) $^ -o $(output)/client
	./$(output)/client

websocket: $(utils) src/ws_server.c
	mkdir -p $(output)
	$(CC) $(CFLAGS) $^ -o $(output)/websocket-server -Ilib/wsServer/include -Llib/wsServer -lws -Ilib/json-c -Llib/json-c/ -ljson-c
	./$(output)/websocket-server

clean:
	rm -f $(output)/*

server-vic: $(utils) src/server.c
	$(CC) $(CFLAGS) $^ -o $(output)/server
	./$(output)/server

client-vic: $(utils) src/client.c
	$(CC) $(CFLAGS) $^ -o $(output)/client
	./$(output)/client
