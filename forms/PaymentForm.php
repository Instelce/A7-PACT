<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\UserAccount;
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
    public string $passwordCheckPayment = '';
    public RibMeanOfPayment|null|false $ribPayment = null;
    public CbMeanOfPayment|null|false $cbPayment = null;


    public function __construct($payment_id)
    {
        $this->ribPayment = RibMeanOfPayment::findOneByPk($payment_id);
        if ($this->ribPayment) {
            $this->titular_name = $this->ribPayment->name;
            $this->iban = $this->ribPayment->iban;
            $this->bic = $this->ribPayment->bic;
        }

        $this->cbPayment = CbMeanOfPayment::findOneByPk($payment_id);
        if ($this->cbPayment) {
            $this->card_name = $this->cbPayment->name;
            $this->card_number = $this->cbPayment->card_number;
            $this->expiration_date = $this->cbPayment->expiration_date;
            $this->cvv = $this->cbPayment->cvv;
        }
    }

    public function update()
    {
        $request = Application::$app->request;
        $this->ribPayment->loadData($request->getBody());
        $this->ribPayment->update();
        $this->cbPayment->loadData($request->getBody());
        $this->cbPayment->update();
        return true;
    }

    public function passwordMatch()
    {
        /**
         * @var UserAccount $user
         */
        $user = Application::$app->user;

        if (!password_verify($this->passwordCheckPayment, $user->password)) {
            $this->addError('passwordCheckPayment', 'Mot-de-passe incorrect.');
            return false;
        }

        return true;
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
            'cvv' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 3]],
            'passwordCheckPayment' => [self::RULE_REQUIRED]
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
            'cvv' => 'Cryptogramme',
            'passwordCheckPayment' => 'Mot de passe'
        ];
    }

    public function placeholders(): array
    {
        return [
            'titular_name' => 'Nom du titulaire',
            'iban' => 'FR76 3000 3000 3000 3000 3000 00',
            'bic' => 'BNPAFRPPXXX',

            'card_name' => 'Nom du titulaire',
            'card_number' => '1234 5678 9012 3456',
            'expiration_date' => 'MM/AA',
            'cvv' => '123',
            'passwordCheckPayment' => '********'
        ];
    }
}