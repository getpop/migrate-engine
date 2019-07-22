<?php

class PoP_Engine_Module_Processor_Elements extends \PoP\Engine\ModuleProcessorBase
{
    public const MODULE_EMPTY = 'empty';

    public function getModulesToProcess()
    {
        return array(
            [self::class, self::MODULE_EMPTY],
        );
    }
}
