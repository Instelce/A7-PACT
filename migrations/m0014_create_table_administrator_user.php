<?php

use app\core\Application;

class m0014_create_table_administrator_user
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE administrator_user (
            admin_id INT PRIMARY KEY,
                        
            CONSTRAINT administrator_user_fk FOREIGN KEY (admin_id) REFERENCES user_account(user_id)
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