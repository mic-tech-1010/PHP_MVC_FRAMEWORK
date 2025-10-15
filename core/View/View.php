<?php

namespace Core\View;

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
        $viewPath = self::$basePath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        // Extract variables to be available in the view
        extract($data, EXTR_SKIP);

        // Capture the output
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}
