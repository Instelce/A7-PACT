<?php

namespace app\core;

class View
{
    public string $title = '';
    // Name of the custom css file for the view
    public string $cssFile = '';
    // Name of the custom js file for the view
    public string $jsFile = '';

    public bool $waves = false;
    public bool $leaflet = false;

    public function renderView(string $view, array $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent(string $viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params = [])
    {
        foreach ($params as $key => $value) {
            $$key = $value; //
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    /**
     * @param $model DBModel | DBModel[]
     */
    public function json($model)
    {
        header('Content-Type: application/json');
        if (is_array($model)) {
            $res = [];
            foreach ($model as $m) {
                $res[] = $m->toJson();
            }
            echo json_encode($res);
        } else {
            echo json_encode($model->toJson());
        }
    }
}