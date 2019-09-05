<?php

use PoP\ComponentModel\FilterInput;
use PoP\Engine\FilterInputProcessor;
use PoP\Translation\Facades\TranslationAPIFacade;

class PoP_Module_Processor_FilterInputs extends \PoP\ComponentModel\AbstractFormInputs implements \PoP\ComponentModel\DataloadQueryArgsFilter
{
    use \PoP\ComponentModel\DataloadQueryArgsFilterTrait;
    
    public const MODULE_FILTERINPUT_ORDER = 'filterinput-order';
    public const MODULE_FILTERINPUT_LIMIT = 'filterinput-limit';
    public const MODULE_FILTERINPUT_OFFSET = 'filterinput-offset';

    public function getModulesToProcess()
    {
        return array(
            [self::class, self::MODULE_FILTERINPUT_ORDER],
            [self::class, self::MODULE_FILTERINPUT_LIMIT],
            [self::class, self::MODULE_FILTERINPUT_OFFSET],
        );
    }

    public function getFilterInput(array $module): ?array
    {
        $filterInputs = [
            self::MODULE_FILTERINPUT_ORDER => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_ORDER],
            self::MODULE_FILTERINPUT_LIMIT => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_LIMIT],
            self::MODULE_FILTERINPUT_OFFSET => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_OFFSET],
        ];
        return $filterInputs[$module[1]];
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
            case self::MODULE_FILTERINPUT_LIMIT:
            case self::MODULE_FILTERINPUT_OFFSET:
                // Add a nice name, so that the URL params when filtering make sense
                $names = array(
                    self::MODULE_FILTERINPUT_ORDER => 'order',
                    self::MODULE_FILTERINPUT_LIMIT => 'limit',
                    self::MODULE_FILTERINPUT_OFFSET => 'offset',
                );
                return $names[$module[1]];
        }

        return parent::getName($module);
    }

    public function addFilterDocumentation(array &$documentation, array $module)
    {
        parent::addFilterDocumentation($documentation, $module);

        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($module[1]) {
            case self::MODULE_FILTERINPUT_ORDER:
                $documentation['description'] = $translationAPI->('', '');
                $documentation['type'] = TYPE_STRING;
                break;

            case self::MODULE_FILTERINPUT_LIMIT:
                $documentation['description'] = $translationAPI->('', '');
                $documentation['type'] = TYPE_INT;
                break;

            case self::MODULE_FILTERINPUT_OFFSET:
                $documentation['description'] = $translationAPI->('', '');
                $documentation['type'] = TYPE_INT;
                break;
        }
    }
}



