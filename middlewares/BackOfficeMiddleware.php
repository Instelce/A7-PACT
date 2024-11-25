<?php

namespace app\middlewares;

use app\core\Application;
use app\core\exceptions\ForbiddenException;
use app\core\middlewares\BaseMiddleware;

class BackOfficeMiddleware extends BaseMiddleware
{
    public array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::$app->userType !== 'professional') {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}