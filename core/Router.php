<?php

namespace app\core;

use app\core\exceptions\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * Return the view of the current route
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        $routes = $this->routes[$method] ?? false;

        if (!$routes) {
            throw new NotFoundException();
        }

        // Check for static route
        $callback = $routes[$path] ?? false;
        if ($callback) {
            if (is_string($callback)) {
                return Application::$app->view->renderView($callback);
            }

            if (is_array($callback)) {
                /**
                 * New instance of the controller
                 * @var Controller $controller
                 */
                $controller = new $callback[0];
                Application::$app->controller = $controller;
                $controller->action = $callback[1];
                $callback[0] = $controller;

                foreach ($controller->getMiddlewares() as $middleware) {
                    $middleware->execute();
                }
            }

            // Call the callback function, and pass request and response to the method
            return call_user_func($callback, $this->request, $this->response);
        }


        foreach ($routes as $route => $callback) {
            $routeRegex = preg_replace_callback('/<(\w+):(\w+)>/', function ($matches) {
                $paramName = $matches[1];
                $type = $matches[2];

                // Support different types like int, string
                if ($type === 'int') {
                    return '(?P<' . $paramName . '>\d+)'; // Match digits for integer type with param name
                } elseif ($type === 'string') {
                    return '(?P<' . $paramName . '>\w+)'; // Match word-like strings for string type with param name
                }

                return '(?P<' . $paramName . '>\w+)';
            }, $route);

            // Get the params name (`/route/<pk:int>` = pk)

            $routeRegex = str_replace('/', '\/', $routeRegex);
            $pattern = "/^{$routeRegex}$/";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                echo "<pre>";
                var_dump($params);
                echo "</pre>";

                if (is_array($callback)) {
                    /**
                     * New instance of the controller
                     * @var Controller $controller
                     */
                    $controller = new $callback[0];
                    Application::$app->controller = $controller;
                    $controller->action = $callback[1];
                    $callback[0] = $controller;

                    foreach ($controller->getMiddlewares() as $middleware) {
                        $middleware->execute();
                    }
                }

                // Call the callback function, and pass request and response to the method
                return call_user_func($callback, $this->request, $this->response, $params);
            }
        }

        throw new NotFoundException();
    }

}