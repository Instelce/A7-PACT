<?php

namespace app\models;

use app\core\DBModel;

class Account extends DBModel
{
    public string $created_at = '';
    public string $updated_at = '';


    public static function tableName(): string
    {
        // TODO: Implement tableName() method
        return 'account';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['created_at', 'updated_at'];
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

        ];

    }
}