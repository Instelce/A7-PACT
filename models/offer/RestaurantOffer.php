<?php

namespace app\models\offer;
use app\core\DBModel;
use app\models\IsOnRestaurantMenu;
use app\models\offer\schedule\ActivitySchedule;
use app\models\offer\schedule\RestaurantSchedule;

class RestaurantOffer extends DBModel
{
    public int $offer_id = 0;
    public string $url_image_carte = '';

    /**
     * @var float 1, 2 or 3 (€, €€, €€€)
     */
    public float $range_price = 0;

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

    public function addSchedule($scheduleId)
    {
        $restaurantSchedule = new RestaurantSchedule();
        $restaurantSchedule->restaurant_id = $this->offer_id;
        $restaurantSchedule->schedule_id = $scheduleId;
        $restaurantSchedule->save();
    }

    public function addMeal($meal_id)
    {
        $isOnRestaurantMenu = new IsOnRestaurantMenu();
        $isOnRestaurantMenu->meal_id = $meal_id;
        $isOnRestaurantMenu->offer_id = $this->offer_id;
        $isOnRestaurantMenu->save();
    }
}