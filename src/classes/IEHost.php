<?php

/**
 * Správce hostů
 *
 * @package    IcingaEditor
 * @subpackage WebUI
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2012 Vitex@hippy.cz (G)
 */
require_once 'IEService.php';
require_once 'IEPortScanner.php';
require_once 'IEHostgroup.php';
require_once 'IEIconSelector.php';

/**
 * Description of IEHosts
 *
 * @author vitex
 */
class IEHost extends IECfg
{

    public $myTable = 'host';
    public $keyword = 'host';
    public $nameColumn = 'host_name';
    public $myKeyColumn = 'host_id';

    /**
     * Weblink
     * @var string
     */
    public $webLinkColumn = 'action_url';

    /**
     * Přidat položky register a use ?
     * @var boolean
     */
    public $allowTemplating = true;

    /**
     * Dát tyto položky k dispozici i ostatním ?
     * @var boolean
     */
    public $publicRecords = true;

    /**
     * Sloupce záznamu
     * @var array
     */
    public $useKeywords = array(
      'host_name' => 'VARCHAR(255)',
      'alias' => 'VARCHAR(64)',
      'display_name' => 'VARCHAR(64)',
      'address' => 'VARCHAR(64)',
      'address6' => 'VARCHAR(128)',
      'parents' => 'IDLIST',
      'hostgroups' => 'IDLIST',
      'check_command' => 'SELECT',
      'initial_state' => "RADIO('o','d','u')",
      'max_check_attempts' => 'SLIDER',
      'check_interval' => 'INT',
      'retry_interval' => 'INT',
      'active_checks_enabled' => 'BOOL',
      'passive_checks_enabled' => 'BOOL',
      'check_period' => 'SELECT',
      'obsess_over_host' => 'BOOL',
      'check_freshness' => 'BOOL',
      'freshness_threshold' => 'INT',
      'event_handler' => 'SELECT',
      'event_handler_enabled' => 'BOOL',
      'low_flap_threshold' => 'INT',
      'high_flap_threshold' => 'INT',
      'flap_detection_enabled' => 'BOOL',
      'flap_detection_options' => "FLAGS('o','d','u')",
      'failure_prediction_enabled' => 'BOOL',
      'process_perf_data' => 'BOOL',
      'retain_status_information' => 'BOOL',
      'retain_nonstatus_information' => 'BOOL',
      'contacts' => 'IDLIST',
      'contact_groups' => 'IDLIST',
      'notification_interval' => 'INT',
      'first_notification_delay' => 'INT',
      'notification_period' => 'SELECT',
      'notification_options' => "FLAGS('d','u','r','f','s')",
      'notifications_enabled' => 'BOOL',
      'stalking_options' => "FLAGS('o','d','u')",
      'notes' => 'TEXT',
      'notes_url' => 'VARCHAR(128)',
      'action_url' => 'VARCHAR(128)',
      'icon_image' => 'VARCHAR(64)',
      'icon_image_alt' => 'VARCHAR(64)',
      'vrml_image' => 'VARCHAR(64)',
      'statusmap_image' => 'VARCHAR(64)',
      '2d_coords' => 'VARCHAR(32)',
      '3d_coords' => 'VARCHAR(64)',
      'platform' => "PLATFORM"
    );
    public $keywordsInfo = array(
      'host_name' => array(
        'severity' => 'mandatory',
        'title' => 'Jméno hosta', 'required' => true),
      'alias' => array(
        'severity' => 'optional',
        'title' => 'alias hosta', 'required' => true),
      'display_name' => array(
        'severity' => 'optional',
        'title' => 'zobrazované jméno'),
      'address' => array(
        'severity' => 'optional',
        'title' => 'IPv4 adresa ', 'mandatory' => true),
      'address6' => array(
        'severity' => 'optional',
        'title' => 'IPv6 adresa', 'mandatory' => true),
      'parents' => array(
        'severity' => 'optional',
        'title' => 'rodiče',
        'refdata' => array(
          'table' => 'host',
          'captioncolumn' => 'host_name',
          'idcolumn' => 'host_id',
          'public' => true,
          'condition' => array('register' => 1))),
      'hostgroups' => array(
        'severity' => 'optional',
        'title' => 'skupiny hostů',
        'refdata' => array(
          'table' => 'hostgroup',
          'captioncolumn' => 'hostgroup_name',
          'idcolumn' => 'hostgroup_id')
      ),
      'check_command' => array(
        'severity' => 'advanced',
        'title' => 'testovací příkaz',
        'severity' => 'optional',
        'refdata' => array(
          'table' => 'command',
          'captioncolumn' => 'command_name',
          'idcolumn' => 'command_id',
          'public' => true,
          'condition' => array('command_type' => 'check')
        )
      ),
      'initial_state' => array('title' => 'výchozí předpokládaný stav',
        'severity' => 'advanced',
        'o' => 'UP - spuštěn',
        'd' => 'DOWN - vypnut',
        'u' => 'UNREACHABLE - nedostupný',
      ),
      'max_check_attempts' => array(
        'title' => 'maximální počet pokusů',
        'severity' => 'advanced',
      ),
      'check_interval' => array('title' => 'interval otestování',
        'severity' => 'advanced',
      ),
      'retry_interval' => array(
        'severity' => 'optional',
        'title' => 'interval dalšího pokusu o test'
      ),
      'active_checks_enabled' => array(
        'severity' => 'advanced',
        'title' => 'povolit aktivní testy'),
      'passive_checks_enabled' => array(
        'severity' => 'advanced',
        'title' => 'povolit pasivní testy'),
      'check_period' => array(
        'severity' => 'optional',
        'title' => 'testovací perioda',
        'refdata' => array(
          'table' => 'timeperiod',
          'captioncolumn' => 'timeperiod_name',
          'public' => true,
          'idcolumn' => 'timeperiod_id')
      ),
      'obsess_over_host' => array(
        'severity' => 'advanced',
        'title' => 'Posedlost přes host'),
      'check_freshness' => array(
        'severity' => 'advanced',
        'title' => 'testovat čerstvost'),
      'freshness_threshold' => array(
        'severity' => 'advanced',
        'title' => 'práh čertvosti'),
      'event_handler' => array('title' => 'ošetřovač událostí',
        'severity' => 'advanced',
        'refdata' => array(
          'table' => 'command',
          'captioncolumn' => 'command_name',
          'idcolumn' => 'command_id',
          'public' => true,
          'condition' => array('command_type' => 'handler')
        )
      ),
      'event_handler_enabled' => array(
        'severity' => 'advanced',
        'title' => 'povolit ošetření událostí'),
      'low_flap_threshold' => array(
        'severity' => 'advanced',
        'title' => 'nižší práh plácání'),
      'high_flap_threshold' => array(
        'severity' => 'advanced',
        'title' => 'vyšší práh plácání'),
      'flap_detection_enabled' => array(
        'severity' => 'advanced',
        'title' => 'detekovat plácání'),
      'flap_detection_options' => array(
        'severity' => 'advanced',
        'title' => 'možnosti detekce plácání',
        'o' => 'Up',
        'd' => 'Down',
        'u' => 'Nedostupný',
      ),
      'failure_prediction_enabled' => array(
        'severity' => 'advanced',
        'title' => 'Předpokládat výpadek'),
      'process_perf_data' => array(
        'severity' => 'advanced',
        'title' => 'zpracovávat výkonostní data'),
      'retain_status_information' => array(
        'severity' => 'advanced',
        'title' => 'držet stavové informace'),
      'retain_nonstatus_information' => array(
        'severity' => 'advanced',
        'title' => 'držet nestavové informace'),
      'contacts' => array(
        'severity' => 'optional',
        'title' => 'kontakty',
        'refdata' => array(
          'table' => 'contact',
          'captioncolumn' => 'contact_name',
          'idcolumn' => 'contact_id')),
      'contact_groups' => array(
        'severity' => 'optional',
        'title' => 'členské skupiny kontaktů',
        'refdata' => array(
          'table' => 'contactgroup',
          'captioncolumn' => 'contactgroup_name',
          'idcolumn' => 'contactgroup_id')
      ),
      'notification_interval' => array(
        'severity' => 'optional',
        'title' => 'interval notifikace'),
      'first_notification_delay' => array(
        'severity' => 'advanced',
        'title' => 'první prodleva v oznamování'),
      'notification_period' => array(
        'severity' => 'optional',
        'title' => 'perioda oznamování',
        'required' => true,
        'refdata' => array(
          'table' => 'timeperiod',
          'captioncolumn' => 'timeperiod_name',
          'public' => true,
          'idcolumn' => 'timeperiod_id')
      ),
      'notification_options' => array(
        'severity' => 'advanced',
        'title' => 'oznamovat událost',
        'd' => 'Vypnutí',
        'u' => 'Nedostupnost',
        'r' => 'Obnovení',
        'f' => 'škytání',
        's' => 'plánovaný výpadek'
      ),
      'notifications_enabled' => array(
        'severity' => 'optional',
        'title' => 'povolit oznamování'),
      'stalking_options' => array(
        'severity' => 'advanced',
        'title' => 'nastavení sledování',
        'o' => 'sledovat UP stavy',
        'd' => 'sledovat DOWN stavy',
        'u' => 'sledovat UNREACHABLE stavy'),
      'notes' => array(
        'severity' => 'basic',
        'title' => 'poznámka', 'mandatory' => true),
      'notes_url' => array(
        'severity' => 'advanced',
        'title' => 'url externí poznámky'),
      'action_url' => array(
        'severity' => 'advanced',
        'title' => 'url externí aplikace'),
      'icon_image' => array(
        'severity' => 'advanced',
        'title' => 'ikona hostu', 'mandatory' => true),
      'icon_image_alt' => array(
        'severity' => 'advanced',
        'title' => 'alternativní ikona'),
      'vrml_image' => array(
        'severity' => 'advanced',
        'title' => '3D ikona'),
      'statusmap_image' => array(
        'severity' => 'advanced',
        'title' => 'ikona statusmapy'),
      '2d_coords' => array(
        'severity' => 'advanced',
        'title' => 'dvourozměrné koordináty'),
      '3d_coords' => array(
        'severity' => 'advanced',
        'title' => 'třírozměrné koordináty'),
      'platform' => array(
        'severity' => 'basic',
        'title' => 'Platforma', 'mandatory' => true)
    );

