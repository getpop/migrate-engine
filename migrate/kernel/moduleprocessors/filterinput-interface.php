<?php
namespace PoP\Engine;

interface FilterInput
{
    public function filterDataloadQueryArgs(array $filterInput, array &$query, $value);
}
