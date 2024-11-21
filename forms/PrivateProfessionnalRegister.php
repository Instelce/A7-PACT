<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\Account;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\user\professional\ProfessionalUser;
use app\core\Utils;
use app\models\user\professional\PublicProfessional;

class PrivateProfessionalRegister extends Model
{
    public const PAYEMENT = 1;
    public const NOPAYEMENT = 0;

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
    public int $payement = self::NOPAYEMENT;
    public int $conditions = self::REFUSE_CONDITIONS;
    public int $notifications = self::REFUSE_NOTIFICATIONS;
    public string $titulaire = '';
    public string $iban = '';
    public string $bic = '';
    public string $cardnumber = '';
    public string $expirationdate = '';
    public string $cryptogram = '';


    public function rules(): array
    {
        return [
            'siren' => [[self::RULE_UNIQUE, 'attributes' => 'siren', 'class' => ProfessionalUser::class], [self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [[self::RULE_REQUIRED], [self::RULE_UNIQUE, 'attributes' => 'mail', 'class' => UserAccount::class], [self::RULE_MAIL]],
            'streetname' => [self::RULE_REQUIRED],
            'postaleCode' => [[self::RULE_REQUIRED], [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED],
            'country' => [self::RULE_REQUIRED],
            'phone' => [[self::RULE_REQUIRED], [self::RULE_MAX, 'max' => 10]],
            'password' => [[self::RULE_REQUIRED], [self::RULE_PASSWORD]],
            'passwordConfirm' => [[self::RULE_REQUIRED], [self::RULE_MATCH, 'match' => 'password']],
            'titulaire' => [self::RULE_MAX, 'max' => 255],
            'iban' => [self::RULE_MAX, 'max' => 34],
            'bic' => [self::RULE_MAX, 'max' => 11],
            'cardnumber' => [self::RULE_MAX, 'max' => 16],
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
        $userAccount->password = password_hash($this->password);
        $userAccount->address_id = $address->id;
        $userAccount->save();

        $proUser = new ProfessionalUser();
        $proUser->user_id = $account->id;
        $proUser->siren = $this->siren;
        $proUser->denomination = $this->denomination;
        $proUser->code = Utils::generateUUID();
        $proUser->phone = $this->phone;
        $proUser->save();

        $proPublic = new PublicProfessional();
        $proPublic->pro_id = $account->id;
        $proPublic->save();

        Application::$app->login($userAccount);
        //        longitude et lattitude à rajouter
    }

    public function labels(): array
    {
        return [
            'siren' => 'SIREN',
            'denomination' => 'Dénomination',
            'mail' => 'E-mail',
            'streetnumber' => 'Numéro de rue',
            'streetname' => 'Nom de rue',
            'postaleCode' => 'Code postal',
            'city' => 'Ville',
            'country' => 'Pays',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'passwordConfirm' => 'Confirmez votre mot de passe',
            'payement' => 'Je choisis de rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)',
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
            'postaleCode' => '22000',
            'city' => 'Lannion',
            'country' => 'France',
            'phone' => '0601020304',
            'password' => '********',
            'passwordConfirm' => '********'
        ];
    }
}