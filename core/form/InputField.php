<?php

namespace app\core\form;

use app\core\Model;

class InputField extends BaseField
{
    public const TEXT_TYPE = 'text';
    public const EMAIL_TYPE = 'email';
    public const PASSWORD_TYPE = 'password';
    public const NUMBER_TYPE = 'number';

    public string $type;

    public function __construct(Model $model, string $attr)
    {
        $this->type = self::TEXT_TYPE;
        parent::__construct($model, $attr);
    }

    public function passwordField()
    {
        $this->type = self::PASSWORD_TYPE;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control %s">',
            $this->type,
            $this->attr,
            $this->model->{$this->attr},
            $this->model->hasError($this->attr) ? 'is-invalid' : ''
        );
    }
}