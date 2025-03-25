<?php

namespace app\models\opinion;

use app\core\DBModel;
use app\models\offer\Offer;
use app\models\opinion\Opinion;

class OpinionBlackList extends DBModel
{
    public static $duration = 300; // 300 secondes

    public int $id_blacklist = 0;
    public String $blacklisted_time = "";
    public int $opinion_id = 0;



    public static function pk() : string
    {
        return "id_blacklist";
    }

    public static function tableName(): string
    {
        return 'opinion_blacklist';
    }

    public function attributes(): array
    {
        return['id_blacklist', 'blacklisted_time', 'opinion_id'];
    }

    public function rules(): array
    {
        return [
            'blacklisted_time' => [self::RULE_REQUIRED, self::RULE_DATE],
            'opinion_id' => [self::RULE_REQUIRED]

        ];
    }

    public function get_opinion(): Opinion
    {
        return Opinion::findOne($this->opinion_id);
    }

    public function can_earn_token(): float
    {
        return time() - $this->blacklisted_time >= self::$duration;
    }

}