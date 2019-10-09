<?php
namespace PoP\Engine;

use PoP\ComponentModel\Schema\DirectiveValidatorTrait;

trait FilterIDsSatisfyingConditionTrait
{
    use DirectiveValidatorTrait;

    protected function getIdsSatisfyingCondition($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // First validate schema (eg of error in schema: ?fields=posts<include(if:this-field-doesnt-exist())>)
        list(
            $directive
        ) = $this->validateDirectiveForSchema($fieldResolver, $this->directive, $schemaErrors, $schemaWarnings, $schemaDeprecations);

        // Check the condition field. If it is satisfied, then skip those fields
        $idsSatisfyingCondition = [];
        foreach (array_keys($idsDataFields) as $id) {
            // Validate directive args for the resultItem
            $resultItem = $resultIDItems[$id];
            list(
                $resultItemDirective,
                $resultItemDirectiveArgs
            ) = $this->validateDirectiveForResultItem($fieldResolver, $resultItem, $directive, $dbErrors, $dbWarnings);
            // $resultItemDirectiveArgs has all the right directiveArgs values. Now we can evaluate on it
            if ($resultItemDirectiveArgs['if']) {
                $idsSatisfyingCondition[] = $id;
            }
        }
        return $idsSatisfyingCondition;
    }
}
