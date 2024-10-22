<?php

use app\core\Application;

class m0018_create_table_meal
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE meal (
            id SERIAL PRIMARY KEY,
            name VARCHAR(30) NOT NULL  
        );

        CREATE TABLE is_on_restaurant_menu (
            id SERIAL PRIMARY KEY,
            
            restaurant_id INT NOT NULL,
            meal_id INT NOT NULL,
            
            FOREIGN KEY (restaurant_id) REFERENCES restaurant_offer(offer_id),
            FOREIGN KEY (meal_id) REFERENCES meal(id)
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