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
  public function parseTemplate(string $content): string
    {
        // Step 1: escaped output
        $content = preg_replace(
            '/\{\{\s*(.+?)\s*\}\}/s',
            '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>',
            $content
        );

        // Step 2: compile directives with balanced parentheses
        $content = $this->compileBalanced($content, 'if', '<?php if (%s): ?>');
        $content = $this->compileBalanced($content, 'elseif', '<?php elseif (%s): ?>');
        $content = $this->compileBalanced($content, 'foreach', '<?php foreach (%s): ?>');
        $content = $this->compileBalanced($content, 'isset', '<?php if (isset(%s)): ?>');
        $content = $this->compileBalanced($content, 'empty', '<?php if (empty(%s)): ?>');

        // Step 3: simple replacements (don’t need parentheses)
        $replacements = [
            '@else'       => '<?php else: ?>',
            '@endif'      => '<?php endif; ?>',
            '@endforeach' => '<?php endforeach; ?>',
            '@endisset'   => '<?php endif; ?>',
            '@endempty'   => '<?php endif; ?>',
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        return $content;
    }

    /**
     * Compile directives like @if(...) or @foreach(...) safely.
     */
    protected function compileBalanced(string $content, string $name, string $format): string
    {
        $pattern = '/@' . $name . '\s*\(([^()]*+(?:\((?>[^()]+|(?1))*\)[^()]*)*)\)/';
        // Explanation:
        // - Match balanced parentheses (recursive regex)
        // - Works for nested calls like @if($foo->bar(baz(1,2)))

        return preg_replace_callback($pattern, function ($matches) use ($format) {
            return sprintf($format, $matches[1]);
        }, $content);
    }
}
