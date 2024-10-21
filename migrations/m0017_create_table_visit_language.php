<?php

use app\core\Application;

class m0017_create_table_visit_language
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE visit_language (
            language_id SERIAL PRIMARY KEY,
            offer_id INT NOT NULL,
            
            language VARCHAR(255) NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );

        CREATE TABLE is_talked (
            id SERIAL PRIMARY KEY,
            
            visit_id INT NOT NULL,
            language_id INT NOT NULL,
            
            FOREIGN KEY (visit_id) REFERENCES visit_offer(offer_id),
            FOREIGN KEY (language_id) REFERENCES visit_language(language_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE visit_language CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}