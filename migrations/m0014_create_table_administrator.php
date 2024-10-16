<?php

use app\core\Application;

class m0015_create_table_private_professional
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE private_professional (
            id INT PRIMARY KEY,
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatarUrl VARCHAR(255) NOT NULL,
            CONSTRAINT administrator_fk1 FOREIGN KEY (id) REFERENCES account(id),
            CONSTRAINT administrator_fk2 FOREIGN KEY (mail) REFERENCES user(mail),
            CONSTRAINT administrator_fk3 FOREIGN KEY (password) REFERENCES user(password),
            CONSTRAINT administrator_fk4 FOREIGN KEY (avatarUrl) REFERENCES user(avatarUrl)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE administrator;";
        $db->pdo->exec($sql);
    }
}