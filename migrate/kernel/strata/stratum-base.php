<?php
namespace PoP\Engine;

abstract class StratumBase
{
    public function __construct()
    {
        $stratummanager = StratumManagerFactory::getInstance();
        $stratummanager->add($this->getStratum(), $this->getStrata());
    }

    abstract public function getStratum();
    abstract public function getStrata();
}
