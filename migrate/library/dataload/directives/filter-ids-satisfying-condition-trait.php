<?php
namespace PoP\Engine;
use PoP\ComponentModel\FieldUtils;

trait FilterIDsSatisfyingConditionTrait
{
    protected function getIdsSatisfyingCondition($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbErrors, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // First validate schema (eg of error in schema: ?fields=posts<include(if:this-field-doesnt-exist())>)
        list(
            $directiveArgs,
            $directiveSchemaErrors,
            $directiveSchemaWarnings,
            $directiveSchemaDeprecations
        ) = FieldUtils::extractFieldArgumentsForSchema($fieldResolver, $this->directive);
        if ($directiveSchemaErrors || $directiveSchemaWarnings || $directiveSchemaDeprecations) {
            // Save the errors
            $directiveOutputKey = FieldUtils::getFieldOutputKey($this->directive);
            foreach ($directiveSchemaErrors as $error) {
                $schemaErrors[$directiveOutputKey][] = $error;
            }
            foreach ($directiveSchemaWarnings as $error) {
                $schemaWarnings[$directiveOutputKey][] = $error;
            }
            foreach ($directiveSchemaDeprecations as $error) {
                $schemaDeprecations[$directiveOutputKey][] = $error;
            }
            // If there's an error, those args will be removed. Then, re-create the fieldDirective to pass it to the function below
            $directiveName = FieldUtils::getFieldDirectiveName($this->directive);
            $directive = FieldUtils::getFieldDirective($directiveName, $directiveArgs);
        } else {
            $directive = $this->directive;
        }

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
