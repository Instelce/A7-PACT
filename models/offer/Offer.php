<?php

namespace app\models\offer;

use app\core\DBModel;
use app\models\Address;
use app\models\offer\schedule\LinkSchedule;
use app\models\offer\schedule\OfferSchedule;
use app\models\opinion\Opinion;
use app\models\payment\Invoice;
use app\models\user\professional\ProfessionalUser;

class Offer extends DBModel
{
    public const STATUS_ONLINE = 0;
    public const STATUS_OFFLINE = 1;

    public int $id = 0;
    public string $title = '';
    public string $summary = '';
    public string $description = '';
    public int $offline = self::STATUS_OFFLINE;
    public int $view_counter = 0;
    public int $click_counter = 0;
    public string $website = '';
    public string $phone_number = '';
    public ?float $minimum_price = null;
    public float $rating = 0;
    public int $token_number = 3;
    public int $time_new_token = 0;

    /**
     * @var 'activity' | 'attraction_park' | 'restaurant' | 'show' | 'visit'
     */
    public string $category = '';

    public int $offer_type_id = 0;
    public int $professional_id = 0;
    public int $address_id = 0;

    public string $created_at = '';
    public string $updated_at = '';

    public function save(): bool
    {
        parent::save();

        // Add history line
        $history = new OfferStatusHistory();
        $history->offer_id = $this->id;
        if ($this->offline) {
            $history->switch_to = "offline";
        } else {
            $history->switch_to = "online";
        }
        $history->created_at = date("Y-m-d", strtotime($this->created_at . "-1 days"));
        $history->save();

        return true;
    }

    public static function tableName(): string
    {
        return 'offer';
    }

    public function attributes(): array
    {
        return ['title', 'summary', 'description', 'offline', 'view_counter', 'click_counter', 'website', 'phone_number', 'category', 'offer_type_id', 'professional_id', 'address_id', 'minimum_price', 'rating','token_number', 'time_new_token'];
    }

