<?php

namespace app\models\opinion;

use app\core\DBModel;
use app\models\offer\Offer;
use app\models\opinion\Opinion;

class OpinionBlackList extends DBModel
{
    public static $duration = 300; // 300 secondes

    public int $id_blacklist = 0;
    public String $blacklisted_date = "";
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
        return['id_blacklist', 'blacklisted_date', 'opinion_id'];
    }

    public function rules(): array
    {
        return [
            'blacklisted_date' => [self::RULE_REQUIRED, self::RULE_DATE],
            'opinion_id' => [self::RULE_REQUIRED]

        ];
    }

    public function get_opinion(): Opinion
    {
        return Opinion::findOne($this->opinion_id);
    }
    public function blacklist($id_opinion)
    {
        $opinion = Opinion::findOneByPk($id_opinion);
        $selected_offer = Offer::findOneByPk($opinion->offer_id);
        if (!$selected_offer->offer_type_id === 2) {
            if (!$selected_offer->nbJetonsDispo > 0) {
                $blacklisted = new OpinionBlackList();
                $blacklisted->blacklisted_date = date('y-m-d');
                $blacklisted->opinion_id = $id_opinion;

                $blacklisted->save();
                $opinion->blacklisted = true;
                $blacklisted->update();
                $selected_offer->nbJetonsDispo--;
                $selected_offer->update();

                return 1;
            }
            return 0;
        }
        return 0;
    }

}