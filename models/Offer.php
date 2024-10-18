<?php

namespace app\models;

use app\core\DBModel;

class Offer extends DBModel
{
    public const STATUS_ONLINE = 0;
    public const STATUS_OFFLINE = 1;

    public int $id = 0;
    public string $title = '';
    public string $summary = '';
    public string $description = '';
    public int $likes = 0;
    public int $offline = self::STATUS_OFFLINE;
    public string $offline_date = '';
    public string $last_online_date = '';
    public int $view_counter = 0;
    public int $click_counter = 0;
    public string $website = '';
    public string $phone_number = '';
    public string $created_at = '';
    public string $updated_at = '';

    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'offer';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['title', 'summary', 'description', 'likes', 'offline', 'offline_date', 'last_online_date', 'view_counter', 'click_counter', 'website', 'phone_number', 'created_at', 'updated_at'];
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
            'title' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 60]],
            'summary' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]],
            'description' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 1024]],

        ];
    }
}