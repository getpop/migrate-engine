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

    public function getHost(): string
    {
        return removeScheme($this->getHomeURL());
    }

    public function isError($object): bool
    {
    	return $object instanceof Throwable;
    }

    public function getDate($format, $date) {
        return date($format, strtotime($date));
    }
}
