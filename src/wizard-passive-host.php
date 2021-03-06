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
require_once 'classes/IEPassiveCheckedHostForm.php';

$oPage->onlyForLogged();

$hostName = trim($oPage->getRequestValue('host_name'));
$platform = trim($oPage->getRequestValue('platform'));
$host_group = $oPage->getRequestValue('host_group', 'int');
$host = new IEHost();
$host->owner = &$oUser;

if ($hostName && $platform) {

    $host->setData(
        array(
          $host->userColumn => $oUser->getUserID(),
          'host_name' => $hostName,
          'use' => 'generic-host',
          'platform' => 'generic',
          'register' => true,
          'generate' => TRUE,
          'platform' => $platform,
          'alias' => $hostName,
          'active_checks_enabled' => 0,
          'passive_checks_enabled' => 1,
          'check_freshness' => 1,
          'freshness_threshold' => 900, // 15m.
          'flap_detection_enabled' => 0,
          'check_command' => 'return-unknown'
        )
    );

    if ($host_group) {
        $hostgroup = new IEHostgroup($host_group);
        $host->addMember('hostgroups', $hostgroup->getId(), $hostgroup->getName());
        $hostgroup->addMember('members', $host->getId(), $host->getName());
        $hostgroup->saveToMySQL();
    }


    if ($host->saveToMysql()) {

        $hostGroup = new IEHostgroup;
        if ($hostGroup->loadDefault()) {
            $hostGroup->setDataValue($hostGroup->nameColumn, EaseShared::user()->getUserLogin());
            $hostGroup->addMember('members', $host->getId(), $host->getName());
            $hostGroup->saveToMySQL();
            $host->addMember('hostgroups', $hostGroup->getId(), $hostGroup->getName());
            $host->saveToMysql();
        }

        $oPage->redirect('host.php?host_id=' . $host->getId());
        exit();
    }
} else {
    if ($oPage->isPosted()) {
        $oPage->addStatusMessage(_('Prosím zastejte název sledovaného hosta'), 'warning');
    }
}




$oPage->addItem(new IEPageTop(_('Průvodce založením hosta')));

$oPage->container->addItem(new EaseTWBPanel(_('Nový pasivně sledovaný host'), 'info', new IEPassiveCheckedHostForm('passive')));

$oPage->addItem(new IEPageBottom());

$oPage->draw();
