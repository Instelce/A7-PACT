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
    public Storage $storage;
    public ?UserAccount $user = null;
    public Mailer $mailer;
    public Notifications $notifications;

    /**
     * @var 'visitor'|'member'|'professional'|'admin'
     */
    public string $userType = 'visitor';

    public function __construct(string $rootPath, array $config)
    {
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        error_reporting(E_ERROR | E_PARSE);

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->view = new View();
        $this->storage = new Storage();
        $this->mailer = new Mailer();
        $this->notifications = new Notifications();

        // Get the primary key of the user from the session
        $pkValue = $this->session->get('user');

        if ($pkValue) {
            $this->userType = $this->session->get('userType');
            $pk = UserAccount::pk();
            $this->user = UserAccount::findOne([$pk => $pkValue])  ?: null;
        } else {
            $this->userType = 'visitor';
        }
    }

    public function run()
    {
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

            if ($this->user && $this->user->isProfessional()) {

                $this->layout = 'back-office';
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
     * @return bool
     */
    public function login(UserAccount $userAccount): bool
    {
        $this->user = $userAccount;
        $this->userType = $userAccount->getType();
        $pk = $userAccount->pk();
        $pkValue = $userAccount->{$pk};

        $this->session->set('user', $pkValue);
        $this->session->set('userType', $this->userType);

        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
        $this->session->set('userType', 'visitor');
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
    public function getUser(): ?DBModel
    {
        if ($this->user) {
            if ($this->userType === 'member') {
                return MemberUser::findOneByPk($this->user->account_id);
            } else if ($this->userType === 'professional') {
                return ProfessionalUser::findOneByPk($this->user->account_id);
            } else if ($this->userType === 'admin') {
                return AdministratorUser::findOneByPk($this->user->account_id);
            }
        }

        return null;
    }

    public static function assets(string $path): string
    {
        return Application::$ROOT_DIR . '/html/assets/' . $path;
    }

    public static function url(string $path): string
    {
        return $_ENV['DOMAIN'] . $path;
    }
}
