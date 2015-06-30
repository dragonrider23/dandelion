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

use Dandelion\Template;
use Dandelion\Utils\View;
use Dandelion\Utils\Updater;
use Dandelion\Exception\Template404Exception;

class UpdateController extends BaseController
{
    public function update()
    {
        // Temporary redirect, just so the infrastructure is in place
        View::redirect('index');
        return;

        // if (!Updater::needsUpdated()) {
        //     View::redirect('index');
        //     return;
        // }
    }
}