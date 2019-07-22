<?php
namespace PoP\Engine;

abstract class ConvertibleFieldValueResolverResolverBase
{
    abstract public function getFieldValueResolverClass();

    public function process($resultitem)
    {
        return false;
    }

    public function cast($resultitem)
    {
        return $resultitem;
    }
}
