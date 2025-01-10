<?php

namespace app\core;


class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if (!$position) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === "get";
    }

    public function isPost()
    {
        return $this->method() === "post";
    }

    public function getBody()
    {
        $body = [];

        if ($this->method() === "get") {
            foreach ($_GET as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = filter_input(INPUT_GET, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                } else {
                    $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }

        if ($this->method() === "post") {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                } else {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }

            foreach ($data as $key => $value) {
                $body[$key] = $value;
            }
        }

        return $body;
    }

    public function getQueryParams($key)
    {
        if ($this->isGet()) {
            return filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return null;
    }

    public function formName()
    {
        return filter_input(INPUT_POST, 'form-name', FILTER_SANITIZE_SPECIAL_CHARS);
    }
}