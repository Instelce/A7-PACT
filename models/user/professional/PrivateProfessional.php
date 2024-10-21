<?php

namespace app\models\user\professional;
use app\core\DBModel;

class PrivateProfessional extends DBModel
{

    public int $pro_id = 0;

    public string $last_veto = '';


    public static function tableName(): string
    {
        return 'private_professional';
    }

    public function attributes(): array
    {
        return ['last_veto'];
    }

    public static function pk(): string
    {
        return 'pro_id';
    }

    public function rules(): array
    {
        return [
            'last_veto' => [self::RULE_REQUIRED]
        ];
    }

    public function professional(): ProfessionalUser {
        return ProfessionalUser::findOne(['pro_id' => $this->pro_id]);
    }
}