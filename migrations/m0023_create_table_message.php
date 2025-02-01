<?php

use app\core\Application;

class m0023_create_table_message
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE message (
            id SERIAL PRIMARY KEY,
            sended_date TIMESTAMP NOT NULL,
            modified_date TIMESTAMP,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            deleted BOOLEAN NOT NULL,
            seen BOOLEAN NOT NULL,
            content TEXT NOT NULL
        );
        CREATE TABLE banned_user (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            banned_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            active BOOLEAN NOT NULL DEFAULT TRUE,

            FOREIGN KEY (user_id) REFERENCES account (id) ON DELETE CASCADE
        );
        CREATE TABLE blocked_user (
            id SERIAL PRIMARY KEY,
            user_id INT NOT NULL,
            for_user_id INT NOT NULL DEFAULT 0,
            blocked_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            duration_seconds INT NOT NULL,
            active BOOLEAN NOT NULL DEFAULT TRUE,

            FOREIGN KEY (user_id) REFERENCES account (id) ON DELETE CASCADE
        );";
        $db->pdo->exec($sql);
    }
    public function down()
    {
        $sql = "DROP TABLE message CASCADE; DROP TABLE banned_user CASCADE; DROP TABLE blocked_user CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}