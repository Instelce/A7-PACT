<?php

use app\core\Application;

class m0015_create_table_public_professional
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE public_professional (
            id INT PRIMARY KEY,
            mail VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(100) NOT NULL,
            avatarUrl VARCHAR(255) NOT NULL,
            code INT NOT NULL,
            denomination VARCHAR(100) UNIQUE NOT NULL,
            siren VARCHAR(14) UNIQUE NOT NULL,
            CONSTRAINT public_professional_fk1 FOREIGN KEY (id) REFERENCES account(id),
            CONSTRAINT public_professional_fk2 FOREIGN KEY (mail) REFERENCES user(mail),
            CONSTRAINT public_professional_fk3 FOREIGN KEY (password) REFERENCES user(password),
            CONSTRAINT public_professional_fk4 FOREIGN KEY (avatarUrl) REFERENCES user(avatarUrl),
            CONSTRAINT public_professional_fk5 FOREIGN KEY (code) REFERENCES professional(code),
            CONSTRAINT public_professional_fk6 FOREIGN KEY (denomination) REFERENCES user(denomination),
            CONSTRAINT public_professional_fk7 FOREIGN KEY (siren) REFERENCES user(siren)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE public_professional;";
        $db->pdo->exec($sql);
    }
}