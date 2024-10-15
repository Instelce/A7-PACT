<?php

use app\core\Application;

class m0004_option{
    public function up(){
        $db = Application::$app->$db;
        $sql = "CREATE TABLE option (
            id SERIAL PRIMARY KEY,
            dateLancement DATE,
            compteurSemaines INT,
            duree INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE option;";
        Application::$app->$db->pdo->exec($sql);
    }
}