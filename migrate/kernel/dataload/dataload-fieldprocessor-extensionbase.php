<?php
namespace PoP\Engine;

abstract class AbstractFieldValueResolverExtension
{
	/**
	 * This class is attached to a FieldValueResolver
	 */
	use AttachableExtensionTrait;

    public function getValue($fieldValueResolver, $resultitem, string $fieldName, array $fieldAtts = [])
    {
        return new \PoP\Engine\Error('no-field');
    }

    public function getFieldDefaultDataloaderClass($fieldValueResolver, string $fieldName, array $fieldAtts = [])
    {
        return null;
    }
}
