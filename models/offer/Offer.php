<?php

namespace app\models\offer;

use app\core\DBModel;
use app\models\Address;
use app\models\opinion\Opinion;
use app\models\user\professional\ProfessionalUser;

class Offer extends DBModel
{
    public const STATUS_ONLINE = 0;
    public const STATUS_OFFLINE = 1;

    public int $id = 0;
    public string $title = '';
    public string $summary = '';
    public string $description = '';
    public int $likes = 0;
    public int $offline = self::STATUS_OFFLINE;
    public ?string $offline_date = '';
    public ?string $last_online_date = '';
    public int $view_counter = 0;
    public int $click_counter = 0;
    public string $website = '';
    public string $phone_number = '';
    public ?float $minimum_price = null;

    /**
     * @var 'activity' | 'attraction_park' | 'restaurant' | 'show' | 'visit'
     */
    public string $category = '';

    public int $offer_type_id = 0;
    public int $professional_id = 0;
    public int $address_id = 0;

    public string $created_at = '';
    public string $updated_at = '';

    public static function tableName(): string
    {
        return 'offer';
    }

    public function attributes(): array
    {
        return ['title', 'summary', 'description', 'likes', 'offline', 'offline_date', 'last_online_date', 'view_counter', 'click_counter', 'website', 'phone_number', 'category', 'offer_type_id', 'professional_id', 'address_id', 'minimum_price'];
    }

    public function updateAttributes(): array
    {
        return ['title', 'summary', 'description', 'likes', 'offline', 'offline_date', 'last_online_date', 'view_counter', 'click_counter', 'website', 'category', 'phone_number', 'address_id', 'minimum_price'];
    }

    public function rules(): array
    {
        return [
//            'title' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 60]],
//            'summary' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]],
//            'description' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 1024]],
//            'website' => [],
//            'phone_number' => [],
//            'minimum_price' => []
        ];
    }

    public function labels(): array
    {
        return [
            'title' => 'Titre',
            'summary' => 'Résumé',
            'website' => 'Site web',
            'phone_number' => 'Numéro de téléphone',
            'minimum_price' => 'Prix minimum'
        ];
    }

    public static function frenchCategoryName(string $category)
    {
        return match ($category) {
            'activity' => 'Activité',
            'attraction_park' => 'Parc d\'attraction',
            'restaurant' => 'Restaurant',
            'show' => 'Spectacle',
            'visit' => 'Visite',
            default => '',
        };
    }

    public function type(): OfferType
    {
        return OfferType::findOne(['id' => $this->offer_type_id]);
    }

    public function option(): OfferOption
    {
        return OfferOption::findOne(['offer_id' => $this->id]);
    }

    public function address(): Address
    {
        return Address::findOne(['address_id' => $this->id]);
    }

    public function tags(): array
    {
        $tags = [];
        $association = OfferIsTagged::find(['offer_id' => $this->id]);

        foreach ($association as $tagAssoc) {
            $tag = OfferTag::findOne(['id' => $tagAssoc->tag_id]);
            $tags[] = $tag;
        }

        return $tags;
    }

    /**
     * @return OfferPhoto[]
     */
    public function photos(): array
    {
        return OfferPhoto::find(['offer_id' => $this->id]);
    }

    public function professional(): ProfessionalUser
    {
        return ProfessionalUser::findOneByPk($this->professional_id);
    }

    public function specificData()
    {
        return match ($this->category) {
            'activity' => ActivityOffer::findOne(['offer_id' => $this->id]),
            'attraction_park' => AttractionParkOffer::findOne(['offer_id' => $this->id]),
            'restaurant' => RestaurantOffer::findOne(['offer_id' => $this->id]),
            'show' => ShowOffer::findOne(['offer_id' => $this->id]),
            'visit' => VisitOffer::findOne(['offer_id' => $this->id]),
            default => null,
        };
    }

    public function addPhoto(string $url) {
        $photo = new OfferPhoto();
        $photo->offer_id = $this->id;
        $photo->url_photo = $url;
        $photo->save();
    }

    public function removePhoto(int $photoId) {
        $photo = OfferPhoto::findOne(['id' => $photoId, 'offer_id' => $this->id]);
        if ($photo) {
            $photo->delete();
        }
    }

    public function addTag($tagId)
    {
        $isTagged = new OfferIsTagged();
        $isTagged->tag_id = $tagId;
        $isTagged->offer_id = $this->id;
        $isTagged->save();
    }

    public function addOption(string $type, string $launchDate, int $duration) {
        $option = new OfferOption();
        $option->offer_id = $this->id;
        $option->type = $type;
        $option->launch_date = $launchDate;
        $option->duration = $duration;
        $option->save();
    }

    /**
     * @return Opinion[]
     */
    public function opinions(): array
    {
        return Opinion::find(['offer_id' => $this->id]);
    }

    public function opinionsCount(): int
    {
        return count($this->opinions());
    }
}