<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\forms\LoginForm;
use app\forms\PublicProfessionalRegister;
use app\models\account\UserAccount;
use app\models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
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
        return $this->render('auth/register');
    }
    public function registerProfessionalPublic(Request $request)
    {
        $pro = new PublicProfessionalRegister();

        if ($request->isPost()) {
            $pro->loadData($request->getBody());

            if ($pro->validate() && $pro->register()) {
                Application::$app->session->setFlash("success", "Your account has been created successfully");
                Application::$app->response->redirect('/');
                exit;
            }

            return $this->render('auth/registerProfessional', [
                'model' => $pro
            ]);
        }

        return $this->render('auth/registerProfessional', [
            'model' => $pro
        ]);
    }

    public function registerProfessionalPrivate(Request $request)
    {
        $pro = new PrivateProfessionalRegister();

        if ($request->isPost()) {
            $pro->loadData($request->getBody());

            if ($pro->validate() && $pro->register()) {
                Application::$app->session->setFlash("success", "Your account has been created successfully");
                Application::$app->response->redirect('/');
                exit;
            }

            return $this->render('auth/registerProfessional', [
                'model' => $pro
            ]);
        }

        return $this->render('auth/registerProfessional', [
            'model' => $pro
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        Application::$app->logout();
        Application::$app->session->setFlash('success', 'Vous avez été déconnecté.');
        $response->redirect('/');
    }

    public function profile() {
        if (Application::$app->user->isProfessional()) {
            $this->setLayout('back-office');
        }
        return $this->render('profile');
    }
}