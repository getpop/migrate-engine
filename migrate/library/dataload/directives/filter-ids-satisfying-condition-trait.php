<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldUtils;

trait FilterIDsSatisfyingConditionTrait
{
    protected function getIdsSatisfyingCondition($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbErrors)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $idsSatisfyingCondition = [];
        foreach (array_keys($idsDataFields) as $id) {
            $resultItem = $resultIDItems[$id];
            // Extract the directiveArguments, to be evaluated on the field as fieldArgs
            list($fieldArgs, $nestedDBErrors) = FieldUtils::extractFieldArgumentsForResultItem($fieldResolver, $resultItem, $this->directive);
            if ($nestedDBErrors) {
                $dbErrors = array_merge(
                    $dbErrors,
                    $nestedDBErrors
                );
            }
            if ($fieldArgs['if']) {
                $idsSatisfyingCondition[] = $id;
            }
        }
        return $idsSatisfyingCondition;
    }
}
