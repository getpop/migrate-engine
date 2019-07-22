<?php
namespace PoP\Engine;
use PoP\Engine\Utils;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Engine\Facades\InstanceManagerFacade;

abstract class FieldValueResolverBase
{
    abstract public function getId($resultitem);
    
    public function getValue($resultitem, $field)
    {
        switch ($fieldName = Utils::getFieldName($field)) {
            case 'id':
                return $this->getId($resultitem);
        }

        // Needed for compatibility with Dataloader_ConvertiblePostList
        // (So that data-fields aimed for another post_type are not retrieved)
        return new \PoP\Engine\Error('no-field');
    }
    
    public function getFieldDefaultDataloaderClass($field)
    {
        return null;
    }

    public function getExtensionValue($fieldValueResolverClass, $resultitem, $field)
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        // Check if there's a hook to implement this field
        // Important: do array_reverse to enable more specific hooks, which are initialized later on in the project, to take priority
        foreach (array_reverse($attachableExtensionManager->getExtensionClasses($fieldValueResolverClass)) as $extensionClass) {
            $instance = $instanceManager->getInstance($extensionClass);
            // Also send the fieldValueResolver along, as to get the id of the $resultitem being passed
            $value = $instance->getValue($resultitem, $field, $this);
            if (!\PoP\Engine\GeneralUtils::isError($value)) {
                return $value;
            }
        }

        return new \PoP\Engine\Error('no-fieldValueResolver-hook');
    }

    public function getExtensionFieldDefaultDataloaderClass($fieldValueResolverClass, $field)
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        foreach (array_reverse($attachableExtensionManager->getExtensionClasses($fieldValueResolverClass)) as $extensionClass) {
            $instance = $instanceManager->getInstance($extensionClass);
            $value = $instance->getFieldDefaultDataloaderClass($field, $this);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}
