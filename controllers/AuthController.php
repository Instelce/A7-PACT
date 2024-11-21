<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\forms\LoginForm;
use app\forms\MemberRegisterForm;
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

    public function registerProfessional(Request $request)
    {
        $pro = new PublicProfessionalRegister();

        if ($request->isPost()) {
        }

        return $this->render('auth/register-professional', [
            'model' => $pro
        ]);
    }

    public function registerMember(Request $request, Response $response)
    {
        $form = new MemberRegisterForm();

        if ($request->isPost()) {
            $form->loadData($request->getBody());

            echo '<pre>';
            var_dump($form);
            echo '</pre>';

            if ($form->validate()) {
                echo "form valid !!!!";
                $form->register();
                echo "all data is created";
                $response->redirect('/');
                Application::$app->session->setFlash('success', 'Votre compte à bien été crée !');
            }
        }

        return $this->render('auth/register-member', ['model' => $form]);
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