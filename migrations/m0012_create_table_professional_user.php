<?php

use app\core\Application;

class m0012_create_table_professional_user
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE professional_user(
            pro_id INT PRIMARY KEY,
            
            code SERIAL NOT NULL,
            denomination VARCHAR(100) UNIQUE NOT NULL,
            siren VARCHAR(14) UNIQUE NOT NULL,
            
            CONSTRAINT professional_user_fk FOREIGN KEY (pro_id) REFERENCES user_account(user_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE professional_user;";
        $db->pdo->exec($sql);
    }
}