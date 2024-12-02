<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferStatusHistory extends DBModel
{
    public int $id = 0;
    public int $offer_id = 0;
    /**
     * @var 'online' | 'offline'
     */
    public string $switch_to = '';
    public string $created_at = '';

    public static function tableName(): string
    {
        return 'offer_status_history';
    }

    public function attributes(): array
    {
        return ['offer_id', 'switch_to'];
    }

    public function rules(): array
    {
        return [];
    }
}