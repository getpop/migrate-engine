<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

trait DataloadModuleProcessorBaseTrait
{
    use FormattableModuleTrait;
    
    public function getSubmodules(array $module)
    {
        $ret = parent::getSubmodules($module);

        if ($filter_module = $this->getFilterSubmodule($module)) {
            $ret[] = $filter_module;
        }

        if ($inners = $this->getInnerSubmodules($module)) {
            $ret = array_merge(
                $ret,
                $inners
            );
        }
                
        return $ret;
    }

    protected function getInnerSubmodules(array $module)
    {
        return array();
    }

    public function getFilterSubmodule(array $module)
    {
        return null;
    }

    public function metaInitProps(array $module, array &$props)
    {
        /**
         * Allow to add more stuff
         */
        HooksAPIFacade::getInstance()->doAction(
            '\PoP\Engine\DataloadModuleProcessorBaseTrait:initModelProps',
            array(&$props),
            $module,
            $this
        );
    }

    // public function getModelPropsForDescendantDatasetmodules(array $module, array &$props)
    // {
    //     $ret = parent::getModelPropsForDescendantDatasetmodules($module, $props);

    //     if ($filter_module = $this->getFilterSubmodule($module)) {
    //         $ret['filter-module'] = $filter_module;
    //     }
    //     // if ($filter = $this->getFilter($module)) {
    //     //     $ret['filter'] = $filter;
    //     // }

    //     return $ret;
    // }

    public function initModelProps(array $module, array &$props)
    {
        $this->metaInitProps($module, $props);
        parent::initModelProps($module, $props);
    }

    //-------------------------------------------------
    // PROTECTED Functions
    //-------------------------------------------------

    public function getDataloaderClass(array $module)
    {
        return \PoP\Engine\Dataloader_Nil::class;
    }
}
