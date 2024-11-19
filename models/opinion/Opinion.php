<?php

namespace app\models\opinion;

use app\core\DBModel;

class Opinion extends DBModel
{
    public int $id = 0;
    public int $rating = 0;
    public string $title = "";
    public string $comment = "";
    public string $visit_date = "";
    public string $visit_context = "";

    public int $account_id = 0;

    public string $created_at = "";
    public string $updated_at = "";

    public static function tableName(): string
    {
        return 'opinion';
    }

    public function attributes(): array
    {
        return ['rating', 'title', 'comment', 'visit_date', 'visit_context', 'account_id'];
    }

    public function rules(): array
    {
        return [
            'rating' => [self::RULE_REQUIRED],
            'title' => [self::RULE_REQUIRED, [self::RULE_MAX, 128]],
            'comment' => [self::RULE_REQUIRED, [self::RULE_MAX, 255]],
            'visit_date' => [self::RULE_REQUIRED],
            'visit_context' => [self::RULE_REQUIRED],
        ];
    }
}