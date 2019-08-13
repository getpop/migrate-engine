<?php
namespace PoP\Engine;

interface DataloadQueryArgsFilter
{
    public function getValue(array $module, ?array $source = null);
    public function filterDataloadQueryArgs(array &$query, array $module, $value);
}
