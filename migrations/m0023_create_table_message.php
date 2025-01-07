<?php

use app\core\Application;

class m0023_create_table_message
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE message (
            id SERIAL PRIMARY KEY,
            sended_date DATE NOT NULL,
            modified_date DATE,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            deleted BOOLEAN NOT NULL,
            received BOOLEAN NOT NULL,
            sended BOOLEAN NOT NULL,
            content TEXT NOT NULL

        );";
        $db->pdo->exec($sql);
    }
    public function down()
    {
        $sql = "DROP TABLE message CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}