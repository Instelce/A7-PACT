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
use app\models\user\professional\PublicProfessional;

class PrivateProfessionalRegister extends Model
{
    public const PAYMENT = 1;
    public const NOPAYMENT = 0;

    public const ACCEPT_CONDITIONS = 1;
    public const REFUSE_CONDITIONS = 0;

    public const ACCEPT_NOTIFICATIONS = 1;
    public const REFUSE_NOTIFICATIONS = 0;



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
    public int $notifications = self::REFUSE_NOTIFICATIONS;
    public string $titulaire = '';
    public string $iban = '';
    public string $bic = '';
    public string $cardnumber = '';
    public string $expirationdate = '';
    public string $cryptogram = '';
    public string $paypallink = '';


    public function rules(): array
    {
        return [
            'siren' => [[self::RULE_UNIQUE, 'attributes' => 'siren', 'class' => ProfessionalUser::class], [self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [[self::RULE_REQUIRED], [self::RULE_UNIQUE, 'attributes' => 'mail', 'class' => UserAccount::class], [self::RULE_MAIL]],
            'streetname' => [self::RULE_REQUIRED],
            'postaleCode' => [[self::RULE_REQUIRED], [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED],
            'phone' => [[self::RULE_REQUIRED], [self::RULE_MAX, 'max' => 10], [self::RULE_UNIQUE, 'attributes' => 'phone', 'class' => ProfessionalUser::class]],
            'password' => [[self::RULE_REQUIRED], [self::RULE_PASSWORD]],
            'passwordConfirm' => [[self::RULE_REQUIRED], [self::RULE_MATCH, 'match' => 'password']],
            'titulaire' => [self::RULE_MAX, 'max' => 255],
            'iban' => [[self::RULE_MAX, 'max' => 34], [self::RULE_UNIQUE, 'attributes' => 'iban', 'class' => RibMeanOfPayment::class]],
            'bic' => [self::RULE_MAX, 'max' => 11],
            'cardnumber' => [[self::RULE_MAX, 'max' => 16],[self::RULE_UNIQUE, 'attributes' => 'iban', 'class' => CbMeanOfPayment::class]],
            'expirationdate' => [self::RULE_EXP_DATE],
            'cryptogram' => [self::RULE_MAX, 'max' => 3]
        ];
    }

    public function register()
    {
        /**
         * @var PublicProfessional $proPublic
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
        $userAccount->avatar_url = "https://ui-avatars.com/api/?size=128&name=$this->denomination";
        $userAccount->address_id = $address->id;
        $userAccount->save();

        $proUser = new ProfessionalUser();
        $proUser->user_id = $account->id;
        $proUser->siren = $this->siren;
        $proUser->denomination = $this->denomination;
        $proUser->code = Utils::generateUUID();
        $proUser->phone = $this->phone;
        $proUser->save();

        $meanOfPayment = new MeanOfPayment();

        if('iban' && 'bic' != ''){
            $payment = new RibMeanOfPayment();
            $payment->rib_id = $meanOfPayment->payment_id;
            $payment->name = $this->titulaire;
            $payment->iban = $this->iban;
            $payment->bic = $this->bic;
            $payment->save();
        }
        else if ('cardnumber' && 'cryptogram' && 'expirationdate' != '') {
            $payment = new CbMeanOfPayment();
            $payment->cb_id = $meanOfPayment->payment_id;
            $payment->name = $this->titulaire;
            $payment->card_number = $this->cardnumber;
            $payment->expiration_date = $this->expirationdate;
            $payment->cvv = $this->cryptogram;
            $payment->save();
        }
        else {
            $payment = new PaypalMeanOfPayment();
            $payment->paypal_id = $meanOfPayment->payment_id;
            $payment->paypalurl = $this->paypallink;
            $payment->save();
        }

        $proPrivate = new PrivateProfessional();
        $proPrivate->pro_id = $account->id;
        $proPrivate->payment_id = $payment->id;
        $proPrivate->save();


        Application::$app->login($userAccount);
        return true;
    }

    public function labels(): array
    {
        return [
            'denomination' => 'Dénomination',
            'siren' => 'SIREN',
            'mail' => 'E-mail',
            'streetnumber' => 'Numéro de rue',
            'streetname' => 'Nom de rue',
            'postaleCode' => 'Code postal',
            'city' => 'Ville',
            'country' => 'Pays',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'passwordConfirm' => 'Confirmez votre mot de passe',
            'payment' => 'Je souhaite de rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)',
            'conditions' => 'J\'accepte les conditions géénrales d\'utilisation',
            'notifications' => 'J\'autorise l\'envoi de notifications'
        ];
    }

    public function placeholders(): array
    {
        return [
            'denomination' => 'Votre association / entreprise',
            'siren'=> '362 521 879',
            'mail' => 'example@email.com',
            'streetnumber' => '12',
            'streetname' => 'Édouard Branly',
            'postaleCode' => '22300',
            'city' => 'Lannion',
            'country' => 'France',
            'phone' => '0601020304',
            'password' => '********',
            'passwordConfirm' => '********'
        ];
    }
}