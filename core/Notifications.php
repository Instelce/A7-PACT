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
        $notification->open_at = date("H:i");
        $notification->save();
        return $notification;
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOne($id);
        $notification->is_read = 1;
        $notification->save();
        return true;
    }

    public function getUnreadNotifications($userId)
    {
        $notification = Notification::find()->where(['is_read' => false])->andWhere(['user_id' => $userId])->all();
        return $notification;
    }
}

// Scénarios des notifications :
//
// Membre A like membre B
// Membre commente pro
// Membre envois message à pro
// Pro like membre
// Pro Répond membre
// Pro envois message à membre
