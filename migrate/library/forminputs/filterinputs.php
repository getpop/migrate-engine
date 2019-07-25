<?php

class PoP_Module_Processor_FilterInputs extends \PoP\Engine\AbstractFormInputs implements \PoP\Engine\DataloadQueryArgsFilter
{
    public const MODULE_FILTERINPUT_ORDER = 'filterinput-order';

    public function getModulesToProcess()
    {
        return array(
            [self::class, self::MODULE_FILTERINPUT_ORDER],
        );
    }

    public function filterDataloadQueryArgs(array &$query, array $module, $value)
    {
        switch ($module[1]) {
            case self::MODULE_FILTERINPUT_ORDER:
                $query['orderby'] = $value['orderby'];
                $query['order'] = $value['order'];
                break;
        }
    }

    public function getInputClass(array $module)
    {
        switch ($module[1]) {
            case self::MODULE_FILTERINPUT_ORDER:
                return \PoP\Engine\GD_FormInput_Order::class;
        }
        
        return parent::getInputClass($module);
    }

    public function getName(array $module)
    {
        switch ($module[1]) {
            case self::MODULE_FILTERINPUT_ORDER:
                // Add a nice name, so that the URL params when filtering make sense
                $names = array(
                    self::MODULE_FILTERINPUT_ORDER => 'order',
                );
                return $names[$module[1]];
        }

        return parent::getName($module);
    }
}



