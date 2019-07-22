<?php
namespace PoP\Engine;

class ModuleProcessorManager
{
    use ItemProcessorManagerTrait;

    public function __construct()
    {
        ModuleProcessorManagerFactory::setInstance($this);
    }
}

/**
 * Initialization
 */
new ModuleProcessorManager();