    /**
     * URL dokumentace objektu
     * @var string
     */
    public $documentationLink = 'http://docs.icinga.org/latest/en/objectdefinitions.html#objectdefinitions-host';

    /**
     * Vrací mazací tlačítko
     *
     * @param  string                     $name
     * @param  string                     $urlAdd Předávaná část URL
     * @return \EaseJQConfirmedLinkButton
     */
    public function deleteButton($name = null, $addUrl = '')
    {
        return parent::deleteButton(_('Hosta'), $addUrl);
    }

    /**
     * Smaže záznam
     */
    public function delete($id = null)
    {
        if (!is_null($id)) {
            $this->loadFromMySQL($id);
        }

        $hostGroup = new IEHostgroup();
        $hostGroup->deleteHost($this->getName());

        $delAll = true;
        $service = new IEService();
        $servicesAssigned = $service->myDbLink->queryToArray('SELECT ' . $service->myKeyColumn . ',' . $service->nameColumn . ' FROM ' . $service->myTable . ' WHERE ' . 'host_name' . ' LIKE \'%"' . $this->getName() . '"%\'', $service->myKeyColumn);
        foreach ($servicesAssigned as $ServiceID => $ServiceInfo) {
            $service->loadFromMySQL($ServiceID);
            $service->delHostName($this->getId(), $this->getName());
            if (!$service->saveToMySQL()) {
                $this->addStatusMessage(sprintf(_('Nepodařilo se odregistrovat %s ze služby %s'), $this->getName(), $service->getName()), 'Error');
                $delAll = false;
            }
        }

        $childsOfMe = $this->myDbLink->queryToArray('SELECT ' . $this->myKeyColumn . ',' . $this->nameColumn . ' FROM ' . $this->myTable . ' WHERE parents ' .
            ' LIKE \'%' . $this->getName() . '%\'', $this->myKeyColumn);

        foreach ($childsOfMe as $chid_id => $child_info) {
            $child = new IEHost($chid_id);

            if ($child->delMember('parents', $this->getId(), $this->getName()) && $child->saveToMySQL()) {
                $this->addStatusMessage(sprintf(_('%s již není rodičem %s'), $this->getName(), $child->getName()), 'success');
            } else {
                $this->addStatusMessage(sprintf(_('%s je stále rodičem %s'), $this->getName(), $child->getName()), 'warning');
            }
        }


        if ($delAll) {
            return parent::delete();
        }

        return false;
    }

