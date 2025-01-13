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
            opening_hours VARCHAR(5) NOT NULL,
            closing_hours VARCHAR(5) NOT NULL
        );
/**-\"ocaca boudin\" : Raphael Corre 28/11/24 **/
        CREATE TABLE link_schedule (
            id SERIAL PRIMARY KEY,
            schedule_id INT NOT NULL,
            offer_id INT NOT NULL,
            FOREIGN KEY (schedule_id) REFERENCES offer_schedule (id),
            FOREIGN KEY (offer_id) REFERENCES offer (id)

        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_schedule CASCADE;
                DROP TABLE link_schedule CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}