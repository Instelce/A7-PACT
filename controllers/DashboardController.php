<?php
// controllers/DashboardController.php
namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\middlewares\BackOfficeMiddleware;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\Notification;
use app\models\offer\Offer;
use app\models\offer\Option;
use app\models\offer\Subscription;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferType;
use app\models\opinion\Opinion;
use app\models\opinion\OpinionReply;
use app\models\payment\Invoice;
use app\models\user\professional\ProfessionalUser;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->setLayout('back-office');
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

        $offersSubscription = [];
        foreach ($offers as $offer) {
            $offersSubscription[] = Subscription::findOne(['offer_id' => $offer->id]);
        }

        $specificData = [];
        foreach ($offers as $offer) {
            $specificData[] = $offer->specificData();
        }

        $professionalUser = ProfessionalUser::findOne(['user_id' => Application::$app->user->account_id]);

        // Add subscribe option form
        if ($request->isPost() && $request->formName() == 'add-option') {
            $option = Option::findOne(['type' => $request->getBody()['type']]);

            $subscription = new Subscription();
            $subscription->loadData($request->getBody());
            $subscription->option_id = $option->id;
            $subscription->save();

            Application::$app->session->setFlash('success', 'Option ajoutée avec succès');
            $response->redirect('/dashboard/offres');
            exit;
        }

        return $this->render('/dashboard/offres', [
            'offers' => $offers,
            'photos' => $photos,
            'offersType' => $offersType,
            'offersSubscription' => $offersSubscription,
            'specificData' => $specificData,
            'professionalUser' => $professionalUser
        ]);
    }

    public function avis(Request $request, Response $response)
    {

        if ($request->isPost()) {
            $formName = $request->formName();
            $body = $request->getBody();

            if ($formName === 'add-reply') {
                $reply = new OpinionReply();
                $reply->loadData($body);
                $reply->created_at = date('Y-m-d H:i:s');
                if ($reply->save()) {
                    Application::$app->notifications->createNotification(Opinion::findOneByPk($reply->opinion_id)->account_id, Application::$app->user->specific()->denomination . " a répondu à votre avis : " . "'$reply->comment'");
                    return $response->redirect('/dashboard/avis');
                }
            }

            if ($formName === 'delete-reply'){
                $opinion = Opinion::findOneByPk($body['opinion_id']);
                $reply = OpinionReply::findOne(['opinion_id' => $opinion->id]);
                $reply->destroy();
                return $response->redirect('/dashboard/avis');
            }
        }

        return $this->render('/dashboard/avis');
    }

    public function invoices(Request $request, Response $response)
    {
        $invoices = Invoice::query()->join(new Offer())->filters(['offer__professional_id' => Application::$app->user->account_id])->order_by(["-issue_date"])->make();
        $offers = Offer::find(['professional_id' => Application::$app->user->account_id]);
        $subscriptions = [];


        foreach ($offers as $offer) {
            $subscription = $offer->subscription();
            if ($subscription) {
                $subscriptions[] = $subscription;
            }
        }

        return $this->render('/dashboard/factures', ['invoices' => $invoices, 'offers' => $offers, 'subscriptions' => $subscriptions]);
    }

    public function invoicesPDF(Request $request, Response $response, $routeParams)
    {
        $pk = $routeParams['pk'];
        $invoiceId = $routeParams['pk'];
        $invoice = Invoice::findOneByPk($routeParams['pk']);
        /** @var Offer $offer */
        $offer = Offer::findOneByPk($invoice->offer_id);
        $user = Application::$app->user;
        $professional = $user->specific();


        //PROFESSIONAL DATA
        $professionalAddress = Address::findOneByPk($user->address_id);

        //PAYMENT DATA
        $subscriptions = $offer->monthSubscriptions();
        $type = $offer->type();


        $download = false;
        if ($request->isGet() && $request->getQueryParams('download') === 'true') {
            $download = true;
        }

        return $this->pdf("Facture $pk - $invoice->service_date - $offer->title", 'invoicePreview', [
            'pk' => $pk,
            'invoice' => $invoice,
            'offer' => $offer,
            'user' => $user,
            'professional' => $professional,
            'professionalAddress' => $professionalAddress,
            'subscriptions' => $subscriptions,
            'type' => $type,
        ], $download);
    }
    public function message(Request $request, Response $response)
    {
        return $this->render('/dashboard/message');
    }

    public function notifications(Request $request, Response $response)
    {
        $notifications = Notification::find(['user_id' => Application::$app->user->opinion_id]);
        return $this->json($notifications);
    }

    public function notificationRead(Request $request, Response $response)
    {
        $notifications = Notification::find(['user_id' => Application::$app->user->opinion_id]);
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return $response->json([]);
    }

    //supprimer une notification individuelle
    /*public function deleteAction()
    {
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $notificationId = (int) $_POST['id'];

            $notification = Notification::findOne($notificationId);

            if ($notification) {
                $notification->deleteNotification();

            }
        } else {
            echo json_encode([
                'status' => 'error',
            ]);
        }
    }*/

    // Supprimer toutes les notifications d'un utilisateur
    public function deleteAllAction()
    {
        if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
            $userId = (int) $_POST['user_id'];
            Notification::deleteAllNotificationsByUser($userId);
        } else {
            echo json_encode([
                'status' => 'error',
            ]);
        }
    }
}