<?php
namespace PoP\Engine;

interface DataloadQueryArgsFilter
{
    public function getValue(array $module);
    public function filterDataloadQueryArgs(array &$query, array $module, $value);
}
