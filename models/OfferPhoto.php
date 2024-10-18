<?php

namespace app\models;

use app\core\DBModel;

class OfferPhoto extends DBModel
{
    public int $id = 0;
    public string $url_photo = '';
    public int $offer_id = 0;
    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'offer_photo';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['url_photo', 'offer_id'];
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
            'url_photo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'offer_id' => [self::RULE_REQUIRED],
        ];
    }
}