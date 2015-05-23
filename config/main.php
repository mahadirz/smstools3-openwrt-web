<?php

require_once 'helpers.php';

$app->setConfig('siteName', 'SMS Tools 3 Web');
$app->setConfig('developmentMode', false);
$app->setConfig('smsdConf', '/etc/smsd.conf');

$app->setConfig('username', 'admin');
$app->setConfig('password', 'admin');

// Add your own configuration values here or in a separate file

if($app->getConfig('developmentMode'))
{
    $workingDir = getcwd().DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."tests";
    $app->setConfig('sentDir', $workingDir.DIRECTORY_SEPARATOR.'sentDir');
    $app->setConfig('inboxDir', $workingDir.DIRECTORY_SEPARATOR.'inboxDir');
    $app->setConfig('outboxDir', $workingDir.DIRECTORY_SEPARATOR.'outboxDir');
    $app->setConfig('templateDir', 'sms');
}
else
{
    $smsdConfArray = parse_ini_file($app->getConfig('smsdConf'),true);
    $app->setConfig('sentDir', $smsdConfArray['sent']);
    $app->setConfig('inboxDir', $smsdConfArray['incoming']);
    $app->setConfig('outboxDir', $smsdConfArray['outgoing']);
    $app->setConfig('templateDir', 'sms');

    $app->setConfig('device', $smsdConfArray[$smsdConfArray['devices']]['device']);
}
