<?php

namespace app\core\form;

use app\core\Model;

class FieldErrors
{
    public Model $model;
    public string $attr;

    public function __construct(Model $model, string $attr)
    {
        $this->model = $model;
        $this->attr = $attr;
    }

    public function __toString()
    {
        if ($this->model->hasError($this->attr)) {
            return sprintf('<p class="field-error">%s</p>', $this->model->getFirstError($this->attr));
        }
        return '';
    }
}