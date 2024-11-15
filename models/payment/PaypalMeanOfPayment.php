<?php

namespace app\models\payment;

use app\core\DBModel;

class PaypalMeanOfPayment extends DBModel
{
    public int $paypal_id = 0;
    public string $paypal_url = '';

    public static function tableName(): string
    {
        return 'paypal_mean_of_payment';
    }

    public function attributes(): array
    {
        return ['paypal_url'];
    }

    public static function pk(): string
    {
        return 'paypal_id';
    }

    public function rules(): array
    {
        return [
            'paypal_url' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]]
        ];
    }

    public function mean_of_payment(): MeanOfPayment {
        return MeanOfPayment::findOne(['payment_id' => $this->paypal_id]);
    }
}