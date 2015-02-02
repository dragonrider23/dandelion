<?php
/**
 * Main Dandelion application
 */
namespace Dandelion;

use \Dandelion\Utils\Updater;
use \Dandelion\Utils\Configuration;
use \Dandelion\Storage\MySqlDatabase;
use \Dandelion\Session\SessionManager;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class Application
{
    const VERSION = '6.0.0';

    public $url;
    public $paths = [];
    public $config;

    /**
     *  @param $url string - The request URI
     */
    public function __construct()
    {
        // Check for and apply updates
        //Updater::checkForUpdate();

        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run()
    {
        // Register logging system
        Logging::register($this, $this->paths['app'].'/logs');

        // Load application configuration
        $this->config = Configuration::load($this->paths);

        $this->loadLegacyCode();

        // Setup session manager
        SessionManager::register($this);
        SessionManager::startSession($this->config['phpSessionName']);

        include $this->paths['app'] . '/routes.php';
        include $this->paths['app'] . '/filters.php';
        list($class, $method, $params) = Routes::route($this->url);

        if (!$class || !class_exists($class)) {
            Logging::errorPage("Controller '{$class}' wasn't found.");
            return;
        }

        $controller = new $class($this);
        if (method_exists($controller, $method)) {
            call_user_func_array(array($controller, $method), $params);
        } else {
            Logging::errorPage("Method '{$method}' wasn't found in Class '{$class}'.");
        }
        return;
    }

    public function loadLegacyCode()
    {
        // Give database class the info to connect
        MySqlDatabase::getInstance($this->config['db'], true);

        // Define constants
        define('DEBUG_ENABLED', $this->config['debugEnabled']);
        define('PUBLIC_DIR', $this->paths['public']);
        define('THEME_DIR', 'assets/themes');
        define('DEFAULT_THEME', $this->config['defaultTheme']);
        return;
    }

    public function bindInstallPaths(array $paths)
    {
        $this->paths = array_merge($this->paths, $paths);
        return;
    }
}