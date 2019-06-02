<?php

if (!function_exists('home_dir')) {
    /**
     * Return the home directory of Dcompose.
     *
     * @return array|false|null|string
     */
    function home_dir()
    {
        // We are in testing environment.
        if (getenv('APP_ENV') === 'testing') {
            return realpath(__DIR__ . '/../../tests/fixtures/home_dir');
        }

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

if (!function_exists('work_dir')) {
    /**
     * Return the project directory.
     *
     * @return string
     */
    function work_dir()
    {
        return getenv('APP_ENV') === 'testing' ?
            realpath(__DIR__ . '/../../tests/fixtures/work_dir') :
            getcwd();
    }
}

if (!function_exists('docker_dir')) {
    /**
     * Return the directory in which all
     * docker assets will be saved.
     *
     * @return string
     */
    function docker_dir()
    {
        return work_dir() . DIRECTORY_SEPARATOR . '.docker';
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
