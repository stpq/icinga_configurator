<?php

/**
 * Vrací jSon
 * @package IcingaEditor
 */
require_once 'includes/IEInit.php';

$oPage->onlyForLogged();

if (!$oUser->GetUserID()) {
    die(_('nejprve se prosím přihlaš'));
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$request = $oPage->getRequestValue('term');
$platform = $oPage->getRequestValue('platform');
$source = $oPage->getRequestValue('source', 'array');
$limit = $oPage->getRequestValue('maxRows', 'int');
if ($limit) {
    $limit = 'LIMIT ' . $limit;
} else {
    $limit = '';
}

$membersFound = array();

if ($request) {

    if ($platform) {
        $sqlConds = " AND ((`platform` =  '" . $platform . "') OR (`platform` = 'generic') OR (`platform` IS NULL) OR (`platform`='') ) ";
    } else {
        $sqlConds = '';
    }

    $query = 'SELECT `service_description` FROM `service` WHERE (user_id=' . $oUser->getUserID() . ' OR public=1) AND service_description LIKE \'%' . EaseShared::myDbLink()->AddSlashes($request) . '%\' ' . $sqlConds . ' ORDER BY  service_description ' . $limit;

    $membersFoundArray = EaseShared::myDbLink()->queryToArray($query);
    if (count($membersFoundArray)) {
        foreach ($membersFoundArray as $request) {
            $membersFound[] = $request['service_description'];
        }
    }
}

echo json_encode($membersFound);
