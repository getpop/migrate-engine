<?php
namespace PoP\Engine;
use PoP\Engine\Facades\InstanceManagerFacade;

abstract class FieldValueResolverBase
{
    abstract public function getId($resultitem);
    abstract public function getIdFieldDataloaderClass();

    public function getValue($resultitem, string $fieldName, array $fieldArgs = [])
    {
        switch ($fieldName) {
            case 'id':
                return $this->getId($resultitem);
        }

        return $this->getValueFromUnits($resultitem, $fieldName, $fieldArgs);
    }

    protected function getValueFromUnits($resultitem, string $fieldName, array $fieldArgs = [])
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        // Iterate classes from the current class towards the parent classes until finding fieldValueResolver that satisfies processing this field
        $class = get_called_class();
        do {
            // Important: do array_reverse to enable more specific hooks, which are initialized later on in the project, to take priority
            foreach (array_reverse($attachableExtensionManager->getExtensionClasses($class)) as $extensionClass) {
                $instance = $instanceManager->getInstance($extensionClass);
                // Also send the fieldValueResolver along, as to get the id of the $resultitem being passed
                $value = $instance->getValue($this, $resultitem, $fieldName, $fieldArgs);
                if (!\PoP\Engine\GeneralUtils::isError($value)) {
                    return $value;
                }
            }
        } while ($class = get_parent_class($class));

        // Needed for compatibility with Dataloader_ConvertiblePostList
        // (So that data-fields aimed for another post_type are not retrieved)
        return new \PoP\Engine\Error('no-field:'.$fieldName);
    }

    public function getFieldDefaultDataloaderClass(string $fieldName, array $fieldArgs = [])
    {
        switch ($fieldName) {
            case 'id':
                return $this->getIdFieldDataloaderClass();
        }

        return $this->getFieldDefaultDataloaderClassFromUnits($fieldName, $fieldArgs);
    }

    protected function getFieldDefaultDataloaderClassFromUnits(string $fieldName, array $fieldArgs = [])
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();

        $class = get_called_class();
        do {
            foreach (array_reverse($attachableExtensionManager->getExtensionClasses($class)) as $extensionClass) {
                $instance = $instanceManager->getInstance($extensionClass);
                $value = $instance->getFieldDefaultDataloaderClass($this, $fieldName, $fieldArgs);
                if ($value) {
                    return $value;
                }
            }
        } while ($class = get_parent_class($class));

        return null;
    }
}
