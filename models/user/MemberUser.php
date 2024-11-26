<?php

namespace app\models\user;

use app\core\DBModel;
use app\models\account\UserAccount;
use app\models\opinion\Opinion;

class MemberUser extends DBModel
{
    public const NO_NOTIFICATIONS = 0;
    public const ALLOW_NOTIFICATIONS = 1;

    public int $user_id = 0;
    public string $lastname = '';
    public string $firstname = '';
    public string $phone = '';
    public string $pseudo = '';
    public int $allows_notifications = self::NO_NOTIFICATIONS;

    public static function tableName(): string
    {
        return 'member_user';
    }

    public function attributes(): array
    {
        return ['user_id', 'lastname', 'firstname', 'phone', 'pseudo', 'allows_notifications'];
    }

    public static function pk(): string
    {
        return 'user_id';
    }

    public function rules(): array
    {
        return [
            'lastname' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'firstname' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50], self::RULE_UNIQUE],
            'pseudo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50], self::RULE_UNIQUE],
            'allows_notifications' => [self::RULE_REQUIRED]
        ];
    }

    public function user(): UserAccount
    {
        return UserAccount::findOne(['account_id' => $this->user_id]);
    }

    /**
     * @return Opinion[]
     */
    public function opinions(): array
    {
        return Opinion::find(['account_id' => $this->user_id]);
    }

    public function opinionsCount(): int
    {
        return count(Opinion::find(['account_id' => $this->user_id]));
    }

}