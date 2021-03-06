<?php

/**
 * Icinga Editor - titulní strana
 *
 * @package    IcingaEditor
 * @subpackage WebUI
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2012 Vitex@hippy.cz (G)
 */
require_once 'includes/IEInit.php';
require_once 'classes/IEHost.php';
require_once 'classes/IEFXPreloader.php';

$oPage->onlyForLogged();

$hostId = $oPage->getRequestValue('host_id', 'int');

if ($hostId == 0) {
    $oPage->redirect('hosts.php');
    exit();
}

$host = new IEHost($hostId);

$operation = $oPage->getRequestValue('operation');
switch ($operation) {
    case 'confirm':
        $state = $oPage->getRequestValue('confirm');
        if ($state == 'on') {
            $host->setDataValue('config_hash', $host->getConfigHash());
        } else {
            $host->setDataValue('config_hash', null);
        }
        if ($host->saveToMySQL()) {
            $host->addStatusMessage(_('Stav nasazení senzoru byl nastaven  ručně.'));
        }

        break;

    default:
        break;
}


$oPage->addItem(new IEPageTop(_('Sensor')));

$oPage->container->addItem(new IESensorTool($host));

$oPage->addItem(new IEPageBottom());

$oPage->draw();
