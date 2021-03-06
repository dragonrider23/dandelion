<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\Application;

class DandelionAPI extends BaseModule
{
    protected $makeRepo = false;

    /**
     *  Get current verison of Dandelion
     *
     *  @return string
     */
    public function version()
    {
        return Application::VERSION;
    }
}
