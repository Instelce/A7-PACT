<?php

namespace app\models\opinion;

use app\core\DBModel;
class OpinionDislike extends DBModel
{

    public int $opinion_id = 0;
    public int $account_id = 0;

    public static function pk() : string
    {
        return "opinion_id";
    }

    public static function tableName(): string
    {
        return 'opinion_dislike';
    }

    public function attributes(): array
    {
        return["opinion_id", "account_id"];
    }

    public function rules(): array
    {
        return [];
    }
}