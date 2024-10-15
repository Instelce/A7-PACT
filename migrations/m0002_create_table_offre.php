<?php

use app\core\Application;

class m0002_create_table_offre
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offres (
            id SERIAL PRIMARY KEY,
            titre VARCHAR(60),
            resume VARCHAR(128),
            description VARCHAR(1024),
            likes INT,
            hors_ligne BOOLEAN,
            hors_ligne_date DATE,
            dernière_mise_en_ligne DATE,
            compteur_vue INT,
            compteur_click INT,
            site_web VARCHAR,
            num_telephone INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down() {
        $db = Application::$app->db;
        $sql = "DROP TABLE offres;";
        $db->pdo->exec($sql);
    }
}