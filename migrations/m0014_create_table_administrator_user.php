<?php

use app\core\Application;

class m0014_create_table_administrator_user
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE administrator_user (
            id INT PRIMARY KEY,
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatarUrl VARCHAR(255) NOT NULL,
            CONSTRAINT administrator_user_fk1 FOREIGN KEY (id) REFERENCES account(id),
            CONSTRAINT administrator_user_fk2 FOREIGN KEY (mail) REFERENCES user(mail),
            CONSTRAINT administrator_user_fk3 FOREIGN KEY (password) REFERENCES user(password),
            CONSTRAINT administrator_user_fk4 FOREIGN KEY (avatarUrl) REFERENCES user(avatarUrl)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE administrator_user;";
        $db->pdo->exec($sql);
    }
}