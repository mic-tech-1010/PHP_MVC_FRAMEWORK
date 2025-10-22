<?php

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        echo '<pre style="
            background: #181c31;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            font-family: Consolas, monospace;
            line-height: 1.4;
            white-space: pre-wrap;
            word-wrap: break-word;
        ">';

        foreach ($vars as $var) {
            echo '<span style="color:#4a90e2;">';
            var_dump($var);
            echo "</span>\n\n";
        }

        echo '</pre>';
        die(); // Stop script execution
    }
}
