<?php
namespace PoP\Engine;
use PoP\ComponentModel\AbstractDirectiveResolver;
use PoP\ComponentModel\FieldUtils;

class SkipDirectiveResolver extends AbstractDirectiveResolver
{
    const DIRECTIVE_NAME = 'skip';
    public function getDirectiveName(): string {
        return self::DIRECTIVE_NAME;
    }

    public function resolveDirective($fieldResolver, array &$resultIDItems, array &$idsDataFields, array &$dbItems, array &$dbErrors, array &$schemaErrors, array &$schemaDeprecations)
    {
        // Check the condition field. If it is satisfied, then skip those fields
        $skipDataFieldsForIds = [];
        $field = $this->directiveArgs['if-field'];
        $fieldName = FieldUtils::getFieldName($field);
        $fieldArgs = FieldUtils::getFieldArgs($field);
        $fieldOutputKey = FieldUtils::getFieldOutputKey($field);
        foreach (array_keys($idsDataFields) as $id) {
            $resultItem = $resultIDItems[$id];
            $value = $fieldResolver->resolveValue($resultItem, $fieldName, $fieldArgs);
            if (\PoP\ComponentModel\GeneralUtils::isError($value)) {
                $error = $value;
                $dbErrors[(string)$id][$fieldOutputKey][] = $error->getErrorMessage();
            } elseif ($value) {
                $skipDataFieldsForIds[] = $id;
            }
        }
        // Remove from the data_fields list to execute on the resultItem
        foreach ($skipDataFieldsForIds as $id) {
            $idsDataFields[$id]['direct'] = [];
            $idsDataFields[$id]['conditional'] = [];
        }
    }
}

SkipDirectiveResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDDIRECTIVERESOLVERS);
