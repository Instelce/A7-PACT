<?php

namespace app\models;

use app\core\DBModel;

class OfferTag extends DBModel
{
    public int $id = 0;
    public string $name = '';
    public int $offer_id = 0;
    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'taggage';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['name', 'offer_id'];
    }

    public static function pk(): string
    {
        // TODO: Implement pk() method.
        return 'id';

    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'offer_id' => [self::RULE_REQUIRED]
        ];
    }
}