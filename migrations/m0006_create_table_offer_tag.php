<?php

use app\core\Application;

class m0006_taggage
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE taggage(
            id SERIAL PRIMARY KEY,
            name INT NOT NULL,
            offer_id INT  NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE taggage;";
        Application::$app->db->pdo->exec($sql);
    }
}