<?php
namespace PoP\Engine;
use PoP\ComponentModel\GD_FormInput;

class GD_FormInput_MultiValueFromString extends GD_FormInput
{
    private $separator;

    public function __construct($params = array())
    {
        parent::__construct($params);
        $this->separator = $params['separator'] ?? ',';
    }

    public function getValue(?array $source = null)
    {
        return explode($this->separator, parent::getValue($source));
    }
}
