<?php

namespace app\models\payment;

use app\core\DBModel;

class MeanOfPayment extends DBModel
{
    public int $id = 0;

    public static function tableName(): string
    {
        return 'mean_of_payment';
    }

    public function attributes(): array
    {
        return [];
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

    public function isCbPayment(): bool
    {
        return CbMeanOfPayment::findOneByPk($this->payment_id) !== false;
    }

    public function isRibPayment(): bool
    {
        return RibMeanOfPayment::findOneByPk($this->payment_id) !== false;
    }

    public function isPaypalPayment(): bool
    {
        return PaypalMeanOfPayment::findOneByPk($this->payment_id) !== false;
    }


    public function specific(){
        if($this->isCbPayment()){
            return CbMeanOfPayment::findOneByPk($this->payment_id);
        } else if ($this->isRibPayment()){
            return RibMeanOfPayment::findOneByPk($this->payment_id);
        } else if ($this->isPaypalPayment()){
            return PaypalMeanOfPayment::findOneByPk($this->payment_id);
        }
    }
}