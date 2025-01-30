FROM debian:latest

RUN apt-get update && apt-get install -y gcc make cmake libpq-dev git libjson-c-dev

WORKDIR /app

COPY . .

RUN rm -rf lib

RUN make deps
RUN make websocket-prod

WORKDIR /app

CMD ["./bin/websocket-server"]
