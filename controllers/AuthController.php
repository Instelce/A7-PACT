<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\exceptions\ForbiddenException;
use app\core\exceptions\NotFoundException;
use app\core\form\Form;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\core\Storage;
use app\core\Utils;
use app\forms\LoginForm;
use app\forms\MemberRegisterForm;
use app\forms\MemberUpdateForm;
use app\forms\PasswordForgetForm;
use app\forms\PaymentForm;
use app\forms\PrivateProfessionalRegister;
use app\forms\PrivateProfessionalUpdateForm;
use app\forms\PublicProfessionalRegister;
use app\forms\PublicProfessionalUpdateForm;
use app\models\account\UserAccount;
use app\models\User;
use app\models\user\professional\PrivateProfessional;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['updateAccount']));
    }

    public function login(Request $request, Response $response)
    {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());

            if ($loginForm->validate() && $loginForm->login()) {
                if (Application::$app->user->isProfessional()) {
                    $response->redirect("/dashboard/offres");
                    exit;
                }
                $response->redirect('/');
                exit;
            }
        }
        return $this->render('auth/login', ['model' => $loginForm]);
    }

    public function forgetPassword(Request $request, Response $response)
    {
        $form = new PasswordForgetForm();

        if ($request->isPost()) {
            $form->loadData($request->getBody());

            if ($form->validate()) {
                $form->verify();
                $response->redirect('/mail-envoye');
                exit;
            }
        }

        return $this->render('auth/password-forget', ['model' => $form]);
    }

    public function sendMail(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $mail = Application::$app->session->get('reset-password-email');

            if ($mail) {
                $user = UserAccount::findOne(['mail' => $mail]);
                if ($user) {
                    $user->reset_password_hash = Utils::generateHash();
                    $user->update();
                    Application::$app->mailer->send($mail, "Modification du mot de passe de $mail", 'reset-password', ['mail' => $mail, 'hash' => $user->reset_password_hash]);
                }
            }
        }
        return $this->render('auth/mail-send', ['mail' => $mail]);
    }



    public function register(Request $request)
    {
        $user = new UserAccount();

        if ($request->isPost()) {
            $user->loadData($request->getBody());

            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash("success", "Your account has been created successfully");
                Application::$app->response->redirect('/');
                exit;
            }

            return $this->render('auth/register', [
                'model' => $user
            ]);
        }

        return $this->render('auth/register', [
            'model' => $user
        ]);
    }


    public function registerProfessional(Request $request, Response $response)
    {
        $proPublic  = new PublicProfessionalRegister();
        $proPrivate = new PrivateProfessionalRegister();
        if ($request->isPost() && $request->formName()=="public") {
            $proPublic->loadData($request->getBody());

            if ($proPublic->validate()){
                if ($proPublic->register()) {
                    Application::$app->session->setFlash('success', "Bienvenue $proPublic->denomination. Votre compte à bien été crée !");
                    Application::$app->mailer->send($proPublic->mail, "Bienvenue $proPublic->denomination", 'welcome', ['denomination' => $proPublic->denomination]);
                    $response->redirect('/dashboard');
                    exit;
                }
            }
        }

        if ($request->isPost() && $request->formName()=="private") {
            $proPrivate->loadData($request->getBody());

            if ($proPrivate->validate()){
                if($proPrivate->register()) {
                    Application::$app->session->setFlash('success', "Bienvenue $proPrivate->denomination. Votre compte à bien été crée !");
                    Application::$app->mailer->send($proPrivate->mail, "Bienvenue $proPrivate->denomination", 'welcome', ['denomination' => $proPrivate->denomination]);
                    $response->redirect('/dashboard');
                    exit;
                }
            }
        }

        return $this->render('auth/register-professional', ['proPublic' => $proPublic, 'proPrivate' => $proPrivate]);
    }


    public function updatePublicProfessionalAccount(Request $request, Response $response){

        $this->setLayout('back-office');

        $form  = new PublicProfessionalUpdateForm();


        if ($request->isPost() && $request->formName()=="update-public") {
            $form->loadData($request->getBody());

            if (!array_key_exists('notification', $request->getBody()))  {
                $form->notifications = 0;
            }

            if ($form->passwordMatch() && $form->validate() && $form->update()) {
                Application::$app->session->setFlash('success', "Votre compte à bien été modifié !");
                $response->redirect('/comptes/modification');
            }
        }

        if ($request->isPost() && $request->formName() === "update-avatar") {
            $avatarPath = Application::$app->storage->saveFile("avatar", "avatar");
            Application::$app->user->avatar_url=$avatarPath;
            Application::$app->user->update();
        }

        if ($request->isPost() && $request->formName() === "reset-password") {
            $mail = Application::$app->user->mail;

            Application::$app->user->reset_password_hash = Utils::generateHash();
            Application::$app->user->update();

            Application::$app->mailer->send($mail, "Modification du mot de passe de $mail", 'reset-password', ['mail' => $mail, 'hash' => Application::$app->user->reset_password_hash]);
            Application::$app->session->setFlash('success', "Un mail a bien été envoyé a l'adresse $mail");
            $response->redirect('/comptes/modification');
        }

        return $this->render('auth/update-public-professional-account', ['proPublic' => $form]);
    }

    public function updatePrivateProfessionalAccount(Request $request, Response $response)
    {
        $this->setLayout('back-office');

        $form  = new PrivateProfessionalUpdateForm();

        $paymentForm = new PaymentForm(Application::$app->user->specific()->specific()->payment_id);


        if ($request->isPost() && $request->formName()=="update-private") {
            $form->loadData($request->getBody());

            if (!array_key_exists('notification', $request->getBody()))  {
                $form->notifications = 0;
            }

            if ($form->passwordMatch() && $form->validate() && $form->update()) {
                Application::$app->session->setFlash('success', "Votre compte à bien été modifié !");
                $response->redirect('/comptes/modification');
            }
        }

        if ($request->isPost() && $request->formName() === "update-avatar") {
            $avatarPath = Application::$app->storage->saveFile("avatar", "avatar");
            Application::$app->user->avatar_url=$avatarPath;
            Application::$app->user->update();
        }

        if ($request->isPost() && $request->formName() === "update-payment") {
            $paymentForm->loadData($request->getBody());
            var_dump($paymentForm->validate()); //renvoie false
            if ($paymentForm->passwordMatch() && $paymentForm->validate() && $paymentForm->update()) {
                Application::$app->session->setFlash('success', "Votre moyen de paiement à bien été modifié !");
                $response->redirect('/comptes/modification');
            }
        }

        if ($request->isPost() && $request->formName() === "reset-password") {
            $mail = Application::$app->user->mail;

            Application::$app->user->reset_password_hash = Utils::generateHash();
            Application::$app->user->update();

            Application::$app->mailer->send($mail, "Modification du mot de passe de $mail", 'reset-password', ['mail' => $mail, 'hash' => Application::$app->user->reset_password_hash]);
            Application::$app->session->setFlash('success', "Un mail a bien été envoyé a l'adresse $mail");
            $response->redirect('/comptes/modification');
        }

        return $this->render('auth/update-private-professional-account', ['proPrivate' => $form, 'paymentForm' => $paymentForm]);
    }

    public function registerMember(Request $request, Response $response)
    {
        $form = new MemberRegisterForm();

        if ($request->isPost()) {
            $form->loadData($request->getBody());

            if ($form->validate()) {
                if($form->register()){
                    Application::$app->session->setFlash('success', "Bienvenue $form->pseudo. Votre compte à bien été crée !");
                    Application::$app->mailer->send($form->mail, "Bienvenue $form->pseudo", 'welcome', ['pseudo' => $form->pseudo]);
                    $response->redirect('/');
                    exit;
                }
            }
        }

        return $this->render('auth/register-member', ['model' => $form]);
    }

    public function updateAccount(Request $request, Response $response)
    {
        $form = new MemberUpdateForm();

        if ($request->isPost() && $request->formName() == "update-main") {
            $form->loadData($request->getBody());

            if (!array_key_exists('notification', $request->getBody()))  {
                $form->notification = 0;
            }

            if ($form->passwordMatch() && $form->validate() && $form->update()) {
                Application::$app->session->setFlash('success', "Votre compte à bien été modifié !");
                $response->redirect('/comptes/modification');
            }
        }

        //////////////////////////////////////////////
        // Update avatar
        //////////////////////////////////////////////

        if ($request->isPost() && $request->formName() == "update-avatar") {
            $avatarPath = Application::$app->storage->saveFile("avatar", "avatar");
            Application::$app->user->avatar_url=$avatarPath;
            Application::$app->user->update();
        }

        //////////////////////////////////////////////
        // Reset password
        //////////////////////////////////////////////

        if ($request->isPost() && $request->formName() === "reset-password") {
            $mail = Application::$app->user->mail;

            Application::$app->user->reset_password_hash = Utils::generateHash();
            Application::$app->user->update();

            Application::$app->mailer->send($mail, "Modification du mot de passe de $mail", 'reset-password', ['mail' => $mail, 'hash' => Application::$app->user->reset_password_hash]);
            Application::$app->session->setFlash('success', "Un mail a bien été envoyé a l'adresse $mail");
            $response->redirect('/comptes/modification');
        }

        //////////////////////////////////////////////
        // Delete account
        //////////////////////////////////////////////

        /*if ($request->isPost() && $request->formName() === "delete-account") {
            if ($form->passwordMatch() && $form->validate() && $form->update()) {
                Application::$app->session->setFlash('success', "Votre compte à bien été modifié !");
                $response->redirect('/comptes/modification?tab=securite');
            }
        }*/

        return $this->render('auth/update-member-account', ['model' => $form]);
    }

    public function resetPassword(Request $request, Response $response) {
        $hash = $request->getBody()['token'];
        $user = UserAccount::findOne(['reset_password_hash' => $hash]);

        if (!$user) {
            throw new ForbiddenException();
        }

        if ($request->isPost()) {
            $user->reset_password_hash = null;
            $user->password = password_hash($request->getBody()['password'], PASSWORD_DEFAULT);
            $user->update();
            Application::$app->logout();
            $response->redirect('/connexion');
        }

        return $this->render('auth/reset-password', ['hash' => $hash]);
    }

    public function logout(Request $request, Response $response)
    {
        Application::$app->logout();
        Application::$app->session->setFlash('success', 'Vous avez été déconnecté.');
        $response->redirect('/');
    }

    public function profile(Request $request, Response $response, $routeParams) {
        $pk = $routeParams['pk'];
        $user = UserAccount::findOneByPk($pk);

        if (!$user)
        {
            throw new NotFoundException();
        }

        return $this->render('profile', ['user'=>$user]);
    }
}