<?php

namespace app\models\account;

use app\core\DBModel;
use app\models\user\AdministratorUser;
use app\models\user\MemberUser;
use app\models\user\professional\ProfessionalUser;

class UserAccount extends DBModel
{
    public int $account_id = 0;
    public string $mail = '';
    public string $password = '';
    public string $avatarUrl = '';

    public static function tableName(): string
    {
        return 'user_account';
    }

    public function attributes(): array
    {
        return ['account_id', 'mail', 'password', 'avatarUrl'];
    }

    public static function pk(): string
    {
        return 'account_id';
    }

    public function rules(): array
    {
        return [
            'mail' => [self::RULE_REQUIRED, [self::RULE_MAIL], [self::RULE_MAX, 'max' => 100], [self::RULE_UNIQUE]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 100]],
            'avatarUrl' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]]
        ];
    }

    public function account(): Account
    {
        return Account::findOne(['id' => $this->account_id]);
    }

    public function isMember(): bool
    {
        return MemberUser::findOne(['account_id' => $this->account_id]) !== null;
    }

    public function isProfessional(): bool
    {
        return ProfessionalUser::findOne(['account_id' => $this->account_id]) !== null;
    }

    public function isAdministrator(): bool
    {
        return AdministratorUser::findOne(['account_id' => $this->account_id]) !== null;
    }

    /**
     * Get the type of the user
     * @return 'member'|'professional'|'admin'
     */
    public function getType(): string
    {
        if ($this->isMember()) {
            return 'member';
        } else if ($this->isProfessional()) {
            return 'professional';
        } else if ($this->isAdministrator()) {
            return 'admin';
        }

        return '';
    }
}

