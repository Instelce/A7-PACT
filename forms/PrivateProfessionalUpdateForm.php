<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\payment\CbMeanOfPayment;
use app\models\payment\MeanOfPayment;
use app\models\payment\PaypalMeanOfPayment;
use app\models\payment\RibMeanOfPayment;
use app\models\user\professional\PrivateProfessional;
use app\models\user\professional\ProfessionalUser;
use app\core\Utils;

class PrivateProfessionalUpdateForm extends Model
{
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

    public int $notifications = self::REFUSE_NOTIFICATIONS;
    public string $passwordCheck = '';
    public ?UserAccount $userAccount = null;
    public ?Address $address = null;
    public ?ProfessionalUser $professional = null;
    public PrivateProfessional|null|false $privateProfessional = null;
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
        $this->denomination = $this->professionalUser->denomination;
        $this->siren = $this->professionalUser->siren;
        $this->phone = $this->professionalUser->phone;
        $this->notifications = $this->professionalUser->allows_notifications;

        $this->privateProfessional = PrivateProfessional::findOneByPk(Application::$app->user->account_id);
    }


    public function update(){
        $request = Application::$app->request;
        $this->userAccount->loadData($request->getBody());
        $this->userAccount->update();
        $this->address->loadData($request->getBody());
        $this->address->update();
        $this->professionalUser->loadData($request->getBody());
        $this->professionalUser->update();
        $this->privateProfessional->loadData($request->getBody());
        $this->privateProfessional->update();
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
            'siren' =>[self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 9]],
            'denomination' => [self::RULE_REQUIRED],
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL],
            'streetnumber' => [self::RULE_REQUIRED],
            'streetname' => [self::RULE_REQUIRED],
            'postaleCode' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 5]],
            'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'phone' => [self::RULE_REQUIRED]
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
            'payment' => 'Je souhaite de rentrer mes coordonnées bancaires maintenant (possibilité de le faire plus tard)',
            'conditions' => 'J\'accepte les conditions géénrales d\'utilisation',
            'notifications' => 'J\'autorise l\'envoi de notifications',
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
            'passwordCheck' => '********'
        ];
    }
}