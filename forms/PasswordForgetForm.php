<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\UserAccount;
use app\core\Utils;



class PasswordForgetForm extends Model
{
    public string $mail = '';

    public function rules(): array
    {
        return [
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL],
        ];
    }

    public function verify()
    {
        /**
         * @var UserAccount $user
         */
        $user = UserAccount::findOne(['mail' => $this->mail]);

        if (!$user) {
            $this->addError('mail', 'E-mail n\'existe pas');
        } else {
            $user->reset_password_hash = Utils::generateHash();
            $user->update();
            Application::$app->mailer->send($this->mail, "Modification du mot de passe de $this->mail", 'reset-password', ['mail' => $this->mail, 'hash' => $user->reset_password_hash]);
        }
    }


    public function labels(): array
    {
        return [
            'mail' => 'E-mail'
        ];
    }

    public function placeholders(): array
    {
        return [
            'mail' => 'example@email.com'
        ];
    }
}