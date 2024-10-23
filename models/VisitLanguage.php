<?php

namespace app\models;

use app\core\DBModel;

class   VisitLanguage extends DBModel
{
    public int $offer_id = 0;
    public string $language = '0.0';



    public static function tableName(): string
    {
        return 'visit_language';
    }

    public function attributes(): array
    {
        return ['offer_id', 'language'];
    }

    public static function pk(): string
    {
        return 'language_id';
    }

    public function rules(): array
    {
        return [
            'offer_id' => [self::RULE_REQUIRED],
            'language' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]]
        ];
    }
}