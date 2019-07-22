<?php
namespace PoP\Engine;
use PoP\Engine\Facades\InstanceManagerFacade;

class DataloadUtils
{
    public static function getDefaultDataloaderNameFromSubcomponentDataField($dataloader_object_or_class, $subcomponent_data_field)
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        if (is_object($dataloader_object_or_class)) {
            $dataloader = $dataloader_object_or_class;
        } else {
            $dataloader_class = $dataloader_object_or_class;
            $dataloader = $instanceManager->getInstance($dataloader_class);
        }

        $fieldValueResolverClass = $dataloader->getFieldValueResolverClass();
        $fieldValueResolver = $instanceManager->getInstance($fieldValueResolverClass);
        $subcomponent_dataloader_class = $fieldValueResolver->getFieldDefaultDataloaderClass($subcomponent_data_field);
        if (!$subcomponent_dataloader_class && \PoP\Engine\Server\Utils::failIfSubcomponentDataloaderUndefined()) {
            throw new \Exception(sprintf('There is no default dataloader set for field  "%s" from dataloader "%s" and fieldValueResolver "%s" (%s)', $subcomponent_data_field, $dataloader_class, $fieldValueResolverClass, fullUrl()));
        }

        return $subcomponent_dataloader_class;
    }
    
    public static function addFilterParams($url, $moduleValues = array())
    {
        $moduleprocessor_manager = ModuleProcessorManagerFactory::getInstance();
        $args = [];
        foreach ($moduleValues as $moduleValue) {
            $module = $moduleValue['module'];
            $value = $moduleValue['value'];
            $moduleprocessor = $moduleprocessor_manager->getProcessor($module);
            $args[$moduleprocessor->getName($module)] = $value;
        }
        $cmsenginehelpers = \PoP\Engine\HelperAPIFactory::getInstance();
        return $cmsenginehelpers->addQueryArgs($args, $url);
    }
}
