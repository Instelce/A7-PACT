<?php

use app\core\Application;

class m0011_create_table_user_account
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE user_account(
            id_user INT PRIMARY KEY,
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatarUrl VARCHAR(255) NOT NULL,
            CONSTRAINT user_account_fk FOREIGN KEY (id_user) REFERENCES account (id);
        )";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE user_account;";
        $db->pdo->exec($sql);
    }
}