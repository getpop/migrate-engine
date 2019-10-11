<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldValueResolvers\AbstractDBDataFieldValueResolver;

abstract class AbstractOperatorsFieldValueResolver extends AbstractDBDataFieldValueResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(\PoP\ComponentModel\FieldResolverBase::class);
    }
}
