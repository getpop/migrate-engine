<?php
namespace PoP\Engine;

abstract class CheckpointProcessorBase
{
    public function __construct()
    {
    }

    abstract public function getCheckpointsToProcess();

    public function process(array $checkpoint)
    {
        // By default, no problem at all, so always return true
        return true;
    }
}