    /**
     * Zkontroluje všechny položky
     *
     * @param  array $allData
     * @return array
     */
    public function controlAllData($allData)
    {
        foreach ($allData as $aDkey => $aD) {
            if ($allData[$aDkey]['max_check_attempts'] == 0) {
                unset($allData[$aDkey]['max_check_attempts']);
            }
        }

        return parent::controlAllData($allData);
    }

    /**
     * Vrací všechna data
     *
     * @return array Data hostu k uložení do konfiguráků
     */
    public function getAllData()
    {
        $allData = parent::getAllData();
        foreach ($allData as $hostID => $hostInfo) {
            if (!intval($hostInfo['register'])) {
                continue;
            }
            if (intval($hostInfo['user_id'])) {
                if (is_object($this->owner)) {
                    if ($this->owner->getUserID() != $hostInfo['user_id']) {
                        $this->owner->loadFromMySQL((int) $hostInfo['user_id']);
                    }
                } else {
                    $this->owner = new IEUser((int) $hostInfo['user_id']);
                }

                $hostOwnerLogin = $this->owner->getUserLogin();
                /* Každý host musí mít jak kontakt login uživatele který ho má vidět */
                if (is_array($hostInfo['contacts'])) {
                    if (array_search($hostOwnerLogin, $hostInfo['contacts']) === false) {
                        $allData[$hostID]['contacts'][] = $hostOwnerLogin;
                    }
                } else {
                    $allData[$hostID]['contacts'] = array($hostOwnerLogin);
                }
            } else {
                $this->addStatusMessage(_('Host bez vlastníka') . ': #' . $hostInfo[$this->myKeyColumn] . ': ' . $hostInfo[$this->nameColumn], 'warning');
            }
        }

        return $allData;
    }

