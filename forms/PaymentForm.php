<?php

namespace app\forms;

use app\core\Model;
use app\models\payment\CbMeanOfPayment;
use app\models\payment\RibMeanOfPayment;

class PaymentForm extends Model
{
    public string $titular_name = '';
    public string $iban = '';
    public string $bic = '';

    public string $card_name = '';
    public string $card_number = '';
    public string $expiration_date = '';
    public string $cvv = '';

    public function __construct($payment_id)
    {
        /** @var RibMeanOfPayment $rib */
        $rib = RibMeanOfPayment::findOneByPk($payment_id);
        if ($rib) {
            $this->titular_name = $rib->name;
            $this->iban = $rib->iban;
            $this->bic = $rib->bic;
        }

        /** @var CbMeanOfPayment $cb */
        $cb = CbMeanOfPayment::findOneByPk($payment_id);
        if ($cb) {
            $this->card_name = $cb->name;
            $this->card_number = $cb->card_number;
            $this->expiration_date = $cb->expiration_date;
            $this->cvv = $cb->cvv;
        }
    }

    public function rules(): array
    {
        return [
            'titular_name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'iban' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 34]],
            'bic' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 11]],

            'card_name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'card_number' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 16]],
            'expiration_date' => [self::RULE_REQUIRED, self::RULE_EXP_DATE],
            'cvv' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 3]]
        ];
    }

    public function labels(): array
    {
        return [
            'titular_name' => 'Titulaire du compte',
            'iban' => 'IBAN',
            'bic' => 'BIC',

            'card_name' => 'Titulaire de la carte',
            'card_number' => 'Numéro de carte',
            'expiration_date' => 'Date d\'expiration',
            'cvv' => 'Cryptogramme'
        ];
    }

    public function placeholders(): array
    {
        return [
            'titular_name' => 'Nom du titulaire',
            'iban' => 'FR76 3000 3000 3000 3000 3000 300',
            'bic' => 'BNPAFRPPXXX',

            'card_name' => 'Nom du titulaire',
            'card_number' => '1234 5678 9012 3456',
            'expiration_date' => 'MM/AA',
            'cvv' => '123'
        ];
    }
}