<?php

namespace Core\View;

use Core\View\TemplateEngine;
use Core\App;

class View
{
    protected static string $basePath;

    /**
     * Set the base path for views (usually /resources/views)
     */
    public static function setBasePath(string $path)
    {
        self::$basePath = rtrim($path, '/\\');
    }

    /**
     * Render a view file with optional data
     */
    public static function render(string $view, array $data = []): string
    {
        $engine = new TemplateEngine(
            self::$basePath,
            dirname(App::basePath()) . '/storage/views/cache'
        );

        // Compile .blade.php template into cached PHP
        $compiledPath = $engine->compile($view);

        if (!file_exists($compiledPath)) {
            throw new \Exception("View file not found: {$compiledPath}");
        }

        // Pull flashed errors and old input from session (this removes them from session)
        $errorsArray = \Core\Http\Session::getFlash('errors', []);
        $oldInput = \Core\Http\Session::getFlash('old', []);

        // Make an ErrorBag instance and expose both $errors (object) and $old (array) in the view
        $errorBag = new \Core\Support\ErrorBag(is_array($errorsArray) ? $errorsArray : []);

        // Make helpers possible: store in globals so functions in helpers can access them
        $GLOBALS['___errors'] = $errorBag;
        $GLOBALS['___old'] = $oldInput;

        // Extract variables to be available in the view
        // $errors (ErrorBag) and $old (array) will be available directly
        extract($data, EXTR_SKIP);
        extract(['errors' => $errorBag, 'old' => $oldInput]);

        // Capture the output
        ob_start();
        include $compiledPath;
        return ob_get_clean();
    }
}
