<?php
namespace PoP\Engine;

abstract class FunctionAPI_Base implements FunctionAPI
{
    public function __construct()
    {
        FunctionAPIFactory::setInstance($this);
    }
    public function getVersion()
    {
    	return '';
    }

    public function getDomain(): string
    {
        return getDomain($this->getSiteURL());
    }

    public function isError($object)
    {
    	return $object instanceof Throwable;
    }
}
