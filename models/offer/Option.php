<?php

namespace app\models\offer;

use app\core\DBModel;

class Option extends DBModel
{
    public int $id = 0;
    public string $type = '';
    public float $price = 0;

    public static function tableName(): string
    {
        return 'option';
    }

    public function attributes(): array
    {
        return ['type', 'price'];
    }

    public function rules(): array
    {
        return [];
    }

    public function french()
    {
        return match ($this->type) {
            'en_relief' => 'en relief',
            'a_la_une' => 'à la une',
            default => 'Erreur',
        };
    }
}