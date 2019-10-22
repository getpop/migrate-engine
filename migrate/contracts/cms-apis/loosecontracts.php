<?php
namespace PoP\Engine;

class CMSLooseContracts extends \PoP\LooseContracts\AbstractCMSLooseContracts
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
new CMSLooseContracts();

