<?php

namespace app\models;
use app\core\DBModel;

class Message extends DBModel
{
    public int $id = 0;
    public string $sended_date = '';
    public ?string $modified_date = '';
    public int $sender_id = 0;
    public int $receiver_id = 0;
    public bool $deleted = false;
    public bool $seen = false;
    public string $content = '';

    public static function tableName(): string
    {
        return 'message';
    }

    public function attributes(): array
    {
        return ['sended_date', 'modified_date', 'sender_id', 'receiver_id', 'deleted', 'seen', 'content'];
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