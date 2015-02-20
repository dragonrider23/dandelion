<?php
/**
  * Dandelion Logbook - Keeping track of events so you remember what you did last week.
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @package Dandelion
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

use \Dandelion\Application;

/**
 * Register Composer's autoloader
 */
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo 'Error: Composer doesn\'t appear to have been installed. Please install <a href="https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx" target="_blank">Composer</a> and then run either: <br><br>Locally: <pre>$ php composer.phar install --no-dev</pre> <br>Globally: <pre>$ composer install --no-dev</pre>';
    exit(1);
}
require __DIR__.'/../vendor/autoload.php';

/**
 * Register the Dandelion specific autoloader for the API
 */
require __DIR__.'/../bootstrap/autoloader.php';

/**
 * Bootstrap.php does quite a bit of set for Dandelion
 */
$app = require __DIR__.'/../bootstrap/start.php';

/**
 * And finally, Dandelion! Let's run this thing!
 */
$app->run();
