<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\UserAccount;
use app\models\User;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_MAIL],
            'password' => [self::RULE_REQUIRED]
        ];
    }

    public function login()
    {
        $user = UserAccount::findOne(['email' => $this->email]);

        if (!$user) {
            $this->addError('email', 'Aucun compte n\'a été trouvé avec cet e-mail.');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Mot-de-passe incorrect.');
            return false;
        }

        return Application::$app->login($user, );
    }

    public function labels(): array
    {
        return [
            'email' => 'E-mail',
            'password' => 'Mot de passe',
        ];
    }
}