    /**
     * Začne sledovat právě běžící TCP služby
     * @return int počet sledovaných
     */
    public function autoPopulateServices()
    {
        $scanner = new IEPortScanner($this);

        return $scanner->assignServices();
    }

    /**
     * Přejmenuje hosta a závistlosti
     * @param type $newname
     */
    public function rename($newname)
    {
        $oldname = $this->getName();
        $this->setDataValue($this->nameColumn, $newname);

        $hostGroup = new IEHostgroup();
        $hostGroup->renameHost($oldname, $newname);

        $renameAll = true;
        $service = new IEService();
        $servicesAssigned = $service->myDbLink->queryToArray('SELECT ' . $service->myKeyColumn . ',' . $service->nameColumn . ' FROM ' . $service->myTable . ' WHERE ' . 'host_name' . ' LIKE \'%"' . $oldname . '"%\'', $service->myKeyColumn);
        foreach ($servicesAssigned as $serviceID => $serviceInfo) {
            $service->loadFromMySQL($serviceID);
            $service->renameHostName($this->getId(), $newname);
            if (!$service->saveToMySQL()) {
                $this->addStatusMessage(sprintf(_('Nepodařilo se přejmenovat %s ve službě %s'), $this->getName(), $service->getName()), $Type);
                $renameAll = false;
            }
        }

        $childsAssigned = $this->myDbLink->queryToArray('SELECT ' . $this->myKeyColumn . ',' . $this->nameColumn . ' FROM ' . $this->myTable . ' WHERE ' . 'parents' . ' LIKE \'%"' . $oldname . '"%\'', $this->myKeyColumn);
        foreach ($childsAssigned as $chid_id => $child_info) {
            $child = new IEHost($chid_id);
            $child->delMember('parents', $this->getId(), $oldname);
            $child->addMember('parents', $this->getId(), $newname);
            $child->updateToMySQL();
        }

        if ($this->save() && $renameAll) {
            return true;
        }

        return false;
    }

