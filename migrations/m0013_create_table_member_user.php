<?php

use app\core\Application;

class m0013_create_table_member_user
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE member_user(
            member_id INT PRIMARY KEY,
            
            lastname VARCHAR(50) NOT NULL,
            firstname VARCHAR(50) NOT NULL,
            phone VARCHAR(50) UNIQUE NOT NULL,
            pseudo VARCHAR(50) UNIQUE NOT NULL,
            allows_notifications bool DEFAULT '0' NOT NULL,
            
            CONSTRAINT member_user_fk FOREIGN KEY (member_id) REFERENCES user_account(user_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE member_user;";
        $db->pdo->exec($sql);
    }
}