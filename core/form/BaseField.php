<?php

namespace app\core\form;

use app\core\Model;

abstract class BaseField
{
    public Model $model;
    public string $attr;

    abstract public function renderInput(): string;

    public function __construct(Model $model, string $attr)
    {
        $this->model = $model;
        $this->attr = $attr;
    }

    public function __toString()
    {
        return sprintf('%s',
            $this->renderInput(),
        );
    }
}