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

class PublicProfessionalUpdateForm extends Model
{
    public const ACCEPT_NOTIFICATIONS = 1;
    public const REFUSE_NOTIFICATIONS = 0;
    public string $siren = '';
    public string $denomination = '';

    public string $mail = '';
    public int $streetnumber = 0;
    public string $streetname = '';
    public string $postaleCode = '';
    public string $city = '';
    public string $phone = '';

    public string $passwordCheck = '';

    public int $notifications = self::REFUSE_NOTIFICATIONS;

    public UserAccount $userAccount;
    public Address $address;
    public ProfessionalUser $professionalUser;
    public PublicProfessional $publicProfessional;

    public function __construct()
    {
        $this->userAccount = Application::$app->user;
        $this->mail = $this->userAccount->mail;

        $this->address = Address::findOneByPk($this->userAccount->address_id);
        $this->streetnumber = $this->address->number;
        $this->streetname = $this->address->street;
        $this->city = $this->address->city;
        $this->postaleCode = $this->address->postal_code;

        $this->professionalUser = ProfessionalUser::findOneByPk(Application::$app->user->account_id);
        if($this->professionalUser){
            $this->denomination = $this->professionalUser->denomination;
            if($this->professionalUser->siren){
                $this->siren = $this->professionalUser->siren;
            } else{
                $this->siren = '';
            }
            $this->phone = $this->professionalUser->phone;
            $this->notifications = $this->professionalUser->allows_notifications;
        }
        $this->publicProfessional = PublicProfessional::findOneByPk(Application::$app->user->account_id);
    }

    public function update(){
        $request = Application::$app->request;
        $this->userAccount->loadData($request->getBody());
        $this->userAccount->update();
        $this->address->loadData($request->getBody());
        $this->address->update();
        $this->professionalUser->loadData($request->getBody());
        $this->professionalUser->update();
        $this->publicProfessional->loadData($request->getBody());
        $this->publicProfessional->update();
        return true;
    }

    public function passwordMatch()
    {
        /**
         * @var UserAccount $user
         */
        $user = Application::$app->user;

        if (!password_verify($this->passwordCheck, $user->password)) {
            $this->addError('passwordCheck', 'Mot-de-passe incorrect.');
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'siren' => [[self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL],
            'streetname' => [self::RULE_REQUIRED],
            'streetnumber' => [self::RULE_REQUIRED],
            'postaleCode' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 10]],
            'passwordCheck' => [self::RULE_REQUIRED, self::RULE_PASSWORD]
        ];
    }


    public function labels(): array
    {
        return [
            'asso' => 'Statut associatif',
            'siren' => 'Siren',
            'denomination' => 'Dénomination',
            'mail' => 'E-mail',
            'streetnumber' => 'Numéro de rue',
            'streetname' => 'Nom de rue',
            'postaleCode' => 'Code postal',
            'city' => 'Ville',
            'phone' => 'Téléphone',
            'passwordCheck' => 'Mot de passe'
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
            'passwordCheck' => '********',
        ];
    }
}