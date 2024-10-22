<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferIsTagged extends DBModel
{
    public int $id = 0;
    public int $offer_id = 0;
    public int $tag_id = 0;

    public static function tableName(): string
    {
        return 'offer_is_tagged';
    }

    public function attributes(): array
    {
        return ['offer_id', 'tag_id'];
    }

    public function rules(): array
    {
        return [];
    }
}