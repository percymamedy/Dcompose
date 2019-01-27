<?php

if (!function_exists('home_dir')) {
    /**
     * Return the home directory of Dcompose.
     *
     * @return array|false|null|string
     */
    function home_dir()
    {
        // Cannot use $_SERVER superglobal since that's empty during UnitUnishTestCase
        // getenv('HOME') isn't set on Windows and generates a Notice.
        $home = getenv('HOME');

        if (!empty($home)) {
            // home should never end with a trailing slash.
            $home = rtrim($home, '/');
        } elseif (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            $home = rtrim($home, '\\/');
        }

        return empty($home) ? null : $home . DIRECTORY_SEPARATOR . '.dcompose';
    }
}

if (!function_exists('ddd')) {
    /**
     * Advanced dumped and die.
     *
     * @param mixed $variable
     * @param null  $depth
     */
    function ddd($variable, $depth = null)
    {
        \Kint::$max_depth = $depth;

        Kint::dump($variable);

        exit;
    }
}

if (!function_exists('recurse_copy')) {
    /**
     * Recursively copy one folder to another.
     *
     * @param string $src
     * @param string $dst
     *
     * @return void
     */
    function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    recurse_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }
}
