<?php
namespace PoP\Engine;
interface FormComponent
{
    public function getValue(array $module);
    public function getDefaultValue(array $module, array &$props);
    public function getName(array $module);
    public function getInputName(array $module);
    public function isMultiple(array $module);
}