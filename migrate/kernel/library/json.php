<?php

// Returns true if the response format must be in JSON
function doingJson()
{
	$vars = \PoP\Engine\Engine_Vars::getVars();
	return $vars['output'] == GD_URLPARAM_OUTPUT_JSON;
    // return isset($_REQUEST[GD_URLPARAM_OUTPUT]) && $_REQUEST[GD_URLPARAM_OUTPUT] == GD_URLPARAM_OUTPUT_JSON;
}
