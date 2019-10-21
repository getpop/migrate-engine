<?php
namespace PoP\Engine;

abstract class HelperAPI_Base implements HelperAPI
{
    public function __construct()
    {
        HelperAPIFactory::setInstance($this);
    }

    public function addQueryArgs(array $key_values, string $url): string
    {
        return GeneralUtils::addQueryArgs($key_values, $url);
    }
}
