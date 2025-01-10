<?php

namespace app\models\user\professional;
use app\core\DBModel;
use app\models\payment\MeanOfPayment;

class PrivateProfessional extends DBModel
{

    public int $pro_id = 0;

    public string $last_veto = '';

    public int $payment_id = 0;


    public static function tableName(): string
    {
        return 'private_professional';
    }

    public function attributes(): array
    {
        return ['pro_id', 'last_veto', 'payment_id'];
    }

    public static function pk(): string
    {
        return 'pro_id';
    }

    public function rules(): array
    {
        return [
            'last_veto' => [self::RULE_REQUIRED],
            'payment_id' => [self::RULE_REQUIRED]
        ];
    }

    public function professional(): ProfessionalUser {
        return ProfessionalUser::findOne(['pro_id' => $this->pro_id]);
    }

    public function payment(){
        $mean = MeanOfPayment::findOne(['payment_id' => $this->payment_id])->specific();
        if($mean === null){
            return null;
        }
        return $mean->specific();
    }
}