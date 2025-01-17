# Use the official Debian image as the base image
FROM debian:latest

# Update package list and install GCC
RUN apt-get update && apt-get install -y gcc make cmake libpq-dev git libjson-c-dev

# Set the working directory inside the container
WORKDIR /app

# Copy the C source code to the working directory
COPY . .

# Remove lib folder if it exists
RUN rm -rf lib

# Compile the C program
RUN make deps
RUN make websocket-prod

# Go back to the working directory
WORKDIR /app

# Set the default command to run the program
CMD ["./bin/websocket-server"]
