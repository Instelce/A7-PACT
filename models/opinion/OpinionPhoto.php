<?php

namespace app\models\opinion;

use app\core\DBModel;

class OpinionPhoto extends DBModel
{
    public int $id = 0;
    public string $photo_url = '';
    public int $opinion_id = 0;

    public static function tableName(): string
    {
        return 'opinion_photo';
    }

    public function attributes(): array
    {
        return ['photo_url', 'opinion_id'];
    }

    public function rules(): array
    {
        return [];
    }
}