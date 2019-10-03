<?php
namespace PoP\Engine;

abstract class AbstractOperatorsFieldValueResolver extends \PoP\ComponentModel\AbstractDBDataFieldValueResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(\PoP\ComponentModel\FieldResolverBase::class);
    }
}
