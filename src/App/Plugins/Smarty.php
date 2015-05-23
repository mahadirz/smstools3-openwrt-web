<?php

namespace App\Plugins;

use \Swiftlet\Abstracts\Plugin as PluginAbstract;

/**
 * Plugin for Smarty
 */
class Smarty extends PluginAbstract
{
    /**
     * register smarty hook
     */
    public function actionBefore()
    {
        $this->view->smarty = new \Smarty();
        $this->view->smarty->setTemplateDir('src/App/views/');
        $this->view->smarty->setCompileDir('vendor/smarty/smarty/demo/templates_c/');
        $this->view->smarty->setConfigDir('vendor/smarty/demo/config/');
        $this->view->smarty->setCacheDir('vendor/smarty/demo/cache/');

    }

    public function actionAfter()
    {
        $this->view->smarty->assign('publicRootPath',$this->getPublicRootPath());
    }

    public function renderAfter()
    {
        $extension = explode('.',$this->view->name);

        $this->view->smarty->assign('performance',$_SESSION['performance']);

        if(strcasecmp($extension[count($extension)-1],"tpl")==0){
            $this->view->smarty->display($this->view->name);
        }
    }



    /**
     * get path relative to root directory
     */
    public function getPublicRootPath(){
        if ( !empty($_SERVER['REQUEST_URI']) ) {
            $rootPath = preg_replace('/(index\.php)?(\?.*)?$/', '', rawurldecode($_SERVER['REQUEST_URI']));
        }

        // Run from command line, e.g. "php index.php -q index"
        $opt = getopt('q:');

        if ( isset($opt['q']) ) {
            $_GET['q'] = $opt['q'];
        }

        if ( !empty($_GET['q']) ) {
            //$rootPath = $_GET['q'];
            return '';

        }

        $scriptName = str_replace('index.php','',$_SERVER['SCRIPT_NAME']);
        $publicPath = str_replace($scriptName,'',$rootPath);

        $relativePath = '';
        $splitPath = explode('/',$publicPath);
        for($i=0;$i<count($splitPath)-1;$i++){
            $relativePath .= '../';
        }
        return $relativePath;

    }
}