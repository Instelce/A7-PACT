<?php

namespace app\models\opinion;

use app\core\DBModel;
class OpinionReply extends DBModel
{

    public int $opinion_reply_id = 0;

    public int $opinion_id = 0;
    public string $comment = "";
    public string $created_at = "";

    public static function pk() : string
    {
        return "opinion_reply_id";
    }

    public static function tableName(): string
    {
        return 'opinion_reply';
    }

    public function attributes(): array
    {
        return["comment", "created_at", "opinion_id", 'opinion_reply_id'];
    }

    public function rules(): array
    {
        return [

        ];
    }

    public function labels(): array
    {
        return [
        ];
    }
}