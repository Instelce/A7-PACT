<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\Account;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\user\MemberUser;

class MemberRegisterForm extends Model
{
    public string $lastname = '';
    public string $firstname = '';
    public string $pseudo = '';
    public string $mail = '';
    public string $streetNumber = '';
    public string $streetName = '';
    public string $postalCode = '';
    public string $city = '';
    public string $country = '';
    public string $phone = '';
    public string $password = '';
    public string $passwordConfirm = '';

    public function register()
    {
        $account = new Account();
        $account->save();

        $address = new Address();
        $address->number = $this->streetNumber;
        $address->street = $this->streetName;
        $address->city = $this->city;
        $address->postal_code = $this->postalCode;
        $address->longitude = 0;
        $address->latitude = 0;
        $address->save();

        $user = new UserAccount();
        $user->account_id = $account->id;
        $user->mail = $this->mail;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        $user->avatar_url = "/assets/images/avatar.png";
        $user->address_id = $address->id;
        $user->save();

        $member = new MemberUser();
        $member->user_id = $account->id;
        $member->lastname = $this->lastname;
        $member->firstname = $this->firstname;
        $member->phone = $this->phone;
        $member->pseudo = $this->pseudo;
        $member->allows_notifications = false;
        $member->save();

        Application::$app->login($user);
    }
    public function rules(): array
    {
        return [
            'lastname' => [self::RULE_REQUIRED],
            'firstname' => [self::RULE_REQUIRED],
            'pseudo' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attributes' => 'pseudo', 'class' => MemberUser::class]],
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL, [self::RULE_UNIQUE, 'attributes' => 'mail', 'class' => UserAccount::class]],
            'streetNumber' => [self::RULE_REQUIRED],
            'streetName' => [self::RULE_REQUIRED],
            'postalCode' => [self::RULE_REQUIRED],
            'city' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED],
            'password' => [self::RULE_REQUIRED],
            'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
        ];
    }

    public function labels(): array
    {
        return [
            'lastname' => 'Nom',
            'firstname' => 'Prénom',
            'pseudo' => 'Pseudo',
            'mail' => 'Email',
            'streetNumber' => 'Numéro de rue',
            'streetName' => 'Nom du rue',
            'postalCode' => 'Code postal',
            'city' => 'Ville',
            'country' => 'Pays',
            'phone' => 'Téléphone',
            'password' => 'Mot de passe',
            'passwordConfirm' => 'Confirmation mot de passe',
            'notifications' => 'notifications',
        ];
    }

    public function placeholders(): array
    {
        return [
            'mail' => 'example@email.com',
        ];
    }
}

