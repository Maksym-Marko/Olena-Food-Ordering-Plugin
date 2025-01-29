<?php

defined('ABSPATH') || exit;

if (!function_exists('vajofoView')) {
    /**
     * This function allow you to connect view to controller.
     * Use this function in \includes\Admin\controllers\{file}.php
     * 
     * @param string $view       File name in the \includes\Admin\views\ folder.
     *                           Use without ".view.php".
     * 
     * @param array $attributes  Here you can pass any number of variables
     *                           to use them in the view file.
     *
     * @return void              require a PHP file
     */

    function vajofoView($view, $attributes = [])
    {

        extract($attributes);

        $path = VAJOFO_PLUGIN_ABS_PATH . "includes/Admin/views/{$view}.view.php";

        if (!file_exists($path)) return false;

        return require $path;
    }
}

if (!function_exists('vajofoRequireGutenbergComponent')) {
    /**
     * This function allow you to connect a component to a 
     * Gutenberg block.
     * Use this function to requite 
     * \includes\Features\Gutenberg\components\{$file}.php
     * 
     * @param string $file       File name in the 
     * \includes\Features\Gutenberg\components\ folder.
     *                           Use without ".php".
     * 
     * @param array $attributes  Here you can pass any number of variables
     *                           to use them in the component.
     *
     * @return void              require a PHP file
     */

    function vajofoRequireGutenbergComponent($file, $attributes = [])
    {

        extract($attributes);

        $path = VAJOFO_PLUGIN_ABS_PATH . "includes/Features/Gutenberg/components/{$file}.php";

        if (!file_exists($path)) return false;

        return require $path;
    }
}

if (!function_exists('vajofoRequireFrontendComponent')) {
    /**
     * This function allow you to connect a component to a 
     * Frontend php code.
     * Use this function to requite 
     * \includes\Frontend\components\{$file}.php
     * 
     * @param string $file       File name in the 
     * \includes\Frontend\components\ folder.
     *                           Use without ".php".
     * 
     * @param array $attributes  Here you can pass any number of variables
     *                           to use them in the component.
     *
     * @return void              require a PHP file
     */

    function vajofoRequireFrontendComponent($file, $attributes = [])
    {

        extract($attributes);

        $path = VAJOFO_PLUGIN_ABS_PATH . "includes/Frontend/components/{$file}.php";

        if (!file_exists($path)) return false;

        return require $path;
    }
}

if (!function_exists('vajofoArrayRecursiveDiff')) {
    /**
     * Recursively compare two arrays and find differences
     * including nested arrays
     * 
     * @param array $array1 First array to compare
     * @param array $array2 Second array to compare
     * @return array Array containing differences
     */
    function vajofoArrayRecursiveDiff($array1, $array2)
    {
        $difference = [];

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $difference[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                if (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                    continue;
                }

                $recursiveDiff = vajofoArrayRecursiveDiff($value, $array2[$key]);
                if (!empty($recursiveDiff)) {
                    $difference[$key] = $recursiveDiff;
                }
                continue;
            }

            if ($value !== $array2[$key]) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}

if (!function_exists('vajofoAddOnsRecursiveDiff')) {
    /**
     * Recursively compare two arrays and find differences
     * including nested arrays. This function also checks for differences
     * in the second array that don't exist in the first array.
     * 
     * @param array $array1 First array to compare
     * @param array $array2 Second array to compare
     * @return array Array containing all differences between both arrays
     */
    function vajofoAddOnsRecursiveDiff($array1, $array2)
    {
        $difference = [];

        if (!is_array($array1)) {
            $array1 = [];
        }
        if (!is_array($array2)) {
            $array2 = [];
        }

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $difference[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                if (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                    continue;
                }

                $recursiveDiff = vajofoAddOnsRecursiveDiff($value, $array2[$key]);
                if (!empty($recursiveDiff)) {
                    $difference[$key] = $recursiveDiff;
                }
                continue;
            }

            if ($value !== $array2[$key]) {
                $difference[$key] = $value;
            }
        }

        foreach ($array2 as $key => $value) {
            if (!array_key_exists($key, $array1)) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
