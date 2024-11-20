<?php

use app\core\Application;

class m0021_create_table_opinion
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE opinion (
            id SERIAL PRIMARY KEY,
            
            rating INT NOT NULL,
            title VARCHAR(128) NOT NULL,
            comment VARCHAR(255) NOT NULL,
            visit_date DATE NOT NULL,
            visit_context VARCHAR(60) NOT NULL,
            
            account_id INT NOT NULL,
            offer_id INT NOT NULL,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (account_id) REFERENCES account(id),
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );
        CREATE TABLE opinion_photo (
            id SERIAL PRIMARY KEY,
            photo_url VARCHAR(255) NOT NULL,
            opinion_id INT NOT NULL,
            
            FOREIGN KEY (opinion_id) REFERENCES opinion(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE opinion CASCADE;
                DROP TABLE opinion_photo CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}