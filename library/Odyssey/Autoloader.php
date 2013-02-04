<?php

/*
 * This file is part of Odyssey theme for wordpress.
 *
 * (c) 2013 Pierre Bodilis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Odyssey class autoloader.
 */
class Odyssey_Autoloader
{

    private $baseDir;

    /**
     * Autoloader constructor.
     *
     * @param string $baseDir Odyssey library base directory (default: dirname(__FILE__).'/..')
     */
    public function __construct($baseDir = null)
    {
        if ($baseDir === null) {
            $this->baseDir = dirname(__FILE__).'/..';
        } else {
            $this->baseDir = rtrim($baseDir, '/');
        }
    }

    /**
     * Register a new instance as an SPL autoloader.
     *
     * @param string $baseDir Odyssey library base directory (default: dirname(__FILE__).'/..')
     *
     * @return Odyssey_Autoloader Registered Autoloader instance
     */
    public static function register($baseDir = null)
    {
        $loader = new self($baseDir);
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Autoload Odyssey classes.
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        if (strpos($class, 'Odyssey') !== 0) {
            return;
        }

        $file = sprintf('%s/%s.php', $this->baseDir, str_replace('_', '/', $class));
        if (is_file($file)) {
            require $file;
        }
    }
}
