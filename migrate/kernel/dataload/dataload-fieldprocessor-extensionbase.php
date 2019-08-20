<?php
namespace PoP\Engine;

abstract class AbstractFieldValueResolverUnit
{
	/**
	 * This class is attached to a FieldValueResolver
	 */
	use AttachableExtensionTrait;

    public function getValue($fieldValueResolver, $resultitem, string $fieldName, array $fieldArgs = [])
    {
        return new \PoP\Engine\Error('no-field:'.$fieldName);
    }

    public function getFieldDefaultDataloaderClass($fieldValueResolver, string $fieldName, array $fieldArgs = [])
    {
        return null;
    }
}
