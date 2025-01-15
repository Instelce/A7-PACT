<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\UserAccount;
use app\models\User;

class LoginForm extends Model
{
    public string $mail = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            'mail' => [self::RULE_REQUIRED, self::RULE_MAIL],
            'password' => [self::RULE_REQUIRED]
        ];
    }

    public function login()
    {
        /**
         * @var UserAccount $user
         */
        $user = UserAccount::findOne(['mail' => $this->mail]);

        if (!password_verify($this->password, $user->password) || (!$user)) {
            $this->addError('password', 'Mot-de-passe ou e-mail incorrect.');
            return false;
        }

        return Application::$app->login($user);
    }

    public function labels(): array
    {
        return [
            'mail' => 'E-mail',
            'password' => 'Mot de passe',
        ];
    }

    public function placeholders(): array
    {
        return [
            'mail' => 'example@email.com',
            'password' => '********',
        ];
    }
}


