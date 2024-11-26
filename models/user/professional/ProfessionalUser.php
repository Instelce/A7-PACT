<?php
namespace app\models\user\professional;
use app\core\DBModel;
use app\models\account\UserAccount;
use app\models\offer\Offer;

class ProfessionalUser extends DBModel
{
    private const ACCEPT_CONDITIONS = 1;
    private const REFUSED_CONDITIONS = 0;
    private const ACCEPT_NOTIFICATIONS = 1;
    private const REFUSED_NOTIFICATIONS = 0;

    public int $user_id = 0;
    public string $code = '';
    public string $denomination = '';
    public string $siren = '';
    public string $phone = '';

    public int $conditions = self::REFUSED_CONDITIONS;
    public int $notification = self::REFUSED_NOTIFICATIONS;

    public static function tableName(): string
    {
        return 'professional_user';
    }

    public function attributes(): array
    {
        return ['code', 'denomination', 'siren', 'conditions', 'notification', 'phone'];
    }

    public static function pk(): string
    {
        return 'user_id';
    }

    public function rules(): array
    {
        return [
            'code' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 16], self::RULE_UNIQUE],
            'denomination' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100], self::RULE_UNIQUE],
            'siren' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 14], self::RULE_UNIQUE],
            'conditions' => [self::RULE_REQUIRED],
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max'=> 10], self::RULE_UNIQUE],
        ];
    }

    public function user(): UserAccount {
        return UserAccount::findOne(['account_id' => $this->user_id]);
    }

    public function hasOffer($offerId): bool {
        return Offer::findOne(['professional_id' => $this->user_id, 'id' => $offerId]) !== false;
    }
}