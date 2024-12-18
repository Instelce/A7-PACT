<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferTag extends DBModel
{
    public int $id = 0;
    public string $name = '';

    public static function tableName(): string
    {
        return 'offer_tag';
    }

    public function attributes(): array
    {
        return ['name'];
    }

    public function updateAttributes(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
        ];
    }
}