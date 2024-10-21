<?php

use app\core\Application;

class m0020_create_table_offer_schedule
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_schedule (
            id SERIAL PRIMARY KEY,
            
            day INT NOT NULL,
            opening_hours TIME NOT NULL,
            closing_hours TIME NOT NULL
        );


        CREATE TABLE activity_schedule (
            id SERIAL PRIMARY KEY,
            
            activity_id INT NOT NULL,
            schedule_id INT NOT NULL,
            
            FOREIGN KEY (activity_id) REFERENCES activity_offer (offer_id),
            FOREIGN KEY (schedule_id) REFERENCES offer_schedule (id)
        );

        CREATE TABLE restaurant_schedule (
            id SERIAL PRIMARY KEY,
            
            restaurant_id INT NOT NULL,
            schedule_id INT NOT NULL,
            
            FOREIGN KEY (restaurant_id) REFERENCES restaurant_offer (offer_id),
            FOREIGN KEY (schedule_id) REFERENCES offer_schedule (id)
        );

        CREATE TABLE parc_schedule (
            id SERIAL PRIMARY KEY,
            
            attraction_park_id INT NOT NULL,
            schedule_id INT NOT NULL,
            
            FOREIGN KEY (attraction_park_id) REFERENCES attraction_park_offer (offer_id),
            FOREIGN KEY (schedule_id) REFERENCES offer_schedule (id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_schedule CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}