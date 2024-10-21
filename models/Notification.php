<?php

namespace app\models;
use app\core\DBModel;

class Notification extends DBModel
{
    public const IS_READ = 1;

    public const IS_NOT_READ = 0;
    public int $notif_id = 0;
    public string $send_at = '';
    public string $reception_day = '';
    public string $open_at = '';
    public int $is_read = self::IS_NOT_READ;
    public string $content = '';
    public int $user_id = 0;


    public static function tableName(): string
    {
        return 'notification';
    }

    public function attributes(): array
    {
        return ['send_at', 'reception_day', 'open_at', 'read', 'content', 'user_id'];
    }

    public static function pk(): string
    {
        return 'notif_id';
    }

    public function rules(): array
    {
        return [
            'reception_day' => [self::RULE_REQUIRED, self::RULE_DATE],
            'open_at' => [self::RULE_REQUIRED],
            'is_read' => [self::RULE_REQUIRED],
            'content' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'user_id' => [self::RULE_REQUIRED],
        ];
    }
}