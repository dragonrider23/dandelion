<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion\Controllers;

use \Dandelion\Rights;
use \Dandelion\Utils\Repos;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\API\Module\BaseModule;
use \Dandelion\Exception\ApiException;

class ApiController extends BaseController
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * Process api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return null
     */
    public function apiCall($module, $method)
    {
        if (!$module || !$method) {
            echo self::makeDAPI(5, 'Bad API call', 'api');
            return;
        }

        if ($this->app->config['publicApiEnabled']) {
            $urlParams = new UrlParameters();
            $apikey = $urlParams->get('apikey');
            echo $this->processRequest($apikey, false, $module, $method);
        } else {
            echo self::makeDAPI(2, 'Public API disabled', 'api');
        }
        return;
    }

    /**
     * Process internal api call
     *
     * @param $module string - Name of api module to create
     * @param $method string - Method to call on module
     *
     * @return null
     */
    public function internalApiCall($module, $method)
    {
        if (!$module || !$method) {
            echo self::makeDAPI(5, 'Bad API call', 'api');
            return;
        }

        echo $this->processRequest($_SESSION['userInfo']['id'], true, $module, $method);
        return;
    }

    /**
     * Process API request
     *
     * @param string $key - API key or userID
     * @param bool $localCall - Is the call from a Dandelion component
     * @param string $subsystem - Module being called
     * @param string $request - Method being called
     *
     * @return string json
     */
    private function processRequest($key, $localCall, $module, $request)
    {
        try {
            /*
             * Declare request source as the api Default value is empty in bootstrap.php
             */
            if ($localCall) {
                define('USER_ID', $key);
            } else {
                define('USER_ID', $this->verifyKey($key));
            }

            $userRights = new Rights(USER_ID, Repos::makeRepo('Groups'));
            $urlParams = new UrlParameters();

            // Shortened alias for keymanager
            if ($module === 'key') {
                $module = 'keymanager';
            }

            // Call the requested function (as defined by the last part of the URL)
            $className = '\Dandelion\API\Module\\' . $module . 'API';
            if (!class_exists($className)) {
                throw new ApiException('Module not found', 6);
            }
            $ApiModule = new $className($this->app, $userRights, $urlParams);

            if ($ApiModule instanceof BaseModule && method_exists($ApiModule, $request)) {
                try {
                    $data = $ApiModule->$request();
                } catch (ApiException $e) {
                    $e->setModule($module);
                    throw $e;
                }
            } else {
                throw new ApiException('Bad API call', 5);
            }

            // Return DAPI object
            return self::makeDAPI(0, 'Completed', $module, $data);
        } catch (ApiException $e) {
            return self::makeDAPI($e->getCode(), $e->getMessage(), $e->getModule(), '');
        } catch (\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            return self::makeDAPI(6, 'Oops, something happened', 'api');
        }
    }

    /**
     * Checks database to see if API is present and therefore valid
     *
     * @param string $key - API key to verify
     *
     * @return bool
     */
    private function verifyKey($key)
    {
        if (!$key) {
            throw new ApiException('API key is not valid', 1);
        }

        $repo = Repos::makeRepo('Api');
        $keyValid = $repo->getKey($key);

        if ($keyValid) {
            return $keyValid['user_id'];
        } else {
            throw new ApiException('API key is not valid', 1);
        }
        return;
    }

    /**
     * Generate and return a JSON encoded 'DAPI' object
     *
     * @param int $ecode - Error code
     * @param string $status - Text status message
     * @param string $module - API where DAPI was created
     * @param array $data - Data returned from API
     *
     * @return string json
     */
    public static function makeDAPI($ecode, $status, $module, $data = '')
    {
        /**
         * DAPI array composition:
         *
         * errorcode - Integer code corresponding to some error
         * status - String message of error or feedback
         * module - String name of the API module that was called
         * data - Data returned by API module
         *
         * Error Code Meanings:
         *
         * 0 - Successful API call
         * 1 - Invalid API key
         * 2 - Public API disabled
         * 3 - Action requires active login
         * 4 - Insufficient permissions
         * 5 - General error
         * 6 - Server error
         */
        $response = array (
            'errorcode' => $ecode,
            'status' => $status,
            'module' => $module,
            'data' => $data ?: $status
        );
        return json_encode($response);
    }
}