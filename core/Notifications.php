<?php

namespace app\core;

use app\models\Notification;

class Notifications
{
    public function createNotification($userId, $content)
    {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->content = "$content";
        $notification->reception_day = date("Y-m-d");
        $notification->send_at = date("Y-m-d H:i:s");
        $notification->open_at = date("Y-m-d H:i:s");
        $notification->save();
        return $notification;
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOne($id);
        $notification->is_read = 1;
        $notification->save();
    }

    public function getUnreadNotifications($userId)
    {
        $notification = Notification::find()->where(['is_read' => false])->andWhere(['user_id' => $userId])->all();
        return $notification;
    }

    public function deleteNotification(){
        $notification = "DELETE FROM " . self::tableName() . " WHERE id = :id";
        return DBModel::prepare($notification)->execute(['id' => $this->id]);
    }

    public static function deleteAllNotificationsByUser(int $userId)
    {
        $notification = "DELETE FROM " . self::tableName() . " WHERE user_id = :user_id";
        return DBModel::prepare($notification)->execute(['user_id' => $userId]);
    }
}