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

        // Extract variables to be available in the view
        extract($data, EXTR_SKIP);

        // Capture the output
        ob_start();
        include $compiledPath;
        return ob_get_clean();
    }
}
