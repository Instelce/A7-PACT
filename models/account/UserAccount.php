<?php

namespace app\models\account;

use app\core\DBModel;
use app\models\user\AdministratorUser;
use app\models\user\MemberUser;
use app\models\user\professional\PrivateProfessional;
use app\models\user\professional\ProfessionalUser;
use app\models\user\professional\PublicProfessional;

class UserAccount extends DBModel
{
    public int $account_id = 0;
    public string $mail = '';
    public string $password = '';
    public string $avatar_url = '';
    public int $address_id = 0;

    public static function tableName(): string
    {
        return 'user_account';
    }

    public function attributes(): array
    {
        return ['account_id', 'mail', 'password', 'avatar_url', 'address_id'];
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
            'avatar_url' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'address_id' => [self::RULE_REQUIRED]
        ];
    }

    public function account(): Account
    {
        return Account::findOne(['id' => $this->account_id]);
    }

    public function isMember(): bool
    {
        return MemberUser::findOneByPk($this->account_id) !== false;
    }

    public function isProfessional(): bool
    {
        return ProfessionalUser::findOneByPk($this->account_id) !== false;
    }

    public function isPrivateProfessional(): bool
    {
        return $this->isProfessional() && PrivateProfessional::findOneByPk($this->account_id) !== false;
    }

    public function isPublicProfessional(): bool
    {
        return $this->isProfessional() && PublicProfessional::findOneByPk($this->account_id) !== false;
    }

    public function isAdministrator(): bool
    {
        return AdministratorUser::findOneByPk($this->account_id) !== false;
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

    public function specific(): ?DBModel
    {
        if ($this->isMember()) {
            return MemberUser::findOneByPk($this->account_id);
        } else if ($this->isProfessional()) {
            return ProfessionalUser::findOneByPk($this->account_id);
        } else if ($this->isAdministrator()) {
            return AdministratorUser::findOneByPk($this->account_id);
        }

        return null;
    }
}

