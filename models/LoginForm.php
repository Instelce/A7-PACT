<?php

namespace app\models;

use app\core\Application;
use app\core\Model;

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
        $user = User::findOne(['email' => $this->email]);

        if (!$user) {
            $this->addError('email', 'Utilisateur inexistant avec cette adresse email.');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Mot-de-passe incorrect.');
            return false;
        }

        return Application::$app->login($user);
    }

    public function labels(): array
    {
        return [
            'email' => 'E-mail',
            'password' => 'Mot de passe',
        ];
    }
}