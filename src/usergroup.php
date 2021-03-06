<?php

/**
 * Icinga Editor - užibatelská skupina
 *
 * @package    IcingaEditor
 * @subpackage WebUI
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2012 Vitex@hippy.cz (G)
 */
require_once 'includes/IEInit.php';
require_once 'classes/IEUserGroupForm.php';

$oPage->onlyForLogged();

$usergroup_name = $oPage->getRequestValue('usergroup_name');
$usergroup_id = $oPage->getRequestValue('usergroup_id', 'int');
$member_id = $oPage->getRequestValue('member_id', 'int');

$userGroup = new IEUserGroup($usergroup_id);

switch ($oPage->getRequestValue('action')) {
    case 'addmember':
        if ($userGroup->addMember(null, $member_id)) {
            $userGroup->addStatusMessage(_('člen byl přidán do skupiny'), 'success');
        } else {
            $userGroup->addStatusMessage(_('člen nebyl přidán do skupiny'), 'warning');
        }

        break;
    case 'delmember':
        if ($userGroup->delMember(null, $member_id)) {
            $userGroup->addStatusMessage(_('člen byl odstraněn ze skupiny'), 'success');
        } else {
            $userGroup->addStatusMessage(_('člen nbyl odstraněn ze skupiny'), 'warning');
        }
        break;
    default:

        if ($usergroup_name) {
            $userGroup->setDataValue('usergroup_name', $usergroup_name);
            if ($userGroup->saveToSQL()) {
                $userGroup->addStatusMessage(_('skupina byla uložena'), 'success');
            } else {
                $userGroup->addStatusMessage(_('skupina nebyla uložena'), 'warning');
            }
        }

        $delete = $oPage->getGetValue('delete', 'bool');
        if ($delete == 'true') {
            $userGroup->delete();
            $oPage->redirect('usergroups.php');
            exit();
        }


        break;
}



$oPage->addItem(new IEPageTop(_('Uživatelská skupina')));
$oPage->addPageColumns();


switch ($oPage->getRequestValue('action')) {
    case 'delete':
        $confirmator = $oPage->columnII->addItem(new EaseTWBPanel(_('Opravdu smazat ?')), 'danger');
        $confirmator->addItem(new EaseTWBLinkButton('?' . $userGroup->myKeyColumn . '=' . $userGroup->getID(), _('Ne') . ' ' . EaseTWBPart::glyphIcon('ok'), 'success'));
        $confirmator->addItem(new EaseTWBLinkButton('?delete=true&' . $userGroup->myKeyColumn . '=' . $userGroup->getID(), _('Ano') . ' ' . EaseTWBPart::glyphIcon('remove'), 'danger'));

        $oPage->columnI->addItem($userGroup->ownerLinkButton());


        break;
    default :
        $oPage->columnII->addItem(new IEUserGroupForm($userGroup));
        if ($userGroup->getMyKey()) {
            $oPage->columnIII->addItem($userGroup->deleteButton());
        }
        $oPage->columnI->addItem(new EaseTWBPanel(_('Skupina uživatelů'), 'info', _('Všichni členové skupiny mohou zobrazit a editovat konfigurace náležející ostatním uživatelům skupiny.')));
}




$oPage->addItem(new IEPageBottom());

$oPage->draw();
