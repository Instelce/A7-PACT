<?php

use app\core\Application;

class m0018_create_table_meal
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE meal (
            meal_id SERIAL PRIMARY KEY,
            name VARCHAR(128) NOT NULL,
            price NUMERIC NOT NULL
        );

        CREATE TABLE is_on_restaurant_menu (
            id SERIAL PRIMARY KEY,
            
            offer_id INT NOT NULL,
            meal_id INT NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES restaurant_offer(offer_id),
            FOREIGN KEY (meal_id) REFERENCES meal(meal_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE meal CASCADE;
                DROP TABLE is_on_restaurant_menu CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}