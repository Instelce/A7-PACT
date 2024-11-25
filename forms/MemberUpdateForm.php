<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\Account;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\user\MemberUser;

class MemberUpdateForm extends Model
{
    public string $lastname = '';
    public string $firstname = '';
    public string $pseudo = '';
    public string $mail = '';
    public string $streetNumber = '';
    public string $streetName = '';
    public string $postalCode = '';
    public string $city = '';
    public string $phone = '';
    public bool $notification = false;

    public function __construct()
    {
        $userAccount = Application::$app->user;
        $this->mail = $userAccount->mail;

        $address = Address::findOneByPk($userAccount->address_id);
        $this->number = $address->number;
        $this->street = $address->street;
        $this->city = $address->city;
        $this->postal_code = $address->postal_code;

        $memberUser = MemberUser::findOneByPk(Application::$app->user->account_id);
        $this->lastname = $memberUser->lastname;
        $this->firstname = $memberUser->firstname;
        $this->phone = $memberUser->phone;
        $this->pseudo = $memberUser->pseudo;
        $this->notification = $memberUser->allows_notifications;
    }

    public function update()
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
        $user->avatar_url = "https://ui-avatars.com/api/?size=128&name=$this->firstname+$this->lastname";
        $user->address_id = $address->id;
        $user->save();

        $member = new MemberUser();
        $member->user_id = $account->id;
        $member->lastname = $this->lastname;
        $member->firstname = $this->firstname;
        $member->phone = $this->phone;
        $member->pseudo = $this->pseudo;
        $member->allows_notifications = $this->notification;
        $member->save();

        Application::$app->login($user);

        return true;
    }

    public function rules(): array
    {
        return [
            'lastname' => [self::RULE_REQUIRED],
            'firstname' => [self::RULE_REQUIRED],
            'pseudo' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'attributes' => 'pseudo', 'class' => MemberUser::class]],
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL, [self::RULE_UNIQUE, 'attributes' => 'mail', 'class' => UserAccount::class]],
            'streetNumber' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'streetName' => [self::RULE_REQUIRED],
            'postalCode' => [self::RULE_REQUIRED, self::RULE_POSTAL],
            'city' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED,self::RULE_PHONE, [self::RULE_UNIQUE, 'attributes' => 'phone', 'class' => MemberUser::class]],
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
            'streetName' => 'Nom de rue',
            'postalCode' => 'Code postal',
            'city' => 'Ville',
            'phone' => 'Téléphone',
            'notifications' => 'notifications',
        ];
    }

    public function placeholders(): array
    {
        return [
            'lastname' => 'Martin',
            'firstname' => 'Gabriel',
            'pseudo' => 'GabMart',
            'mail' => 'example@email.com',
            'streetNumber' => '12',
            'streetName' => 'Edouard Branly',
            'postalCode' => '22300',
            'city' => 'Lannion',
            'phone' => '01 23 45 67 89',
            'notifications' => '*******',
        ];
    }
}

