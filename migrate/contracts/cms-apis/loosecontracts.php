<?php
namespace PoP\Engine;
use PoP\LooseContracts\AbstractLooseContractSet;

class CMSLooseContracts extends AbstractLooseContractSet
{
	public function getRequiredHooks() {
		return [
			// Actions
			'popcms:boot',
			'popcms:init',
			'popcms:shutdown',
			'popcms:componentInstalled',
			'popcms:componentUninstalled',
			'popcms:componentInstalledOrUninstalled',
		];
	}

	public function getRequiredNames() {
		return [
			// Options
			'popcms:option:dateFormat',
			'popcms:option:charset',
			'popcms:option:gmtOffset',
			'popcms:option:timezone',
		];
	}
}

/**
 * Initialize
 */
new CMSLooseContracts(\PoP\LooseContracts\Facades\LooseContractManagerFacade::getInstance());

