<?php
/**
 * Formulář testu IMCP odezvy
 *
 * @package    IcingaEditor
 * @subpackage plugins
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2014 Vitex@hippy.cz (G)
 */
require_once 'classes/IEServiceConfigurator.php';

/**
 * Description of ping
 *
 * @author vitex
 */
class ping extends IEServiceConfigurator
{

    /**
     *
     */
    public function form()
    {
        $warningValues = explode(',', $this->commandParams[0]);
        $criticalValues = explode(',', $this->commandParams[1]);

        $this->form->addItem(new EaseTWBFormGroup(_('prodleva varování'), new EaseHtmlInputTextTag('wt', $warningValues[0]), '100.0', _('Čas v milisekundách, po jehož překročení při testu bude hlášeno varování')));
        $this->form->addItem(new EaseTWBFormGroup(_('ztráta varování'), new EaseHtmlInputTextTag('wp', $warningValues[1]), '20 %', _('Procento ztracených paketů, po jehož překročení při testu bude hlášeno varování')));

        $this->form->addItem(new EaseTWBFormGroup(_('prodleva kritické chyby'), new EaseHtmlInputTextTag('ct', $criticalValues[0]), '500.0', _('Čas v milisekundách, po jehož překročení při testu bude hlášena kritická chyba')));
        $this->form->addItem(new EaseTWBFormGroup(_('ztráta kritické chyby'), new EaseHtmlInputTextTag('cp', $criticalValues[1]), '60 %', _('Procento ztracených paketů, po jehož překročení při testu bude hlášena kritická chyba')));
    }

    /**
     * Zpracování formuláře
     * 
     * @return boolean
     */
    public function reconfigureService()
    {
        $page = EaseShared::webPage();
        $wt = $page->getRequestValue('wt', 'float');
        $ct = $page->getRequestValue('ct', 'float');
        $wp = str_replace('%', '', $page->getRequestValue('wp'));
        $cp = str_replace('%', '', $page->getRequestValue('cp'));

        if ($wt && $ct && $wp && $cp) {

            $command = $wt . ',' . $wp . '%!' . $ct . ',' . $cp . '%';

            $this->tweaker->service->setDataValue('check_command-params', $command);

            return parent::reconfigureService();
        }

        return FALSE;
    }

}
