<?php

namespace app\models\payment;

use app\core\DBModel;

class MeanOfPayment extends DBModel
{
    public int $id = 0;

    public string $created_at = '';

    public static function tableName(): string
    {
        return 'mean_of_payment';
    }

    public function attributes(): array
    {
        return ['created_at'];
    }

    public static function pk(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'created_at' => [self::RULE_REQUIRED, self::RULE_DATE]
        ];

    }

    public function isCbPayment(): bool
    {
        return CbMeanOfPayment::findOneByPk($this->id) !== false;
    }

    public function isRibPayment(): bool
    {
        return RibMeanOfPayment::findOneByPk($this->id) !== false;
    }

    public function isPaypalPayment(): bool
    {
        return PaypalMeanOfPayment::findOneByPk($this->id) !== false;
    }


    public function specific(){
        if($this->isCbPayment()){
            return CbMeanOfPayment::findOneByPk($this->id);
        } else if ($this->isRibPayment()){
            return RibMeanOfPayment::findOneByPk($this->id);
        } else if ($this->isPaypalPayment()){
            return PaypalMeanOfPayment::findOneByPk($this->id);
        }
        return null;
    }
}