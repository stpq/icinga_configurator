<?php

/**
 * Vrací jSon
 * @package IcingaEditor
 */
require_once 'includes/IEInit.php';

$OPage->onlyForLogged();

if (!$OUser->GetUserID()) {
    die(_('nejprve se prosím přihlaš'));
}


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');


$Request = $OPage->getRequestValue('term');
$Source = $OPage->getRequestValue('source','array');
$Limit = $OPage->getRequestValue('maxRows', 'int');
if ($Limit) {
    $Limit = 'LIMIT ' . $Limit;
} else {
    $Limit = '';
}

$MembersFound = array();

if ($Request) {
    $MembersFoundArray = EaseShared::myDbLink()->queryToArray('SELECT '.current($Source).' FROM `'. DB_PREFIX . key($Source).'` WHERE user_id='.$OUser->getUserID().' AND '.current($Source).' LIKE \'%' . EaseShared::myDbLink()->AddSlashes($Request) . '%\' ORDER BY contact_name ' . $Limit);
    if (count($MembersFoundArray)) {
        foreach ($MembersFoundArray as $Request) {
            $MembersFound[] = $Request[current($Source)];
        }
    }
}

echo json_encode($MembersFound);


?>
