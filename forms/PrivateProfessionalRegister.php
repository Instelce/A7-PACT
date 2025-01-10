<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\Account;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\payment\CbMeanOfPayment;
use app\models\payment\MeanOfPayment;
use app\models\payment\PaypalMeanOfPayment;
use app\models\payment\RibMeanOfPayment;
use app\models\user\professional\PrivateProfessional;
use app\models\user\professional\ProfessionalUser;
use app\core\Utils;

class PrivateProfessionalRegister extends Model
{
    public const PAYMENT = 1;
    public const NOPAYMENT = 0;

    public const ACCEPT_CONDITIONS = 1;
    public const REFUSE_CONDITIONS = 0;


    public string $denomination = '';
    public string $siren = '';

    public string $mail = '';
    public int $streetnumber = 0;
    public string $streetname = '';
    public string $postaleCode = '';
    public string $city = '';
    public string $phone = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public int $payment = self::NOPAYMENT;
    public int $conditions = self::REFUSE_CONDITIONS;
    public string $titularAccount = '';
    public string $iban = '';
    public string $bic = '';
    public string $titularCard = '';
    public string $cardnumber = '';
    public string $expirationdate = '';
    public string $cryptogram = '';
    public string $paypallink = '';

    public function register()
    {
        /**
         * @var PrivateProfessional $proPrivate
         */

        $account = new Account();
        $account->save();

        $address = new Address();
        $address->number = $this->streetnumber;
        $address->street = $this->streetname;
        $address->postal_code = $this->postaleCode;
        $address->city = $this->city;
        $address->save();

        $userAccount = new UserAccount();
        $userAccount->account_id = $account->id;
        $userAccount->mail = $this->mail;
        $userAccount->password = password_hash($this->password, PASSWORD_DEFAULT);
        $userAccount->address_id = $address->id;
        $userAccount->avatar_url = "https://ui-avatars.com/api/?size=128&name=$this->denomination";
        $userAccount->save();

        $proUser = new ProfessionalUser();
        $proUser->user_id = $userAccount->account_id;
        $proUser->siren = $this->siren;
        $proUser->denomination = $this->denomination;
        $proUser->code = Utils::generateUUID();
        $proUser->phone = str_replace(' ', '', $this->phone);
        $proUser->save();

        $meanOfPayment = new MeanOfPayment();
        $meanOfPayment->save();
        echo 'ok';

        if ($this->iban != NULL && $this->bic != NULL) {
            $payment = new RibMeanOfPayment();
            $payment->id = $meanOfPayment->payment_id;
            $payment->name = $this->titularAccount;
            $payment->iban = $this->iban;
            $payment->bic = $this->bic;
            $payment->save();
            echo 'ok2';

        } elseif ($this->cardnumber != NULL && $this->cryptogram != NULL && $this->expirationdate != NULL) {
            $payment = new CbMeanOfPayment();
            $payment->id = $meanOfPayment->payment_id;
            $payment->name = $this->titularCard;
            $payment->card_number = $this->cardnumber;
            $payment->expiration_date = $this->expirationdate;
            $payment->cvv = $this->cryptogram;
            $payment->save();
            echo 'ok3';
        }
//        else {
//            $payment = new PaypalMeanOfPayment();
//            $payment->id = $meanOfPayment->payment_id;
//            $payment->paypalurl = $this->paypallink;
//            if (!$payment->validate()) {
//                return false;
//            }
//            $payment->save();
//        }

        echo "wow";
        $proPrivate = new PrivateProfessional();
        $proPrivate->pro_id = $account->id;
        $proPrivate->last_veto = date('Y-m-d');
        $proPrivate->payment_id = $meanOfPayment->id;
        $proPrivate->save();
        echo 'ok4';

        Application::$app->login($userAccount);
        return true;
    }



    public function rules(): array
    {
        return [
            'siren' =>[self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'siren', 'class' => ProfessionalUser::class], self::RULE_SIREN],
            'denomination' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'denomination', 'class' => ProfessionalUser::class]],
            'mail' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'mail', 'class' => UserAccount::class], self::RULE_MAIL],
            'streetnumber' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'streetname' => [self::RULE_REQUIRED],
            'postaleCode' => [self::RULE_REQUIRED, self::RULE_POSTAL],
            'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'phone' => [self::RULE_REQUIRED, self::RULE_PHONE, [self::RULE_UNIQUE, 'attribute' => 'phone', 'class' => ProfessionalUser::class]],
            'password' => [self::RULE_REQUIRED, self::RULE_PASSWORD],
            'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'titular-account' => [[self::RULE_MAX, 'max' => 255]],
            'iban' => [],
            'bic' => [[self::RULE_MAX, 'max' => 11]],
            'cardnumber' => [],
            'titular-card' => [],
            'expirationdate' => [self::RULE_EXP_DATE],
            'cryptogram' => [[self::RULE_MAX, 'max' => 3], self::RULE_NUMBER]
        ];
    }


    public function labels(): array
    {
        return [
            'denomination' => 'Dénomination',
            'siren' => 'Siren',
            'mail' => 'E-mail',
            'streetnumber' => 'Numéro de rue',
            'streetname' => 'Nom de rue',
            'postaleCode' => 'Code postal',
            'city' => 'Ville',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'passwordConfirm' => 'Confirmez votre mot de passe',
            'payment' => 'Je souhaite de rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)',
            'conditions' => 'J\'accepte les conditions géénrales d\'utilisation',
            'notifications' => 'J\'autorise l\'envoi de notifications',
            'titular-account' => 'Titulaire du compte',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'titular-card' => 'Titulaire de la carte',
            'cardnumber' => 'Numéro de carte',
            'expirationdate' => 'Date d\'expiration',
            'cryptogram' => 'CVV'
        ];
    }

    public function placeholders(): array
    {
        return [
            'denomination' => 'Votre association / entreprise',
            'siren'=> '362521879',
            'mail' => 'example@email.com',
            'streetnumber' => '12',
            'streetname' => 'Édouard Branly',
            'postaleCode' => '22300',
            'city' => 'Lannion',
            'phone' => '06 01 02 03 04',
            'password' => '************',
            'passwordConfirm' => '************',
            'titular-account' => 'Nom entreprise / Nom personne',
            'iban' => 'FR76 XXXX XXXX XXXX XXXX XXXX XXXX XX',
            'bic' => 'vore BIC (optionnel)',
            'titular-card' => 'Nom entreprise / Nom personne',
            'cardnumber' => 'XXXX XXXX XXXX XXXX',
            'expirationdate' => 'MM/AA',
            'cryptogram' => '000'
        ];
    }
}