    /**
     * Zjistí ikonu, stahne jí z netu, zkonvertuje a použije jako ikonu hosta
     */
    public function favToIcon()
    {
        $icoUrl = false;
        $baseUrl = 'http://' . $this->getDataValue('host_name') . '/';
        $indexpage = @file_get_contents($baseUrl);
        $icoUrls = array();
        if (strlen($indexpage)) {
            $dom = new DOMDocument();
            @$dom->loadHTML($indexpage);
            $links = $dom->getElementsByTagName('link');
            foreach ($links as $link) {
                $urlLink = false;
                if (isset($link->attributes)) {
                    foreach ($link->attributes as $atribut) {
                        if (isset($atribut->name)) {
                            if (($atribut->name == 'rel') && stristr($atribut->value, 'icon')) {
                                $urlLink = true;
                                $rel = $atribut->value;
                            }
                            if (($atribut->name == 'href')) {
                                $url = $atribut->value;
                            }
                        }
                    }
                    if ($urlLink) {
                        if (strstr($url, '://')) {
                            $icoUrls[$rel] = $url;
                        } else {
                            $icoUrls[$rel] = $baseUrl . $url;
                        }
                    }
                }
            }
        }

        if (!count($icoUrls)) {
            $icoUrls[] = $baseUrl . '/favicon.ico';
        } else {
            if (count($icoUrls) == 1) {
                $icoUrl = current($icoUrls);
            } else {
                foreach ($icoUrls as $ico) {
                    if (strstr($ico, '.png')) {
                        $icoUrl = $ico;
                    }
                }

                if (!$icoUrl) {
                    foreach ($icoUrls as $ico) {
                        if (strstr($ico, '.gif')) {
                            $icoUrl = $ico;
                        }
                    }
                }

                if (!$icoUrl) {
                    foreach ($icoUrls as $ico) {
                        if (strstr($ico, '.jpg')) {
                            $icoUrl = $ico;
                        }
                    }
                }

                if (!$icoUrl) {
                    $icoUrl = current($icoUrls);
                }
            }
        }



        $tmpfilename = sys_get_temp_dir() . '/' . EaseSand::randomString();


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $icoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $downloaded = curl_exec($ch);
        curl_close($ch);

        file_put_contents($tmpfilename, $downloaded);



        if (IEIconSelector::imageTypeOK($tmpfilename)) {

            EaseShared::webPage()->addStatusMessage(sprintf(_('Nalezena ikona %s'), $icoUrl), 'success');

            $newicon = IEIconSelector::saveIcon($tmpfilename, $this);

            if ($newicon) {
                $this->setDataValue('icon_image', $newicon);
                $this->setDataValue('statusmap_image', $newicon);
                return true;
            }
        } else {
            unlink($tmpfilename);
        }

        return false;
    }

    function draw()
    {
        echo new IEHostIcon($this);
    }

    /**
     * Vrací služby přiřazené ke sledování hosta
     *
     * @return array Seznam služeb
     */
    public function getServices()
    {
        $services = array();

        $service = new IEService;
        $servicesAssigned = $service->myDbLink->queryToArray('SELECT ' . $service->myKeyColumn . ',' . $service->nameColumn . ' FROM ' . $service->myTable . ' WHERE host_name LIKE \'%"' . $this->getName() . '"%\'', $service->myKeyColumn);
        if ($servicesAssigned) {
            foreach ($servicesAssigned as $service_id => $service_info) {
                $services[$service_id] = $service_info[$service->nameColumn];
            }
        }
        return $services;
    }

