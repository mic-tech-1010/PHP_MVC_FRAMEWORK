<?php

namespace Core\View;

class TemplateEngine
{
    protected string $templatePath;
    protected string $cachePath;
    protected array $compiledViews = [];

    public function __construct(string $templatePath, string $cachePath)
    {
        $this->templatePath = rtrim($templatePath, '/\\');
        $this->cachePath = rtrim($cachePath, '/\\');
    }

    /**
     * Compile the given template file into a cached PHP file.
     */
    public function compile(string $view): string
    {
        $templateFile = $this->templatePath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.blade.php';

        if (!file_exists($templateFile)) {
            throw new \Exception("Template not found: {$templateFile}");
        }

        $compiledFile = $this->cachePath . DIRECTORY_SEPARATOR . md5($templateFile) . '.php';

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        // Read the main template
        $template = file_get_contents($templateFile);

        // Detect dependencies
        $dependencies = $this->getDependencies($template);

        // Decide whether to recompile
        $needsRecompile =
            !file_exists($compiledFile) ||
            filemtime($templateFile) > filemtime($compiledFile) ||
            $this->hasDependencyChanged($dependencies, $compiledFile);

        if ($needsRecompile) {
            $compiled = $this->compileTemplate($template, $view);
            file_put_contents($compiledFile, $compiled);
        }

        return $compiledFile;
    }


    /**
     * get the dependencies of a template (extends and includes)
     */
    protected function getDependencies(string $template, array &$collected = []): array
    {
        // Step 1: find direct @extends and @include directives
        $pattern = '/@(?:extends|include)\([\'"](.+?)[\'"]\)/';
        if (preg_match_all($pattern, $template, $matches)) {
            foreach ($matches[1] as $viewName) {
                $filePath = $this->templatePath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $viewName) . '.blade.php';

                if (file_exists($filePath) && !in_array($filePath, $collected)) {
                    $collected[] = $filePath;

                    // Step 2: check inside that dependency for more includes
                    $nestedTemplate = file_get_contents($filePath);
                    $this->getDependencies($nestedTemplate, $collected);
                }
            }
        }

        return $collected;
    }

    /**
     * Check if any dependencies have changed since last compilation
     */
    protected function hasDependencyChanged(array $dependencies, string $compiledFile): bool
    {
        foreach ($dependencies as $file) {
            if (file_exists($file) && filemtime($file) > filemtime($compiledFile)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle @extends, @section, and @yield
     */
    protected function compileTemplate(string $template, string $view): string
    {
        //Handle extends
        if (preg_match('/@extends\([\'"](.+?)[\'"]\)/', $template, $matches)) {
            $parentView = $matches[1];
            $parentFile = $this->templatePath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $parentView) . '.blade.php';

            if (!file_exists($parentFile)) {
                throw new \Exception("parent layout not found: {$parentFile}");
            }

            $parentFileContent = file_get_contents($parentFile);

            // Extract sections from child
            preg_match_all('/@section\([\'"](.+?)[\'"]\)(.*?)@endsection/s', $template, $sections, PREG_SET_ORDER);

            foreach ($sections as $section) {
                $name = $section[1];
                $content = trim($section[2]);

                //Replace @yield('name') in parent with this section's content
                $parentFileContent = preg_replace(
                    '/@yield\([\'"]' . preg_quote($name, '/') . '[\'"]\)/',
                    $content,
                    $parentFileContent
                );
            }

            $template = $parentFileContent;
        }

        // Handle Includes
        $template = $this->processIncludes($template);

        // convert blade syntax to php
        return $this->parseTemplate($template);
    }


    /**
     * process @includes directives
     */
    protected function processIncludes(string $content): string
    {
        return preg_replace_callback('/@include\([\'"](.+?)[\'"]\)/', function ($matches) {
            $includedView = $matches[1];

            // prevent circular include
            if (in_array($includedView, $this->compiledViews)) {
                throw new \Exception("Circular include detected: {$includedView}");
            }

            $this->compiledViews[] = $includedView;

            $includedFile = $this->templatePath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $includedView) . '.blade.php';
            if (!file_exists($includedFile)) {
                throw new \Exception("Included view not found: {$includedFile}");
            }

            $includedTemplate = file_get_contents($includedFile);

            // Recursively compile includes safely
            $compiledIncluded = $this->compileTemplate($includedTemplate, $includedView);

            return $compiledIncluded;
        }, $content);
    }

    /**
     * Convert Blade-like syntax into PHP
     */
    protected function parseTemplate(string $content): string
    {
        // Very simple replacements — you can extend this later
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1) ?>', $content);
        $content = preg_replace('/\@\s*if\s*\((.+?)\)/', '<?php if ($1): ?>', $content);
        $content = preg_replace('/\@\s*elseif\s*\((.+?)\)/', '<?php elseif ($1): ?>', $content);
        $content = preg_replace('/\@\s*else/', '<?php else: ?>', $content);
        $content = preg_replace('/\@\s*endif/', '<?php endif; ?>', $content);
        $content = preg_replace('/\@\s*foreach\s*\((.+?)\)/', '<?php foreach ($1): ?>', $content);
        $content = preg_replace('/\@\s*endforeach/', '<?php endforeach; ?>', $content);

        return $content;
    }
}
