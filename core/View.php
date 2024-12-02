<?php

namespace app\core;

use Dompdf\Dompdf;
use Dompdf\Options;

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

    protected function layoutContent($name = null)
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        if ($name) {
            $layout = $name;
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    public function renderOnlyView($view, $params = [])
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    public function renderOnlyViewWithLayout($layout, $view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent($layout);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Send a json response
     *
     * Take a DBModel or an array of DBModel and send it as a json response
     * It make the conversion to json with the toJson method of the DBModel
     *
     * @param $model DBModel | DBModel[]
     */
    public function json(array|DBModel $model): void
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

    /**
     * Generate a pdf file
     *
     * @param string $name Name of the pdf file
     * @param string $view Name of the view to render
     * @param array $params Parameters to pass to the view
     */
    public function pdf(string $name, string $view, array $params = [], bool $download = false): void
    {
//        $options = new Options();
//        $options->setIsRemoteEnabled(true);

        $pdf = new Dompdf();
        $pdf->loadHtml($this->renderOnlyView('pdf/' . $view, $params));
        $pdf->setPaper('A4');
        $pdf->render();

        if ($download) {
            $pdf->stream($name);
        } else {
            $pdf->stream($name, ['Attachment' => 0]);
        }
    }
}