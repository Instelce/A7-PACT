<?php

namespace app\models\user\professional;
use app\core\DBModel;

class PublicProfessional extends DBModel
{

  public int $pub_pro_id = 0;


  public static function tableName(): string
  {
    return 'public_professional';
  }

  public function attributes(): array
  {
    return ['pub_pro_id'];
  }

  public static function pk(): string
  {
    return 'pub_pro_id';
  }


  public function rules(): array
  {
    return [];
  }
}