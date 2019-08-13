<?php
namespace PoP\Engine;

abstract class AbstractFilterInputProcessor implements FilterInput {

	public function getFilterInputsToProcess() {

		return array();
	}
}
