<?php 

use app\core\Application;

class m0008_adresse{
    public function up(){
        $db = Application::$app->$db;
        $sql = "CREATE TABLE adresse(
            id SERIAL PRIMARY KEY,
            numero INT,
            nom VARCHAR(255),
            ville VARCHAR(255),
            code_postal INT,
            longitude_user NUMERIC,
            latitude_user NUMERIC,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE adresse;";
        Application::$app->$db->pdo->exec($sql);
    }
}