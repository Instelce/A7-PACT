<?php

namespace app\core\exceptions;

class ForbiddenException extends \Exception
{
    protected $message = "Vous n'avez pas la permission d'accéder à cette page.";
    protected $code = 403;
}