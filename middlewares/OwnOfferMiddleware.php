<?php

namespace app\middlewares;

use app\core\Application;
use app\core\exceptions\ForbiddenException;
use app\core\middlewares\BaseMiddleware;

class OwnOfferMiddleware extends BaseMiddleware
{
    public array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (!Application::$app->user->isProfessional() || !Application::$app->router->hasParams('pk') || !Application::$app->user->specific()->hasOffer(Application::$app->router->getParams('pk'))) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}