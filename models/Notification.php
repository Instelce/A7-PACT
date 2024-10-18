<?php

namespace app\models;
use app\core\DBModel;

class Notification extends DBModel
{

    public string $send_at = '';
    public string $reception_day = '';
    public string $open_at = '';
    public bool $read = false;
    public string $content = '';



    public static function tableName(): string
    {
        // TODO: Implement tableName() method
        return 'account';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['send_at', 'reception_day', 'open_at', 'read', 'content'];
    }

    public static function pk(): string
    {
        // TODO: Implement pk() method.
        return 'notif_id';
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'send_at' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'firstname' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50], [self::RULE_UNIQUE]],
            'pseudo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50], [self::RULE_UNIQUE]],
            'allows_notifications' => [self::RULE_REQUIRED]
        ];

    }
}