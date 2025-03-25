<?php

use app\core\Application;

class m0021_create_table_opinion
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE opinion (
            id SERIAL PRIMARY KEY,
            
            rating FLOAT NOT NULL,
            title VARCHAR(128) NOT NULL,
            comment VARCHAR(1024) NOT NULL,
            visit_date DATE NOT NULL,
            visit_context VARCHAR(60) NOT NULL,
            
            read BOOLEAN NOT NULL DEFAULT false,
            
            account_id INT NOT NULL,
            offer_id INT NOT NULL,
            
            nb_reports INT NOT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            
            FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE CASCADE,
            FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE
        );
        CREATE TABLE opinion_photo (
            id SERIAL PRIMARY KEY,
            photo_url VARCHAR(255) NOT NULL,
            opinion_id INT NOT NULL,
            
            FOREIGN KEY (opinion_id) REFERENCES opinion(id) ON DELETE CASCADE
        );

        CREATE TABLE opinion_like(
            opinion_id INT NOT NULL,
            account_id INT NOT NULL,
            CONSTRAINT opinion_like_pk PRIMARY KEY(opinion_id, account_id)
        );

        CREATE TABLE opinion_dislike(
            opinion_id INT NOT NULL,
            account_id INT NOT NULL,
            CONSTRAINT opinion_dislike_pk PRIMARY KEY(opinion_id, account_id)
        );

        CREATE TABLE opinion_reply(
            opinion_reply_id SERIAL PRIMARY KEY,
            opinion_id INT NOT NULL,
            comment VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(opinion_id) REFERENCES opinion(id) ON DELETE CASCADE
        );
        CREATE TABLE opinion_blacklist(
            id_blacklist SERIAL PRIMARY KEY,
            opinion_id INT NOT NULL,
            blacklisted_date TIMESTAMP NOT NULL,
            FOREIGN KEY(opinion_id) REFERENCES opinion(id) ON DELETE CASCADE
        )";
        $db->pdo->exec($sql);
    }



    public function down()
    {
        $sql = "DROP TABLE opinion_blacklist CASCADE;
                DROP TABLE opinion CASCADE;
                DROP TABLE opinion_photo CASCADE;
                DROP TABLE opinion_like CASCADE;
                DROP TABLE opinion_dislike CASCADE;
                DROP TABLE opinion_reply CASCADE;
";
        Application::$app->db->pdo->exec($sql);
    }

    //create or replace function ftg_document_totalite()
//  returns trigger as $$
//begin
//  perform * from document_totalite;
//  if found then
//    raise exception 'Vous devez créer un post ou un commentaire, pas un document seul !';
//  end if;
//  return null;
//end;
//$$ language plpgsql;
//
//DROP TRIGGER if exists tg_document_totalite ON forum1._document CASCADE;
//create trigger tg_document_totalite
//after insert
//on _document for each row
//execute procedure ftg_document_totalite();

}