<?php

use app\core\Application;

class m0009_create_table_tag
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE tag (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) NOT NULL
        );

        CREATE TABLE is_tagged (
            id SERIAL PRIMARY KEY,
            
            tag_id INT NOT NULL,
            offer_id INT NOT NULL,  
            
            FOREIGN KEY (offer_id) REFERENCES offer(id),
            FOREIGN KEY (tag_id) REFERENCES tag(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE tag CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}