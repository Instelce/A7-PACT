<?php

use app\core\Application;

class m0010_create_table_anonymous
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE anonymous(
            id INT NOT NULL PRIMARY KEY,
            pseudo VARCHAR(15) NOT NULL,
            CONSTRAINT anonymous_fk FOREIGN KEY (id) REFERENCES account(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE anonymous;";
        $db->pdo->exec($sql);
    }
}