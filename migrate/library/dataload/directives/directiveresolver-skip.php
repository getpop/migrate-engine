<?php
namespace PoP\Engine;
use PoP\ComponentModel\DirectiveResolvers\AbstractDirectiveResolver;

class SkipDirectiveResolver extends AbstractDirectiveResolver
{
    use FilterIDsSatisfyingConditionTrait;

    const DIRECTIVE_NAME = 'skip';
    // public function getDirectiveName(): string {
    //     return self::DIRECTIVE_NAME;
    // }

    public function resolveDirective($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$dbErrors, array &$dbWarnings, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $skipDataFieldsForIds = $this->getIdsSatisfyingCondition($fieldResolver, $resultIDItems, $this->directive, $idsDataFields, $dbErrors, $dbWarnings);
        foreach ($skipDataFieldsForIds as $id) {
            $idsDataFields[$id]['direct'] = [];
            $idsDataFields[$id]['conditional'] = [];
        }
    }
}

SkipDirectiveResolver::attach(\PoP\ComponentModel\AttachableExtensions\AttachableExtensionGroups::FIELDDIRECTIVERESOLVERS);
