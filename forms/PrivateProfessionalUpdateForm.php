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
    public string $titularAccount = '';
    public string $iban = '';
    public string $bic = '';
    public string $titularCard = '';
    public string $cardnumber = '';
    public string $expirationdate = '';
    public string $cryptogram = '';
    public string $paypallink = '';
    public string $passwordCheck = '';
    public ?UserAccount $userAccount = null;
    public ?Address $address = null;
    public ?ProfessionalUser $professional = null;
    public PrivateProfessional|null|false $privateProfessional = null;
    public ?MeanOfPayment $payment = null;
    public ?CbMeanOfPayment $cb = null;
    public ?RibMeanOfPayment $rib = null;
    public ?PaypalMeanOfPayment $paypal = null;

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

        $this->payment = MeanOfPayment::findOneByPk($this->privateProfessional->payment_id);

        if(!$this->payment){

        }

        else if (MeanOfPayment::findOneByPk($this->payment->id)->isRibPayment()) {
            $this->rib = RibMeanOfPayment::findOneByPk($this->payment->id);
            $this->titularAccount = $this->rib->name;
            $this->iban = $this->rib->iban;
            $this->bic = $this->rib->bic;
        }
        else if (MeanOfPayment::findOneByPk($this->payment->id)->isCbPayment()) {
            $this->cb = CBMeanOfPayment::findOneByPk($this->payment->id);
            $this->titularCard = $this->cb->name;
            $this->cardnumber = $this->cb->card_number;
            $this->expirationdate = $this->cb->expiration_date;
            $this->cryptogram = $this->cb->cvv;
        }
        else{
            $this->paypal = PaypalMeanOfPayment::findOneByPk($this->payment->id);
            $this->paypallink = $this->paypal->paypal_url;
        }
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

    public function paymentLoad(){
        $request = Application::$app->request;
        $this->payment->loadData($request->getBody());
        $this->payment->update();
        if (MeanOfPayment::findOneByPk($this->payment->id)->isRibPayment()){
            $this->rib->loadData($request->getBody());
            $this->rib->update();
        }
        else if (MeanOfPayment::findOneByPk($this->payment->id)->isCbPayment()){
            $this->cb->loadData($request->getBody());
            $this->cb->update();
        }
        else if (MeanOfPayment::findOneByPk($this->payment->id)->isPaypalPayment()){
            $this->paypal->loadData($request->getBody());
            $this->paypal->update();
        }
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
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 10]],
            'titular-account' => [[self::RULE_MAX, 'max' => 255]],
            'iban' => [[self::RULE_MAX, 'max' => 34]],
            'bic' => [[self::RULE_MAX, 'max' => 11]],
            'cardnumber' => [[self::RULE_MAX, 'max' => 16]],
            'titular-card' => [[self::RULE_MAX, 'max' => 255]],
            'expirationdate' => [self::RULE_EXP_DATE],
            'cryptogram' => [[self::RULE_MAX, 'max' => 3]],
            'passwordCheck' => [self::RULE_REQUIRED]
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
            'titular-account' => 'Titulaire du compte',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'titular-card' => 'Titulaire de la carte',
            'cardnumber' => 'Numéro de carte',
            'expirationdate' => 'Date d\'expiration',
            'cryptogram' => 'CVV',
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
            'titular-account' => 'Nom entreprise / Nom personne',
            'iban' => 'FR76 XXXX XXXX XXXX XXXX XXXX XXXX XX',
            'bic' => 'vore BIC (optionnel)',
            'titular-card' => 'Nom entreprise / Nom personne',
            'cardnumber' => 'XXXX XXXX XXXX XXXX',
            'expirationdate' => 'MM/AA',
            'cryptogram' => '000',
            'passwordCheck' => '********'
        ];
    }
}