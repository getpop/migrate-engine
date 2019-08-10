<?php
namespace PoP\Engine;
use PoP\Engine\Facades\InstanceManagerFacade;

abstract class FieldValueResolverBase
{
    abstract public function getId($resultitem);

    public function getValue($resultitem, string $fieldName, array $fieldAtts = [])
    {
        switch ($fieldName) {
            case 'id':
                return $this->getId($resultitem);
        }

        // Needed for compatibility with Dataloader_ConvertiblePostList
        // (So that data-fields aimed for another post_type are not retrieved)
        return new \PoP\Engine\Error('no-field');
    }

    public function getFieldDefaultDataloaderClass(string $fieldName, array $fieldAtts = [])
    {
        return null;
    }

    public function getExtensionValue(string $fieldValueResolverClass, $resultitem, string $fieldName, array $fieldAtts = [])
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        // Check if there's a hook to implement this field
        // Important: do array_reverse to enable more specific hooks, which are initialized later on in the project, to take priority
        foreach (array_reverse($attachableExtensionManager->getExtensionClasses($fieldValueResolverClass)) as $extensionClass) {
            $instance = $instanceManager->getInstance($extensionClass);
            // Also send the fieldValueResolver along, as to get the id of the $resultitem being passed
            $value = $instance->getValue($this, $resultitem, $fieldName, $fieldAtts);
            if (!\PoP\Engine\GeneralUtils::isError($value)) {
                return $value;
            }
        }

        return new \PoP\Engine\Error('no-fieldValueResolver-hook');
    }

    public function getExtensionFieldDefaultDataloaderClass(string $fieldValueResolverClass, string $fieldName, array $fieldAtts = [])
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        foreach (array_reverse($attachableExtensionManager->getExtensionClasses($fieldValueResolverClass)) as $extensionClass) {
            $instance = $instanceManager->getInstance($extensionClass);
            $value = $instance->getFieldDefaultDataloaderClass($this, $fieldName, $fieldAtts);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}