    public function updateAttributes(): array
    {
        return ['title', 'summary', 'description', 'offline', 'view_counter', 'click_counter', 'website', 'category', 'phone_number', 'address_id', 'minimum_price', 'rating','token_number', 'time_new_token'];
    }

    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 60]],
            'summary' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]],
            'description' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 1024]],
            'website' => [],
            'phone_number' => [],
            'minimum_price' => []
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

    public static function frenchCategoryName(string $category): string
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

    public function subscription(): false|null|Subscription
    {
        return Subscription::findOne(['offer_id' => $this->id]);
    }

    public function getSubscriptions():false|null|array
    {
        return Subscription::find(['offer_id' => $this->id]);
    }

    public function monthSubscriptions(): array
    {
        return Subscription::query()->filters(['offer_id' => $this->id])->search(['launch_date' => date('Y-m')])->make();
    }

    public function address(): Address
    {
        return Address::findOneByPk($this->address_id);
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

    public function schedule(): array
    {
        $schedules = [];
        $associations = LinkSchedule::find(['offer_id' => $this->id]);
        foreach ($associations as $association) {
            $schedules[] = OfferSchedule::findOneByPk($association->schedule_id);
        }
        return $schedules;
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

    public function addPhoto(string $url)
    {
        $photo = new OfferPhoto();
        $photo->offer_id = $this->id;
        $photo->url_photo = $url;
        $photo->save();
    }

    public function removePhoto(int $photoId)
    {
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

    public function hasTag(string $tagName): bool
    {
        $tag = OfferTag::findOne(['name' => strtolower($tagName)]);
        $isTagged = OfferIsTagged::findOne(['tag_id' => $tag->id, 'offer_id' => $this->id]);
        return $isTagged !== false;
    }

    public function addSubscription(string $type, string $launchDate, int $duration): void
    {
        $option = Option::findOne(['type' => $type]);

        $subscription = new Subscription();
        $subscription->offer_id = $this->id;
        $subscription->option_id = $option->id;
        $subscription->launch_date = $launchDate;
        $subscription->duration = $duration;
        $subscription->save();
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

    public function noReadOpinions(): int
    {
        return count(Opinion::find(['offer_id' => $this->id, 'read' => 0]));
    }


    public function isALaUne()
    {
        return count(Subscription::query()
            ->join(new Option())
            ->filters(['offer_id' => $this->id, 'option__type' => Subscription::A_LA_UNE])->make()) > 0;
    }

    public function rating(): float
    {
        $opinions = Opinion::find(['offer_id' => $this->id]);
        return count($opinions) > 0 ? round(array_sum(array_map(fn($opinion) => $opinion->rating, $opinions)) / count($opinions) * 2) / 2 : 0;
    }

    public function addInvoice(): void
    {
        $invoice = new Invoice();
        $invoice->offer_id = $this->id;
        $invoice->service_date = date("m");
        $invoice->issue_date = date("Y-m-d");
        $invoice->due_date = date("Y-m-d", strtotime("+30 days"));
        $invoice->save();
    }

    /**
     * Count activate days in order of the history for the current month
     */
    public function activeDays(): int
    {
        $lastMonthHistories = OfferStatusHistory::query()->filters(['offer_id' => $this->id])->search(['created_at' => date('Y-m', strtotime("-1 month"))])->make();
        $histories = OfferStatusHistory::query()->filters(['offer_id' => $this->id])->search(['created_at' => date('Y-m')])->make();
        $lastMonthDay = date('t', strtotime(date('Y-m')));
        $count = 0;

        // Set status
        if (empty($lastMonthHistories)) {
            $status = $this->offline ? "offline" : "online";
        } else {
            $status = $lastMonthHistories[count($lastMonthHistories) - 1]->switch_to;
        }

        //        echo $status;

        //        echo "<pre>";
        for ($day = 1; $day <= $lastMonthDay; $day++) {
            // Check if the status has change on this day
            $dayHistories = array_filter($histories, fn($history) => date('d', strtotime($history->created_at)) == $day);
            $dayHistories = array_values($dayHistories);

            if (!empty($dayHistories)) {
                $lastDayHistory = $dayHistories[count($dayHistories) - 1];
                $status = $lastDayHistory->switch_to;
            }

            //            echo $status . "($day)" . PHP_EOL;

            if ($status === "online") {
                $count++;
            }
        }
        //                echo "</pre>";


        return $count;
    }

    public function activeDaysToNow(): int
    {
        $lastMonthHistories = OfferStatusHistory::query()->filters(['offer_id' => $this->id])->search(['created_at' => date('Y-m', strtotime("-1 month"))])->order_by(['created_at'])->make();
        $histories = OfferStatusHistory::query()->filters(['offer_id' => $this->id])->search(['created_at' => date('Y-m')])->order_by(['created_at'])->make();
        $currentDay = date('d');
        $count = 0;
        //
//        echo "<pre>";
//        var_dump($lastMonthHistories);
//        echo "</pre>";

        // Set status
        if (empty($lastMonthHistories)) {
            $status = $this->offline ? "offline" : "online";
        } else {
            $status = $lastMonthHistories[count($lastMonthHistories) - 1]->switch_to;
        }

        //        echo $currentDay;

        //        echo "<pre>";
        for ($day = 1; $day <= $currentDay; $day++) {
            // Check if the status has change on this day
            $dayHistories = array_filter($histories, fn($history) => date('d', strtotime($history->created_at)) == $day);
            $dayHistories = array_values($dayHistories);

            if (!empty($dayHistories)) {
                $lastDayHistory = $dayHistories[count($dayHistories) - 1];
                $status = $lastDayHistory->switch_to;
            }

            //            echo $status . "($day)" . PHP_EOL;

            if ($status === "online") {
                $count++;
            }
        }
        //        echo "</pre>";

        return $count;
    }
}