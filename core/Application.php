<?php

namespace app\core;


use app\core\exceptions\NotFoundException;
use app\core\exceptions\ForbiddenException;
use app\models\account\UserAccount;
use app\models\user\AdministratorUser;
use app\models\user\MemberUser;
use app\models\user\professional\ProfessionalUser;

class Application
{
    public static string $ROOT_DIR;

    public string $layout = 'main';
    public static Application $app;
    public Router $router;
    public Database $db;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public View $view;
    public Session $session;
    public string $userClass;
    public ?DBModel $user = null;

    /**
     * @var 'visitor'|'member'|'professional'|'admin'
     */
    public string $userType = 'visitor';

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->userClass = $config['userClass'];
        $this->view = new View();

        // Get the primary key of the user from the session
        $pkValue = $this->session->get('user');

        if ($pkValue) {
            $this->userType = $this->session->get('userType');
            $pk = $this->userClass::pk();
            $this->user = $this->userClass::findOne([$pk => $pkValue]);
        } else {
            $this->userType = 'visitor';
        }
    }

    public function run() {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            // Set the status code
            if ($e instanceof NotFoundException) {
                $this->response->setStatusCode($e->getCode());
            } elseif ($e instanceof ForbiddenException) {
                $this->response->setStatusCode($e->getCode());
            } else {
                $this->response->setStatusCode(500);
            }

            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        }
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @param UserAccount $userAccount
     * @param 'member'|'professional'|'admin' $userType
     * @return bool
     */
    public function login(UserAccount $userAccount, string $userType): bool
    {
        $this->user = $userAccount;
        $this->userType = $userAccount->getType();
        $pk = $userAccount->pk();
        $pkValue = $userAccount->{$pk};

        $this->session->set('user', $pkValue);
        $this->session->set('userType', $userType);

        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }

    /**
     * Retrieve the user model
     *
     * @return DBModel|null
     */
    public function user(): ?DBModel
    {
        if ($this->user) {
            if ($this->userType === 'member') {
                return MemberUser::findOneByPk($this->user->id);
            } else if ($this->userType === 'professional') {
                return ProfessionalUser::findOneByPk($this->user->id);
            } else if ($this->userType === 'admin') {
                return AdministratorUser::findOneByPk($this->user->id);
            }
        }

        return null;
    }
}
