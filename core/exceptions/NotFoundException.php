<?php

namespace app\core\exceptions;

class NotFoundException extends \Exception
{
    protected $message = "Not found";
    protected $code = 404;
}