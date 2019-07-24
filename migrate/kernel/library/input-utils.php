<?php
namespace PoP\Engine;

class PoP_InputUtils
{
    public static function getMultipleinputsName($name, $subname)
    {
        return $name.'-'.$subname;
    }
}
