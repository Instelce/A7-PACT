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
    public string $password = '';
    public bool $notification = false;
    public ?UserAccount $userAccount = null;
    public ?Address $address = null;
    public MemberUser|null|false $memberUser = null;

    /**
     * Initialize attributes this form
     */
    public function __construct()
    {
        $this->userAccount = Application::$app->user;
        $this->mail = $this->userAccount->mail;

        $this->address = Address::findOneByPk($this->userAccount->address_id);
        $this->streetNumber = $this->address->number;
        $this->streetName = $this->address->street;
        $this->city = $this->address->city;
        $this->postalCode = $this->address->postal_code;

        $this->memberUser = MemberUser::findOneByPk(Application::$app->user->account_id);

        if ($this->memberUser) {
            $this->lastname = $this->memberUser->lastname;
            $this->firstname = $this->memberUser->firstname;
            $this->phone = $this->memberUser->phone;
            $this->pseudo = $this->memberUser->pseudo;
            $this->notification = $this->memberUser->allows_notifications;
        }

    }

    public function update()
    {
        $request = Application::$app->request;
        $this->userAccount->loadData($request->getBody());
        $this->userAccount->update();
        $this->address->loadData($request->getBody());
        $this->address->update();
        $this->memberUser->loadData($request->getBody());
        $this->memberUser->update();
        return true;
    }

    public function rules(): array
    {
        return [
            'lastname' => [self::RULE_REQUIRED],
            'firstname' => [self::RULE_REQUIRED],
            'pseudo' => [self::RULE_REQUIRED],
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL],
            'streetNumber' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'streetName' => [self::RULE_REQUIRED],
            'postalCode' => [self::RULE_REQUIRED, self::RULE_POSTAL],
            'city' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED,self::RULE_PHONE],
            'password' => [self::RULE_PASSWORD_CHECK],
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
            'password' => 'Entrez votre mot de passe',
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

