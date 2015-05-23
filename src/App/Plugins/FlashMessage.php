<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/21/2015
 * Time: 6:47 PM
 */

namespace App\Plugins;

use App\Libraries\FlashMessages;
use App\Libraries\SessionStorage;
use \Swiftlet\Abstracts\Plugin as PluginAbstract;

/**
 * FlashMessage plugin
 */
class FlashMessage extends PluginAbstract {

    /**
     * initialize flash message
     */
    public function actionBefore()
    {
        $this->view->flashMessages = new FlashMessages();
    }

    /**
     * Assign flash message into smarty
     */
    public function actionAfter()
    {
        $this->view->smarty->assign('flashMessages',$this->view->flashMessages);
    }
}