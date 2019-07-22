<?php
use PoP\Hooks\Facades\HooksAPIFacade;

function popLoaded()
{
    return defined('POP_STARTUP_INITIALIZED') && POP_STARTUP_INITIALIZED;
}
