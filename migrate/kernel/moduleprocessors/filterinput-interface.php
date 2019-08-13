<?php
namespace PoP\Engine;

interface FilterInput
{
    public function filterDataloadQueryArgs(array &$query, array $filterInput, $value);
}
