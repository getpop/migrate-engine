<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldUtils;

trait FilterIDsSatisfyingConditionTrait
{
    protected function getIdsSatisfyingCondition($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbErrors, array &$schemaErrors, array &$schemaDeprecations)
    {
        // First validate schema (eg of error in schema: ?fields=posts<include(if:this-field-doesnt-exist())>)
        $directiveArgs = FieldUtils::extractFieldArgumentsForSchema($fieldResolver, $this->directive, $schemaErrors, $schemaDeprecations);
        // If there's an error, those args will be removed. Then, re-create the fieldDirective to pass it to the function below
        $directiveName = FieldUtils::getFieldDirectiveName($this->directive);
        $directive = FieldUtils::getFieldDirective($directiveName, $directiveArgs);

        // Check the condition field. If it is satisfied, then skip those fields
        $idsSatisfyingCondition = [];
        foreach (array_keys($idsDataFields) as $id) {
            $resultItem = $resultIDItems[$id];
            // Extract the directiveArguments, to be evaluated on the field as fieldArgs
            list($fieldArgs, $nestedDBErrors) = FieldUtils::extractFieldArgumentsForResultItem($fieldResolver, $resultItem, $directive);
            if ($nestedDBErrors) {
                foreach ($nestedDBErrors as $id => $fieldOutputKeyErrorMessages) {
                    $dbErrors[$id] = array_merge(
                        $dbErrors[$id] ?? [],
                        $fieldOutputKeyErrorMessages
                    );
                }
            }
            if ($fieldArgs['if']) {
                $idsSatisfyingCondition[] = $id;
            }
        }
        return $idsSatisfyingCondition;
    }
}
