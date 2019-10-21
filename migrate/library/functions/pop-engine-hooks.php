<?php
namespace PoP\Engine\Impl;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\ComponentModel\Managers\ModuleFilterManager;
use PoP\Site\ModuleProcessors\DataloadingConstants;
use PoP\ComponentModel\GeneralUtils;

class EngineHooks
{
    public function __construct()
    {
        HooksAPIFacade::getInstance()->addAction(
            '\PoP\ComponentModel\Engine:getModuleData:start',
            array($this, 'start'),
            10,
            4
        );
        HooksAPIFacade::getInstance()->addAction(
            '\PoP\ComponentModel\Engine:getModuleData:dataloading-module',
            array($this, 'calculateDataloadingModuleData'),
            10,
            8
        );
        HooksAPIFacade::getInstance()->addAction(
            '\PoP\ComponentModel\Engine:getModuleData:end',
            array($this, 'end'),
            10,
            5
        );
    }

    public function start($root_module, $root_model_props_in_array, $root_props_in_array, $helperCalculations_in_array)
    {
        $helperCalculations = &$helperCalculations_in_array[0];
        $helperCalculations['has-lazy-load'] = false;
    }

    public function calculateDataloadingModuleData(array $module, $module_props_in_array, $data_properties_in_array, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs, $helperCalculations_in_array)
    {
        $data_properties = &$data_properties_in_array[0];

        if ($data_properties[DataloadingConstants::LAZYLOAD]) {
            $helperCalculations = &$helperCalculations_in_array[0];
            $helperCalculations['has-lazy-load'] = true;
        }
    }

    public function end($root_module, $root_model_props_in_array, $root_props_in_array, $helperCalculations_in_array, $engine)
    {
        $helperCalculations = &$helperCalculations_in_array[0];

        // Fetch the lazy-loaded data using the Background URL load
        if ($helperCalculations['has-lazy-load']) {
                $url = GeneralUtils::addQueryArgs([
                GD_URLPARAM_DATAOUTPUTITEMS => [
                    GD_URLPARAM_DATAOUTPUTITEMS_META, 
                    GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA, 
                    GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
                ],
                ModuleFilterManager::URLPARAM_MODULEFILTER => \PoP\Engine\ModuleFilters\Lazy::NAME,
                GD_URLPARAM_ACTIONS.'[]' => POP_ACTION_LOADLAZY,
            ], \PoP\ComponentModel\Utils::getCurrentUrl());
            $engine->addBackgroundUrl($url, array(POP_TARGET_MAIN));
        }
    }
}


/**
 * Initialization
 */
new EngineHooks();
