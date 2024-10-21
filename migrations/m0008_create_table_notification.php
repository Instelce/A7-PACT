<?php

use app\core\Application;

class m0008_create_table_notification
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE notification (
            id SERIAL PRIMARY KEY,
            send_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            reception_day DATE NOT NULL,
            open_at TIMESTAMP NOT NULL,
            is_read BOOLEAN NOT NULL,
            content VARCHAR(255) NOT NULL
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE notification CASCADE;";
        $db->pdo->exec($sql);
    }
}