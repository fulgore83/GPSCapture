<?php

namespace Laprimavera;

/**
 * @author      Grzegorz Galas
 * @description autoloader for app and vendor
 */
class Autoloader
{

    private $directory;
    private $prefix;
    private $prefixLength;

    /**
     * @param string $baseDirectory Base directory where the source files are located.
     */
    public function __construct($baseDirectory = __DIR__)
    {
        $this->directory = $baseDirectory;
        $this->directoryVendor = $baseDirectory . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor";
        $this->prefix = __NAMESPACE__ . '\\';
        $this->prefixLength = strlen($this->prefix);
    }

    public static function register($prepend = false)
    {
        spl_autoload_register([new self(), 'autoload'], true, $prepend);
        spl_autoload_register([new self(), 'autoloadVendor'], true, $prepend);
    }

    /**
     * autoload for app
     * 
     * @param string $className
     */
    public function autoload($className)
    {
        if (0 === strpos($className, $this->prefix)) {
            $parts = explode('\\', substr($className, $this->prefixLength));
            $filepath = $this->directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

            if (is_file($filepath)) {
                require $filepath;
            } else {
                echo $filepath . ' not exist';
            }
        }
    }

    /**
     * autoload for vendor
     * 
     * @param string $className
     */
    public function autoloadVendor($className)
    {
        if (false === strpos($className, $this->prefix)) {
            $parts = explode('\\', $className);
            $filepath = $this->directoryVendor . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

            if (is_file($filepath)) {
                require $filepath;
            } else {
                echo $filepath . ' not exist';
            }
        }
    }

}
