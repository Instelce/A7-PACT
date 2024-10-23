<?php

namespace app\models;
use app\core\DBModel;

class IsOnRestaurantMenu extends DBModel {
    public int $id = 0;
    public int $offer_id =0;
    public int $meal_id =0;
    public static function tableName(): string
    {
        return 'is_on_restaurant_menu';
    }

    public function attributes(): array
    {
        return ['offer_id', 'meal_id'];
    }

    public static function pk(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
        ];
    }
}