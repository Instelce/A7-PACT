<?php

namespace app\forms;

use app\core\Application;
use app\core\Model;
use app\models\account\AnonymousAccount;
use app\models\account\UserAccount;
use app\models\User;
use app\models\user\MemberUser;
use app\models\Address;

class DeleteForm extends Model
{
    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => [self::RULE_REQUIRED]
        ];
    }
    public function delete(): bool
    {
        /**
         * @var UserAccount $user
         */
        $user = Application::$app->user;

        if (!password_verify($this->password, $user->password) || (!$user)) {
            $this->addError('password', 'Mot-de-passe incorrect.');
            return false;
        } else {
            //1 create an anonymous account
            $anonymous = new AnonymousAccount();
            $anonymous->account_id = $user->account_id;
            $anonymous->pseudo = "anonymous" . strval($user->account_id);
            $anonymous->save();

            //2 log out the user
            Application::$app->logout();

            //3 delete the user account
            $member = MemberUser::findOne(['user_id' => $user->account_id])->destroy();
            $address = Address::findOne(where: ['id' => $user->address_id])->destroy();
            $user->destroy();

            return true;
        }
    }

    public function labels(): array
    {
        return [
            'password' => 'Mot de passe',
        ];
    }

    public function placeholders(): array
    {
        return [
            'password' => 'mot de passe',
        ];
    }
}