<?php

namespace app\models\opinion;

use app\core\Application;
use app\core\DBModel;
use app\models\offer\OfferPhoto;

class Opinion extends DBModel
{
    public int $id = 0;
    public float $rating = 0;
    public string $title = "";
    public string $comment = "";
    public string $visit_date = "";
    public string $visit_context = "";

    public bool $read = false;
    public bool $blacklisted = false;

    public int $account_id;
    public int $offer_id;

    public string $created_at = "";
    public string $updated_at = "";

    public int $nb_reports = 0;
    public static function tableName(): string
    {
        return 'opinion';
    }

    public function attributes(): array
    {
        return ['rating', 'title', 'comment', 'visit_date', 'visit_context', 'read', 'blacklisted', 'account_id', 'offer_id', 'nb_reports'];
    }

    public function rules(): array
    {
        return [
            'rating' => [self::RULE_REQUIRED],
            'title' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]],
            'comment' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 1024]],
            'visit_date' => [self::RULE_REQUIRED],
            'visit_context' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'rating' => 'Quelle note donneriez-vous à votre expérience ?',
            'title' => 'Donnez un titre à votre avis',
            'comment' => 'Ajouter votre commentaire',
            'visit_date' => 'Quand y êtes-vous allé ?',
            'visit_context' => 'Qui vous accompagnait ?',
        ];
    }

    public function addPhoto(string $photo_url)
    {
        $photo = new OpinionPhoto();
        $photo->photo_url = $photo_url;
        $photo->opinion_id = $this->id;
        $photo->save();
    }

    public function addLike()
    {
        $like = new OpinionLike();
        $like->opinion_id = $this->id;
        $like->account_id = Application::$app->user->account_id;
        $like->save();
    }

    public function removeLike()
    {
        $like = OpinionLike::findOne(["opinion_id" => $this->id, "account_id" => Application::$app->user->account_id]);
        $like->destroy();
    }

    public function addDislike()
    {
        $dislike = new OpinionDislike();
        $dislike->opinion_id = $this->id;
        $dislike->account_id = Application::$app->user->account_id;
        $dislike->save();
    }

    public function removeDislike()
    {
        $dislike = OpinionDislike::findOne(["opinion_id" => $this->id, "account_id" => Application::$app->user->account_id]);
        $dislike->destroy();
    }

    public function likes(): int
    {
        return count(OpinionLike::find(["opinion_id" => $this->id]));
    }

    public function dislikes(): int
    {
        return count(OpinionDislike::find(["opinion_id" => $this->id]));
    }


    public function addReport()
    {
        $this->nb_reports += 1;
        $this->update();
    }

    public function photos()
    {
        return OpinionPhoto::find(['opinion_id' => $this->id]);
    }
}