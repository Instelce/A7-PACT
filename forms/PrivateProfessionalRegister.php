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
    public string $titularAccount = '';
    public string $iban = '';
    public string $bic = '';
    public string $titularCard = '';
    public string $cardnumber = '';
    public string $expirationdate = '';
    public string $cryptogram = '';
    public string $paypallink = '';


    public function rules(): array
    {
        return [
            'siren' =>[self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'siren', 'class' => ProfessionalUser::class], [self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'mail', 'class' => UserAccount::class], self::RULE_MAIL],
            'streetnumber' => [self::RULE_REQUIRED],
            'streetname' => [self::RULE_REQUIRED],
            'postaleCode' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 10], [self::RULE_UNIQUE, 'attribute' => 'phone', 'class' => ProfessionalUser::class]],
            'password' => [self::RULE_REQUIRED, self::RULE_PASSWORD],
            'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'titular-account' => [[self::RULE_MAX, 'max' => 255]],
            'iban' => [[self::RULE_MAX, 'max' => 34], [self::RULE_UNIQUE, 'attribute' => 'iban', 'class' => RibMeanOfPayment::class]],
            'bic' => [[self::RULE_MAX, 'max' => 11]],
            'cardnumber' => [[self::RULE_MAX, 'max' => 16], [self::RULE_UNIQUE, 'attribute' => 'card_number', 'class' => CbMeanOfPayment::class]],
            'titular-card' => [[self::RULE_MAX, 'max' => 255]],
            'expirationdate' => [self::RULE_EXP_DATE],
            'cryptogram' => [[self::RULE_MAX, 'max' => 3]]
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
        $meanOfPayment->save();

        if('iban' && 'bic' != ''){
            $payment = new RibMeanOfPayment();
            $payment->id = $meanOfPayment->payment_id;
            $payment->name = $this->titularAccount;
            $payment->iban = $this->iban;
            $payment->bic = $this->bic;
            $payment->save();
        }
        else if ('cardnumber' && 'cryptogram' && 'expirationdate' != '') {
            $payment = new CbMeanOfPayment();
            $payment->id = $meanOfPayment->payment_id;
            $payment->name = $this->titularCard;
            $payment->card_number = $this->cardnumber;
            $payment->expiration_date = $this->expirationdate;
            $payment->cvv = $this->cryptogram;
            $payment->save();
        }
        else {
            $payment = new PaypalMeanOfPayment();
            $payment->id = $meanOfPayment->payment_id;
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
            'password' => '********',
            'passwordConfirm' => '********',
            'titular-account' => 'Nom entreprise / Nom personne',
            'iban' => 'votre iban',
            'bic' => 'vore BIC (optionnel)',
            'titular-card' => 'Nom entreprise / Nom personne',
            'cardnumber' => 'XXXX XXXX XXXX XXXX',
            'expirationdate' => '07/26',
            'cryptogram' => '000'
        ];
    }
}