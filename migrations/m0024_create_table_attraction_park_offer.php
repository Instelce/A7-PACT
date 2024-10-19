<?php

use app\core\Application;

class m0024_create_table_attraction_park_offer
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE attraction_park_offer (
            offer_id INT NOT NULL PRIMARY KEY,

            url_image_park_map VARCHAR(255) NOT NULL,
            attraction_number INT NOT NULL,
            required_age INT NOT NULL,

            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE attraction_park_offer CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}