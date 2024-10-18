<?php

use app\core\Application;

class m0010_create_table_anonymous_account
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE anonymous_account(
            anonymous_id INT NOT NULL PRIMARY KEY,
            
            pseudo VARCHAR(15) NOT NULL,
            
            CONSTRAINT anonymous_account_fk FOREIGN KEY (anonymous_id) REFERENCES account(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE anonymous_account;";
        $db->pdo->exec($sql);
    }
}