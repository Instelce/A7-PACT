<?php

// This file is used to make some test to create a php client that will connect to the server (4242)

// Set the host and port of the server
$host = '127.0.0.1';
$port = 4242;

// Create a socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if ($socket === false) {
    echo "Socket creation failed: " . socket_strerror(socket_last_error()) . "\n";
    exit();
}

// Connect to the server
$result = socket_connect($socket, $host, $port);

if ($result === false) {
    echo "Connection failed: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit();
}

echo "Connected to server $host:$port\n";


$data = "Bonjour";

echo "Sending data to server: $data\n";

// Send raw binary data
socket_write($socket, $data, strlen($data));

echo "Data sent\n";

// Read the response from the server
$response = socket_read($socket, 1024);
echo "Server response: $response\n";

// Close the socket
socket_close($socket);

?>