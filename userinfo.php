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
require_once 'classes/IEContact.php';
require_once 'classes/IEContactgroup.php';
require_once 'classes/IEHost.php';
require_once 'classes/IEHostgroup.php';
require_once 'classes/IETimeperiod.php';
require_once 'classes/IECommand.php';
require_once 'classes/IEServicegroup.php';


$OPage->onlyForLogged();


$OPage->addItem(new IEPageTop(_('Icinga Editor')));

$UserID = $OPage->getRequestValue('user_id','int');

$User= new EaseUser($UserID);

$UserInfoFrame = $OPage->column1->addItem( new EaseHtmlFieldSet($User->getUserLogin()) );
$UserInfoFrame->addItem($User);



$Contact = new IETimeperiod();
$PocTimeperiods = $Contact->getMyRecordsCount($UserID);
if ($PocTimeperiods) {
    $Success = $OPage->column3->addItem(new EaseHtmlDivTag('Timeperiod', new EaseTWBLinkButton('timeperiods.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s časových period'), $PocTimeperiods)), array('class' => 'alert alert-success')));
} 

$Host = new IEHost();
$PocHostu = $Host->getMyRecordsCount($UserID);
if ($PocHostu) {
    $Success = $OPage->column2->addItem(new EaseHtmlDivTag('Host', new EaseTWBLinkButton('hosts.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s hostů'), $PocHostu)), array('class' => 'alert alert-success')));
} else {
    if ($PocTimeperiods) {
        $Warning = $OPage->column2->addItem(new EaseHtmlDivTag('Host', _('Nemáte definovaný žádný host'), array('class' => 'alert alert-info')));
        $Warning->addItem(new EaseTWBLinkButton('host.php', _('Založit první host <i class="icon-edit"></i>')));
    }
}

$Hostgroup = new IEHostgroup();
$PocHostgroups = $Hostgroup->getMyRecordsCount($UserID);
if ($PocHostgroups) {
    $Success = $OPage->column2->addItem(new EaseHtmlDivTag('Hostgroup', new EaseTWBLinkButton('hostgroups.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s skupin hostů'), $PocHostgroups)), array('class' => 'alert alert-success')));
}


$Command = new IECommand();
$PocCommands = $Command->getMyRecordsCount($UserID);
if ($PocCommands) {
    $Success = $OPage->column3->addItem(new EaseHtmlDivTag('Command', new EaseTWBLinkButton('commands.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s příkazů'), $PocCommands)), array('class' => 'alert alert-success')));
}

$Service = new IEService();
$PocServices = $Service->getMyRecordsCount($UserID);
if ($PocServices) {
    $Success = $OPage->column3->addItem(new EaseHtmlDivTag('Service', new EaseTWBLinkButton('services.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s služeb'), $PocServices)), array('class' => 'alert alert-success')));
} else {
    if ($PocCommands) {
        if ($PocTimeperiods) {
            $Warning = $OPage->column3->addItem(new EaseHtmlDivTag('Host', _('Nemáte definovaný žádné služby'), array('class' => 'alert alert-info')));
            $Warning->addItem(new EaseTWBLinkButton('service.php', _('Založit první službu') . ' <i class="icon-edit"></i>'));
        }
    }
}

$Servicegroup = new IEServicegroup();
$PocServicegroups = $Servicegroup->getMyRecordsCount($UserID);
if ($PocServicegroups) {
    $Success = $OPage->column3->addItem(new EaseHtmlDivTag('Servicegroup', new EaseTWBLinkButton('servicegroups.php', _('<i class="icon-list"></i>').' '.sprintf(_('Definováno %s skupin služeb'), $PocServicegroups)), array('class' => 'alert alert-success')));
}

if($OUser->getSettingValue('admin')){
    $OPage->column1->addItem(new EaseTWBLinkButton('login.php?force_id='.$UserID, _('Přihlásit se jako uživatel <i class="icon-refresh"></i>')));
}

$OPage->addItem(new IEPageBottom());


$OPage->draw();
?>
