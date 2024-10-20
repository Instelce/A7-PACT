<?php

use app\core\Application;

class m0001_create_table_account
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
            account_id INT NOT NULL PRIMARY KEY,
            pseudo VARCHAR(15) NOT NULL,
            
            CONSTRAINT anonymous_account_fk FOREIGN KEY (account_id) REFERENCES account(id)
        );
        CREATE TABLE user_account (
            account_id INT PRIMARY KEY,
            
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatarUrl VARCHAR(255) NOT NULL,
            
            CONSTRAINT user_account_fk FOREIGN KEY (account_id) REFERENCES account(id)
        );
        CREATE TABLE administrator_user (
            user_id INT PRIMARY KEY,

            CONSTRAINT administrator_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id)
        );
        CREATE TABLE member_user (
            user_id INT PRIMARY KEY,
            
            lastname VARCHAR(50) NOT NULL,
            firstname VARCHAR(50) NOT NULL,
            phone VARCHAR(50) UNIQUE NOT NULL,
            pseudo VARCHAR(50) UNIQUE NOT NULL,
            allows_notifications BOOLEAN DEFAULT FALSE NOT NULL,
            
            CONSTRAINT member_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id)
        );
        CREATE TABLE professional_user (
            user_id INT PRIMARY KEY,
            
            code SERIAL NOT NULL,
            denomination VARCHAR(100) UNIQUE NOT NULL,
            siren VARCHAR(14) UNIQUE NOT NULL,
            
            CONSTRAINT professional_user_fk FOREIGN KEY (user_id) REFERENCES user_account(account_id)
        );
        CREATE TABLE private_professional (
            pro_id INT PRIMARY KEY,
            
            last_veto DATE NOT NULL,
            
            CONSTRAINT private_professional_fk FOREIGN KEY (pro_id) REFERENCES professional_user(user_id)
        );
        CREATE TABLE public_professional (
            pro_id INT PRIMARY KEY,

            CONSTRAINT public_professional_fk FOREIGN KEY (pro_id) REFERENCES professional_user(user_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE account CASCADE;";
        $db->pdo->exec($sql);
    }
}