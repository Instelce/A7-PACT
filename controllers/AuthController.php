<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\forms\LoginForm;
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

    public function logout(Request $request, Response $response)
    {
        Application::$app->logout();
        Application::$app->session->setFlash('success', 'Vous avez été déconnecté.');
        $response->redirect('/');
    }

    public function profile() {
        return $this->render('profile');
    }
}