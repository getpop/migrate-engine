<?php
namespace PoP\Engine;

class CheckpointProcessorManager
{
    use ItemProcessorManagerTrait;

    public function __construct()
    {
        CheckpointProcessorManagerFactory::setInstance($this);
    }
}

/**
 * Initialization
 */
new CheckpointProcessorManager();
