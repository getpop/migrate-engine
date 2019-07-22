<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

abstract class AbstractFieldValueResolverExtension
{
	/**
	 * This class is attached to a FieldValueResolver
	 */ 
	use AttachableExtensionTrait;

    public function getValue($resultitem, $field, $fieldValueResolver)
    {
        return new \PoP\Engine\Error('no-field');
    }

    public function getFieldDefaultDataloaderClass($field, $fieldValueResolver)
    {
        return null;
    }
}
