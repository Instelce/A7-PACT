<?php

use app\core\Application;

class m0007_create_table_offer
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE offer_type (
            id SERIAL PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            price NUMERIC NOT NULL 
        );

        CREATE TABLE offer (
            id SERIAL PRIMARY KEY,
            title VARCHAR(128) NOT NULL,
            summary VARCHAR(384) NOT NULL,
            description VARCHAR(1024) NOT NULL,
            likes INT DEFAULT 0,
            offline BOOLEAN DEFAULT TRUE,
            offline_date DATE,
            last_online_date DATE,
            view_counter INT DEFAULT 0,
            click_counter INT DEFAULT 0,
            website VARCHAR(255),
            phone_number VARCHAR(10),
            category VARCHAR(20) NOT NULL,
            minimum_price FLOAT,
            
            professional_id INT NOT NULL,
            address_id INT NOT NULL,
            offer_type_id INT NOT NULL,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (professional_id) REFERENCES professional_user(user_id) ON DELETE CASCADE,
            FOREIGN KEY (address_id) REFERENCES address(id) ON DELETE CASCADE,
            FOREIGN KEY (offer_type_id) REFERENCES offer_type(id) ON DELETE CASCADE
        );

        CREATE TABLE offer_option (
            id SERIAL PRIMARY KEY,
            type VARCHAR(20) NOT NULL,
            launch_date DATE NOT NULL,
            duration INT NOT NULL,
            offer_id INT NOT NULL,

            FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE
        );


        CREATE TABLE offer_photo (
            id SERIAL PRIMARY KEY,
            url_photo VARCHAR(255) NOT NULL,
            offer_id INT NOT NULL,
 
            FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE offer_type CASCADE;
                DROP TABLE offer CASCADE;
                DROP TABLE offer_option CASCADE;
                DROP TABLE offer_photo CASCADE;";
        $db->pdo->exec($sql);
    }
}