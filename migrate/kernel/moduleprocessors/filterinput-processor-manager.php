<?php
namespace PoP\Engine;
use PoP\Engine\ItemProcessorManagerTrait;

class PoP_FilterInputProcessorManager {

	use ItemProcessorManagerTrait;
}

/**
 * Initialization
 */
global $pop_filterinputprocessor_manager;
$pop_filterinputprocessor_manager = new PoP_FilterInputProcessorManager();
