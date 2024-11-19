<?php

namespace app\core\form;

use app\core\Model;

class Form
{
    public static function begin($action, $method, $id = '', $class = '')
    {
        echo "<form action='$action' method='$method' id='$id' class='$class'>";
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attr)
    {
        return new InputField($model, $attr);
    }

    public function textarea(Model $model, $attr, int $rows = 4)
    {
        return new TextareaField($model, $attr, $rows);
    }

    public function error(Model $model, $attr)
    {
        return new FieldErrors($model, $attr);
    }
}