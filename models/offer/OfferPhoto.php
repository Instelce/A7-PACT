<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferPhoto extends DBModel
{
    public int $id = 0;
    public string $url_photo = '';
    public int $offer_id = 0;

    public static function tableName(): string
    {
        return 'offer_photo';
    }

    public function attributes(): array
    {
        return ['url_photo', 'offer_id'];
    }

    public function rules(): array
    {
        return [
            'url_photo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'offer_id' => [self::RULE_REQUIRED],
        ];
    }
}