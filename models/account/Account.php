<?php

namespace app\models\account;

use app\core\DBModel;

class Account extends DBModel
{
    public int $id = 0;
    public string $created_at = '';
    public string $updated_at = '';

    public static function tableName(): string
    {
        return 'account';
    }

    public function attributes(): array
    {
        return ['created_at', 'updated_at'];
    }

    public static function pk(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'create_at' => [self::RULE_REQUIRED, self::RULE_DATE],
            'update_at' => [self::RULE_REQUIRED, self::RULE_DATE]
        ];

    }
}