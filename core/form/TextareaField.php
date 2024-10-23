<?php

namespace app\core\form;

use app\core\Model;

class TextareaField extends BaseField
{

    public function __construct(Model $model, string $attr)
    {
        parent::__construct($model, $attr);
    }

    public function renderInput(): string
    {
        return sprintf('<x-input %s>
                    <p slot="label">%s</p>
                    <textarea slot="input" id="%s" name="%s" placeholder="%s" %s>%s</textarea>
                    %s
        </x-input>',
            $this->model->hasError($this->attr) ? 'data-invalid' : '', // other class
            $this->model->getLabel($this->attr) ?? ucfirst($this->attr), // label
            $this->attr, // id
            $this->attr, // name
            $this->model->getPlaceholder($this->attr) ?? '', // placeholder
            $this->model->rules()[$this->attr][0] === Model::RULE_REQUIRED ? 'required' : '', // required or not
            $this->model->{$this->attr}, // value
            $this->renderError()
        );
    }

    protected function renderError()
    {
        return sprintf('<p slot="error">%s</p>', $this->model->getFirstError($this->attr));
    }
}