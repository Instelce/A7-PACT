<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\exceptions\NotFoundException;
use app\core\form\Form;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\core\Storage;
use app\forms\LoginForm;
use app\forms\MemberRegisterForm;
use app\forms\MemberUpdateForm;
use app\forms\PrivateProfessionalRegister;
use app\forms\PublicProfessionalRegister;
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
                $response->redirect("/");
                exit;
            }
        }
        return $this->render('auth/login', ['model' => $loginForm]);
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
            var_dump("test");

            if ($proPublic->validate() && $proPublic->register()) {
                var_dump("test");
                Application::$app->session->setFlash('success', "Bienvenue $proPublic->denomination. Votre compte à bien été crée !");
                Application::$app->mailer->send($proPublic->mail, "Bienvenue $proPublic->denomination", 'welcome', ['denomination' => $proPublic->denomination]);
                $response->redirect('/');
                exit;
            }
        }

        if ($request->isPost() && $request->formName()=="private") {
            if ($proPrivate->validate() && $proPrivate->register()) {
                Application::$app->session->setFlash('success', "Bienvenue $proPrivate->denomination. Votre compte à bien été crée !");
                Application::$app->mailer->send($proPrivate->mail, "Bienvenue $proPrivate->denomination", 'welcome', ['denomination' => $proPrivate->denomination]);
                $response->redirect('/');
                exit;
            }
        }

        return $this->render('auth/register-professional', ['proPublic' => $proPublic, 'proPrivate' => $proPrivate]);
    }

    public function registerMember(Request $request, Response $response)
    {
        $form = new MemberRegisterForm();

        if ($request->isPost()) {
            $form->loadData($request->getBody());

            if ($form->validate() && $form->register()) {
                Application::$app->session->setFlash('success', "Bienvenue $form->pseudo. Votre compte à bien été crée !");
                Application::$app->mailer->send($form->mail, "Bienvenue $form->pseudo", 'welcome', ['pseudo' => $form->pseudo]);
                $response->redirect('/');
                exit;
            }
        }

        return $this->render('auth/register-member', ['model' => $form]);
    }

    public function updateAccount(Request $request, Response $response){
        $form = new MemberUpdateForm();


        if ($request->isPost() && $request->formName() === "update-main") {
            $form->loadData($request->getBody());

            if ($form->validate() && $form->update()) {
                Application::$app->session->setFlash('success', "Votre compte à bien été modifié !");
                $response->redirect('/comptes/modification');
                exit;
            }
        }

        if ($request->isPost() && $request->formName() === "update-avatar") {
            $avatarPath = Application::$app->storage->saveFile("avatar", "avatar");
            Application::$app->user->avatar_url=$avatarPath;
            Application::$app->user->update();
        }

        /*if ($request->isPost() && $request->formName() === "reset-password") {
            $form->loadData($request->getBody());

            if
        }*/

        return $this->render('auth/update-member-account', ['model' => $form]);
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