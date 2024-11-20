<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;

class ApiController extends Controller
{
    public function offers(Request $request, Response $response)
    {
        $allOffers = Offer::all();//get all offer from the model
        return $this->json($allOffers);
    }
}