<?php
namespace app\models\user\professional;
use app\core\DBModel;
use app\models\account\UserAccount;
use app\models\offer\Offer;
use app\models\opinion\Opinion;

class ProfessionalUser extends DBModel
{

    public int $user_id = 0;
    public string $code = '';
    public string $denomination = '';
    public string $siren = '';
    public string $phone = '';

    public static function tableName(): string
    {
        return 'professional_user';
    }

    public function attributes(): array
    {
        return ['user_id', 'code', 'denomination', 'siren', 'phone'];
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
            'phone' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max'=> 10], self::RULE_UNIQUE],
        ];
    }

    public function user(): UserAccount {
        return UserAccount::findOne(['account_id' => $this->user_id]);
    }

    public function isPublic(): bool
    {
        return PublicProfessional::findOneByPk($this->user_id) !== false;
    }

    public function isPrivate(): bool
    {
        return PrivateProfessional::findOneByPk($this->user_id) !== false;
    }

    public function specific()
    {
        if ($this->isPublic()) {
            return PublicProfessional::findOneByPk($this->user_id);
        } else {
            return PrivateProfessional::findOneByPk($this->user_id);
        }
    }

    public function hasOffer($offerId): bool {
        return Offer::findOne(['professional_id' => $this->user_id, 'id' => $offerId]) !== false;
    }

    public function opinionsReceiveCount(): int
    {
        return count(Opinion::query()->join(new Offer())->filter('offer__professional_id', $this->user_id)->make());
    }

    public function offerLikes(): int
    {
        return array_sum(array_map(fn($offer) => $offer->likes, Offer::find(['professional_id' => $this->user_id])));
    }
}