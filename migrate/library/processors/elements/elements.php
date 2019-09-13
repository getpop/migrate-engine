<?php

class PoP_Engine_Module_Processor_Elements extends \PoP\ComponentModel\ModuleProcessorBase
{
    public const MODULE_EMPTY = 'empty';

    public function getModulesToProcess(): array
    {
        return array(
            [self::class, self::MODULE_EMPTY],
        );
    }
}
