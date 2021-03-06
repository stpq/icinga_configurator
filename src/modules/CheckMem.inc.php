<?php

/**
 * Formulář pro test Disku windows
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
class CheckMem extends IEServiceConfigurator
{

    /**
     *
     */
    public function form()
    {
        $config = array(
          'ShowAll' => null,
          'MaxWarn' => NULL,
          'MaxCrit' => NULL,
          'MinWarn' => NULL,
          'MinCrit' => NULL,
          'warn' => NULL,
          'crit' => NULL,
          'type' => NULL
        );
        foreach (explode(' ', $this->commandParams[0]) as $cfg) {
            if (strstr($cfg, '=')) {
                list($key, $value) = explode('=', $cfg);
                $config[$key] = $value;
            } else {
                if ($cfg == 'ShowAll') {
                    $config[$cfg] = true;
                } else {
                    $config[$cfg] = null;
                }
            }
        }


        $types = array('physical' => _('Physical memory (RAM)'), 'committed' => _('total memory (RAM+PAGE)'));

        $this->form->addInput(new EaseHtmlSelect('type', $types, str_replace(':', '', $config['type'])), _('Typ'), '', _('Typ sledované paměti'));

        $this->form->addItem(new EaseTWBFormGroup(_('MaxWarn'), new EaseHtmlInputTextTag('MaxWarn', $config['MaxWarn']), '80%', _('Maximum value before a warning is returned.')));
        $this->form->addItem(new EaseTWBFormGroup(_('MaxCrit'), new EaseHtmlInputTextTag('MaxCrit', $config['MaxCrit']), '95%', _('Maximum value before a critical is returned.')));
        $this->form->addItem(new EaseTWBFormGroup(_('MinWarn'), new EaseHtmlInputTextTag('MinWarn', $config['MinWarn']), '10%', _('Minimum value before a warning is returned.')));
        $this->form->addItem(new EaseTWBFormGroup(_('MinCrit'), new EaseHtmlInputTextTag('MinCrit', $config['MinCrit']), '5%', _('Minimum value before a critical is returned.')));

        $this->form->addItem(new EaseTWBFormGroup(_('warn'), new EaseHtmlInputTextTag('warn', $config['warn']), '5%', _('Maximum value before a warning is returned.')));
        $this->form->addItem(new EaseTWBFormGroup(_('crit'), new EaseHtmlInputTextTag('crit', $config['crit']), '5%', _('Maximum value before a critcal is returned.')));


        $this->form->addInput(new EaseTWBSwitch('ShowAll', $config['ShowAll']), _('Zobrazit vše'), null, _('Configures display format (if set shows all items not only failures, if set to long shows all cores).'));

        //    $this->form->addInput(new EaseHtmlInputTextTag('orig', $this->commandParams[0], array('disabled')));
    }

    /**
     * Zpracování formuláře
     *
     * @return boolean
     */
    public function reconfigureService()
    {
        $config = array();
        $page = EaseShared::webPage();

        foreach ($page->getRequestValues() as $key => $value) {
            switch ($key) {
                case 'ShowAll':
                    if ($value) {
                        $config[] = 'ShowAll';
                    }
                    break;
                case 'MaxWarn':
                case 'MaxCrit':
                case 'MinWarn':
                case 'MinCrit':
                case 'warn':
                case 'crit':
                case 'type':
                    if ($value) {
                        $config[] = $key . '=' . $value;
                    }
                    break;

                default:
                    break;
            }
        }



        if (count($config)) {

            $this->tweaker->service->setDataValue('check_command-params', implode(' ', $config));

            return parent::reconfigureService();
        }

        return FALSE;
    }

}
