<?php

namespace app\core\form;

use app\core\Model;

class InputField extends BaseField
{
    public const TEXT_TYPE = 'text';
    public const NUMBER_TYPE = 'number';
    public const EMAIL_TYPE = 'email';
    public const PASSWORD_TYPE = 'password';
    public const DATE_TYPE = 'date';
    public const PHONE_TYPE = 'tel';

    public string $type;
    public string $class;

    public function __construct(Model $model, string $attr, string $class = '')
    {
        $this->type = self::TEXT_TYPE;
        $this->class = $class;
        parent::__construct($model, $attr);
    }

    public function passwordField()
    {
        $this->type = self::PASSWORD_TYPE;
        return $this;
    }

    public function numberField()
    {
        $this->type = self::NUMBER_TYPE;
        return $this;
    }

    public function dateField()
    {
        $this->type = self::DATE_TYPE;
        return $this;
    }

    public function phoneField()
    {
        $this->type = self::PHONE_TYPE;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<x-input %s %s class="%s">
                    <p slot="label">%s</p>
                    <input slot="input" id="%s" type="%s" name="%s" value="%s" placeholder="%s" %s>
                    %s
        </x-input>',
            $this->model->hasError($this->attr) ? 'data-invalid' : '', // other class
            $this->model->rules()[$this->attr][0] === Model::RULE_REQUIRED ? 'required' : '', // required or not
            $this->class, // class
            $this->model->getLabel($this->attr) ?? ucfirst($this->attr), // label
            $this->attr, // id
            $this->type, // type
            $this->attr, // name
            $this->model->{$this->attr}, // value
            $this->model->getPlaceholder($this->attr) ?? '', // placeholder
            $this->model->rules()[$this->attr][0] === Model::RULE_REQUIRED ? '' : '', // required or not
            $this->renderError()
        );
    }

    protected function renderError()
    {
        return sprintf('<p slot="error">%s</p>', $this->model->getFirstError($this->attr));
    }
}

//                    <p slot="helper">%s</p>
//    $this->model->getLabel($this->attr) ?? ucfirst($this->attr)
//$this->model->getFirstError($this->attr)
