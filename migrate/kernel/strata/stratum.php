<?php
define('POP_STRATUM_DATA', 'data');

class GD_Stratum_Data extends \PoP\Platform\Platforms\StratumBase
{
	public function getStratum()
    {
        return POP_STRATUM_DATA;
    }

    public function getStrata() {
    	return [
    		POP_STRATUM_DATA
    	];
    }
}

/**
 * Initialization
 */
new GD_Stratum_Data();
