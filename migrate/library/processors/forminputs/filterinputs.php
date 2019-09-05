<?php
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

    public function getFilterDocumentationType(array $module): ?string
    {
        $types = [
            self::MODULE_FILTERINPUT_ORDER => TYPE_STRING,
            self::MODULE_FILTERINPUT_LIMIT => TYPE_INT,
            self::MODULE_FILTERINPUT_OFFSET => TYPE_INT,
        ];
        return $types[$module[1]];
    }

    public function getFilterDocumentationDescription(array $module): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            self::MODULE_FILTERINPUT_ORDER => $translationAPI->__('Order the results. Specify the \'orderby\' and \'order\' (\'ASC\' or \'DESC\') fields in this format: \'orderby|order\'', ''),
            self::MODULE_FILTERINPUT_LIMIT => $translationAPI->__('Limit the results. -1 to bring all results (or the maximum amount allowed)', ''),
            self::MODULE_FILTERINPUT_OFFSET => $translationAPI->__('Offset the results by how many places, needed for pagination', ''),
        ];
        return $descriptions[$module[1]];
    }
}



