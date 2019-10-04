<?php
namespace PoP\Engine;
use PoP\ComponentModel\AbstractDirectiveResolver;

class SkipDirectiveResolver extends AbstractDirectiveResolver
{
    use FilterIDsSatisfyingConditionTrait;

    const DIRECTIVE_NAME = 'skip';
    // public function getDirectiveName(): string {
    //     return self::DIRECTIVE_NAME;
    // }

    public function resolveDirective($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$dbErrors, array &$schemaErrors, array &$schemaDeprecations)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $skipDataFieldsForIds = $this->getIdsSatisfyingCondition($fieldResolver, $resultIDItems, $idsDataFields, $dbErrors, $schemaErrors, $schemaDeprecations);
        foreach ($skipDataFieldsForIds as $id) {
            $idsDataFields[$id]['direct'] = [];
            $idsDataFields[$id]['conditional'] = [];
        }
    }
}

SkipDirectiveResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDDIRECTIVERESOLVERS);
