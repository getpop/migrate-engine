<?php
namespace PoP\Engine;
use PoP\ComponentModel\GD_FormInput;

class GD_FormInput_MultiInput extends GD_FormInput
{
    public function isMultiple()
    {
        return true;
    }
}
