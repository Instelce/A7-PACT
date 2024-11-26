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

class PublicProfessionalRegister extends Model
{
    public const ASSO = 1;
    public const OTHER = 0;
    public int $isAsso = self::OTHER;
    public string $siren = '';
    public string $denomination = '';

    public string $mail = '';
    public int $streetnumber = 0;
    public string $streetname = '';
    public string $postaleCode = '';
    public string $city = '';
    public string $phone = '';
    public string $password = '';
    public string $passwordConfirm = '';


    public function register()
    {
        echo "<pre>";
        var_dump("enter register");
        echo "</pre>";
        exit;
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
        
        echo "<pre>";
        var_dump(Utils::generateUUID());
        echo "</pre>";
        exit;

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
        return true;
    }


    public function rules(): array
    {
        return [
            'siren' => [[self::RULE_UNIQUE, 'attribute' => 'siren', 'class' => ProfessionalUser::class], [self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attribute' => 'mail', 'class' => UserAccount::class], self::RULE_MAIL],
            'streetname' => [self::RULE_REQUIRED],
            'streetnumber' => [self::RULE_REQUIRED],
            'postaleCode' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 10], [self::RULE_UNIQUE, 'attribute' => 'phone', 'class' => ProfessionalUser::class]],
            'password' => [self::RULE_REQUIRED, self::RULE_PASSWORD],
            'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
        ];
    }


    public function labels(): array
    {
        return [
            'asso' => 'Statut associatif',
            'siren' => 'SIREN',
            'denomination' => 'Dénomination',
            'mail' => 'E-mail',
            'streetnumber' => 'Numéro de rue',
            'streetname' => 'Nom de rue',
            'postaleCode' => 'Code postal',
            'city' => 'Ville',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'passwordConfirm' => 'Confirmez votre mot de passe'
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
            'phone' => '0601020304',
            'password' => '********',
            'passwordConfirm' => '********'
        ];
    }
}