<?php

namespace app\models\opinion;

use app\core\DBModel;
class OpinionBlackList extends DBModel
{

    public int $opinion_id = 0;
    public int $duration = 0;
    public int $blacklister_id = 0; // id of the professionnal that blacklisted the opinion
    public String $blacklisted_date = "";

    public static function pk() : string
    {
        return "opinion_id";
    }

    public static function tableName(): string
    {
        return 'opinion_blacklist';
    }

    public function attributes(): array
    {
        return["opinion_id", "duration","blacklister_id","blacklisted_date"];
    }

    public function rules(): array
    {
        return [];
    }
}