    public function getInfoBlock()
    {
        $block = parent::getInfoBlock();
        $block->addDef(_('Alias'), array(new IEHostIcon($this), $this->getDataValue('alias')));
        $block->addDef(_('Platforma'), new IEPlatformIcon($this->getDataValue('platform')));

        $parents = $this->getDataValue('parents');
        if ($parents) {
            foreach ($parents as $pId => $pName) {
                $parents[$pId] = '<a href="host.php?host_id=' . $pId . '">' . $pName . '</a>';
            }
            $block->addDef(_('Rodiče'), implode(',', $parents));
        }

        $block->addDef(_('Konfigurace senzoru'), $this->sensorStatusLabel());

        return $block;
    }

    /**
     * Vrací label se statusem registrace statusu
     *
     * @param int $status_code Kód nasazení senzoru 2: aktuální 1: zastaralý 0:nenasazeno
     * @return \EaseTWBLabel
     */
    function sensorStatusLabel($status_code = null)
    {

        $status = null;
        if (is_null($status_code)) {
            $status_code = $this->getSensorStatus();
        }

        switch ($status_code) {
            case 2:
                $status = new EaseTWBLabel('success', _('Aktuální'));
                break;
            case 1:
                $status = new EaseTWBLabel('warning', _('Zastaralá'));
                break;
            case 0:
            default :
                $status = new EaseTWBLabel('danger', _('Nenasazeno'));
                break;
        }
        return $status;
    }

    /**
     * Vrací status nasazení senzoru
     *
     * @return int 2: aktuální 1: zastaralý 0:nenasazeno
     */
    function getSensorStatus()
    {
        $status = null;
        $hash = $this->getDataValue('config_hash');
        if ($hash) {
            if ($this->getConfigHash() == $hash) {
                $status = 2;
            } else {
//Zastaralá konfigurace
                $status = 1;
            }
        } else {
//senzor neregistrován
            $status = 0;
        }
        return $status;
    }

    /**
     * Vrací hash vypočítaný z aktuální konfigurace hosta
     */
    function getConfigHash()
    {
        $configuration = array();
        $service = new IEService;
        $servicesAssigned = $service->myDbLink->queryToArray('SELECT `' . $service->getmyKeyColumn() . '` FROM ' . $service->myTable . ' WHERE host_name LIKE \'%"' . $this->getName() . '"%\'', $service->myKeyColumn);
        foreach ($servicesAssigned as $serviceAssigned) {
            $service->loadFromSQL((int) $serviceAssigned[$service->myKeyColumn]);
            $service->unsetDataValue('display_name'); //Položky které se mohou měnit bez nutnosti aktualizovat senzor
            $service->unsetDataValue('service_description');
            $service->unsetDataValue('host_name');
            $service->unsetDataValue('hostgroup_name');
            $service->unsetDataValue('notes');
            $service->unsetDataValue('notes_url');
            $service->unsetDataValue('action_url');
            $service->unsetDataValue('icon_image');
            $service->unsetDataValue('icon_image_alt');
            $service->unsetDataValue('public');
            $service->unsetDataValue('user_id');
            $service->unsetDataValue($service->myLastModifiedColumn);
            $configuration[] = $service->getEffectiveCfg();
        }
        return hash('md5', $this->getName() . serialize($configuration));
    }

    /**
     * Přidá hosta služby
     *
     * @param string $column     název sloupce
     * @param int    $memberID
     * @param string $memberName
     */
    function addMember($column, $memberID, $memberName)
    {
        if ($column == 'parents') {
            if ($memberName == $this->getName()) {
                $this->addStatusMessage(_('Host nemůže být rodičem sebe sama'), 'warning');
                return null;
            }
        }
        return parent::addMember($column, $memberID, $memberName);
    }

    /**
     * Vytvoří nového hosta patřícího rovnou do výchozí skupiny uživatele
     *
     * @param array $data asiciativní pole dat
     *
     * @return int|null id nově vloženého řádku nebo null, pokud se data
     * nepovede vložit
     */
    public function insertToMySQL($data = null)
    {
        if (!is_null($data)) {
            $this->takeData($data);
        }
        $hostgroup = new IEHostgroup($this->user->getUserLogin());
        $this->addMember('hostgroups', $hostgroup->getId(), $hostgroup->getName());
        return parent::insertToMySQL();
    }

}
