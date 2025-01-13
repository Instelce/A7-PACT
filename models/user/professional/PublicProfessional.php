<?php

namespace app\models\user\professional;
use app\core\DBModel;

class PublicProfessional extends DBModel
{

  public int $pro_id = 0;

  public static function tableName(): string
  {
    return 'public_professional';
  }

  public function attributes(): array
  {
    return ['pro_id'];
  }

  public static function pk(): string
  {
    return 'pro_id';
  }

  public function rules(): array
  {
    return [];
  }

  public function professional(): ProfessionalUser {
      return ProfessionalUser::findOne(['pro_id' => $this->pro_id]);
  }
}