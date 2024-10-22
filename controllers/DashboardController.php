<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;

class DashboardController extends Controller
{
    public function offers(Request $request, Response $response) {
        $offers = Offer::find(['id'=>Application::$app->user->account_id]);

        return $this->render('/dashboard/offres', [
            'offers' => $offers ,
        ]);
    }
}