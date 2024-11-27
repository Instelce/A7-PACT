<?php

use app\core\Application;

class m0006_create_table_account
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE account (
            id SERIAL PRIMARY KEY,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE anonymous_account (
            account_id INT PRIMARY KEY,
            pseudo VARCHAR(15) NOT NULL,
            
            FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE
        );

        CREATE TABLE user_account (
            account_id INT PRIMARY KEY,
            
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatar_url VARCHAR(255) NOT NULL,
            address_id INT NOT NULL,
            
            FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE CASCADE,
            FOREIGN KEY (address_id) REFERENCES address(id) ON DELETE CASCADE
        );
        CREATE TABLE administrator_user (
            user_id INT PRIMARY KEY,

            CONSTRAINT administrator_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id) ON DELETE CASCADE
        );
        CREATE TABLE member_user (
            user_id INT PRIMARY KEY,
            
            lastname VARCHAR(50) NOT NULL,
            firstname VARCHAR(50) NOT NULL,
            pseudo VARCHAR(50) UNIQUE NOT NULL,
            phone VARCHAR(10) UNIQUE NOT NULL,
            allows_notifications BOOLEAN DEFAULT FALSE NOT NULL,
            
            CONSTRAINT member_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id) ON DELETE CASCADE
        );
        CREATE TABLE professional_user (
            user_id INT PRIMARY KEY,
            
            code SERIAL NOT NULL,
            denomination VARCHAR(100) UNIQUE NOT NULL,
            siren VARCHAR(14) UNIQUE NOT NULL,
            phone VARCHAR(10) UNIQUE NOT NULL,
            allows_notifications BOOLEAN DEFAULT FALSE NOT NULL,
            
            CONSTRAINT professional_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id) ON DELETE CASCADE
        );
        CREATE TABLE private_professional (
            pro_id INT PRIMARY KEY,
            
            last_veto DATE NOT NULL,
            payment_id INT NOT NULL,
            
            CONSTRAINT private_professional_fk FOREIGN KEY (pro_id) REFERENCES professional_user(user_id) ON DELETE CASCADE,
            FOREIGN KEY (payment_id) REFERENCES mean_of_payment(id) ON DELETE CASCADE
        );
        CREATE TABLE public_professional (
            pro_id INT PRIMARY KEY,

            CONSTRAINT public_professional_fk FOREIGN KEY (pro_id) REFERENCES professional_user(user_id) ON DELETE CASCADE
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE account CASCADE;
                DROP TABLE anonymous_account CASCADE;
                DROP TABLE user_account CASCADE;
                DROP TABLE administrator_user CASCADE;
                DROP TABLE member_user CASCADE;
                DROP TABLE professional_user CASCADE;
                DROP TABLE private_professional CASCADE;
                DROP TABLE public_professional CASCADE;";
        $db->pdo->exec($sql);
    }
}