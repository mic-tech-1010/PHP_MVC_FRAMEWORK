<?php

namespace Core\Http;

use Core\Validator\Validator;
use Core\Http\Session;
use Exception;

class Request
{
    protected array $body;
    protected array $params = []; // holds dynamic route parameters

    public function __construct()
    {
        $this->body = $this->getBody();
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }

        return $path = substr($path, 0, $position);
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody()
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }

        throw new Exception("The property '{$key}' does not exist in request data.");
    }

    public function input($key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    public function all()
    {
        return $this->body;
    }

    /*----------------------------------------------
     | ROUTE PARAMETERS (new section)
     ----------------------------------------------*/
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function allParams(): array
    {
        return $this->params;
    }

    /*----------------------------------------------
     | Validator (new section)
     ----------------------------------------------*/

    public function validate(array $rules): array
    {
        $validator = new Validator($this->body, $rules);

        try {
            $validated = $validator->validate();
            return $validated;
        } catch (\Exception $e) {
            $errors = json_decode($e->getMessage(), true);

            // Store old input and errors in session flash
            Session::flash('errors', $errors);
            Session::flash('old', $this->body);

            // Redirect back
            back();
            exit;
        }
    }
}
