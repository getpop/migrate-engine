<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldUtils;

trait FilterIDsSatisfyingConditionTrait
{
    protected function getIdsSatisfyingCondition($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbErrors)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $idsSatisfyingCondition = [];
        $field = $this->directiveArgs['if-field'];
        $fieldOutputKey = FieldUtils::getFieldOutputKey($field);
        foreach (array_keys($idsDataFields) as $id) {
            $resultItem = $resultIDItems[$id];
            $value = $fieldResolver->resolveValue($resultItem, $field);
            if (\PoP\ComponentModel\GeneralUtils::isError($value)) {
                $error = $value;
                $dbErrors[(string)$id][$fieldOutputKey][] = $error->getErrorMessage();
            } elseif ($value) {
                $idsSatisfyingCondition[] = $id;
            }
        }
        return $idsSatisfyingCondition;
    }
}
