<?php

namespace app\models\offer;
use app\core\DBModel;

class RestaurantOffer extends DBModel
{
    public int $offer_id = 0;
    public string $url_image_carte ='';

    /**
     * @var int 1, 2 or 3 (€, €€, €€€)
     */
    public int $range_price = 0;

    public static function tableName(): string
    {
        return 'restaurant_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'url_image_carte', 'range_price'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'url_image_carte' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'range_price' => [self::RULE_REQUIRED],
        ];
    }
}