<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldValueResolvers\AbstractDBDataFieldValueResolver;
use PoP\ComponentModel\FieldResolvers\AbstractFieldResolver;

abstract class AbstractOperatorsFieldValueResolver extends AbstractDBDataFieldValueResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
        	AbstractFieldResolver::class,
        ];
    }
}
