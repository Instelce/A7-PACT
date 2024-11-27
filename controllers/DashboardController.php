<?php
// controllers/DashboardController.php
namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\middlewares\BackOfficeMiddleware;
use app\models\offer\Offer;
use app\models\offer\OfferOption;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferType;
use app\models\user\professional\ProfessionalUser;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['offers']));
        $this->registerMiddleware(new BackOfficeMiddleware(['offers']));
    }

    public function offers(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        $offers = Offer::find(['professional_id' => Application::$app->user->account_id]);

        $photos = [];
        foreach ($offers as $offer) {
            $photos[] = OfferPhoto::findOne(['offer_id' => $offer->id]);
        }

        $offersType = [];
        foreach ($offers as $offer) {
            $offersType[] = OfferType::findOne(['id' => $offer->offer_type_id]);
        }

        $offersOption = [];
        foreach ($offers as $offer) {
            $offersOption[] = OfferOption::findOne(['offer_id' => $offer->id]);
        }

        $specificData = [];
        foreach ($offers as $offer) {
            $specificData[] = $offer->specificData();
        }

        $professionalUser = ProfessionalUser::findOne(['user_id' => Application::$app->user->account_id]);

        // Add option form
        if ($request->isPost() && $request->formName() == 'add-option') {
            $offerOption = new OfferOption();
            $offerOption->loadData($request->getBody());
            $offerOption->save();

            Application::$app->session->setFlash('success', 'Option ajoutée avec succès');
            $response->redirect('/dashboard/offres');
            exit;
        }

        return $this->render('/dashboard/offres', [
            'offers' => $offers,
            'photos' => $photos,
            'offersType' => $offersType,
            'offersOption' => $offersOption,
            'specificData' => $specificData,
            'professionalUser' => $professionalUser
        ]);
    }

    public function avis(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        return $this->render('/dashboard/avis');
    }

    public function invoices(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        return $this->render('/dashboard/factures');
    }

}