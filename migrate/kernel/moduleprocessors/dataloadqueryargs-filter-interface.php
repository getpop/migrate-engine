<?php
namespace PoP\Engine;

interface DataloadQueryArgsFilter
{
    public function getValue(array $module, ?array $source = null);
    public function getFilterInput(array $module): ?array;
}
