<?php
namespace PoP\Engine;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Engine_Vars;
use PoP\ComponentModel\DataloadUtils;
use PoP\ComponentModel\SchemaDefinition;

class OperatorsFieldValueResolver extends AbstractOperatorsFieldValueResolver
{
    public function getFieldNamesToResolve(): array
    {
        return [
            'if',
            'not',
            'and',
            'or',
            'equals',
            'empty',
            'var',
            'context',
            'sprintf',
        ];
    }

    public function getFieldDocumentationType(string $fieldName): ?string
    {
        $types = [
            'if' => TYPE_MIXED,
            'not' => TYPE_BOOL,
            'and' => TYPE_BOOL,
            'or' => TYPE_BOOL,
            'equals' => TYPE_BOOL,
            'empty' => TYPE_BOOL,
            'var' => TYPE_MIXED,
            'context' => TYPE_OBJECT,
            'sprintf' => TYPE_STRING,
        ];
        return $types[$fieldName];
    }

    public function getFieldDocumentationDescription(string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'if' => $translationAPI->__('If a boolean property is true, execute a field, else, execute another field', 'pop-component-model'),
            'not' => $translationAPI->__('Return the opposite value of a boolean property', 'pop-component-model'),
            'and' => $translationAPI->__('Return an `AND` operation among several boolean properties', 'pop-component-model'),
            'or' => $translationAPI->__('Return an `OR` operation among several boolean properties', 'pop-component-model'),
            'equals' => $translationAPI->__('Indicate if the result from a field equals a certain value', 'pop-component-model'),
            'empty' => $translationAPI->__('Indicate if the result from a field is empty', 'pop-component-model'),
            'var' => $translationAPI->__('Retrieve the value of a certain property from the `$vars` context object', 'pop-component-model'),
            'context' => $translationAPI->__('Retrieve the `$vars` context object', 'pop-component-model'),
            'sprintf' => $translationAPI->__('Replace placeholders inside a string with provided values', 'pop-component-model'),
        ];
        return $descriptions[$fieldName];
    }

    public function getFieldDocumentationArgs(string $fieldName): ?array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'if':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'condition',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_BOOL,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The condition to check if its value is `true` or `false`', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'then',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to return if the condition evals to `true`', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'else',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to return if the condition evals to `false`', 'pop-component-model'),
                    ],
                ];

            case 'not':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_BOOL,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value from which to return its opposite value', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'and':
            case 'or':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'values',
                        SchemaDefinition::ARGNAME_TYPE => DataloadUtils::combineTypes(TYPE_ARRAY, TYPE_BOOL),
                        SchemaDefinition::ARGNAME_DESCRIPTION => sprintf(
                            $translationAPI->__('The array of values on which to execute the `%s` operation', 'pop-component-model'),
                            strtoupper($fieldName)
                        ),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'equals':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value1',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The first value to compare', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value2',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The second value to compare', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'empty':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'value',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_MIXED,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The value to check if it is empty', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'var':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'name',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The name of the variable to retrieve from the `$vars` context object', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];

            case 'sprintf':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'string',
                        SchemaDefinition::ARGNAME_TYPE => TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The string containing the placeholders', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                    [
                        SchemaDefinition::ARGNAME_NAME => 'values',
                        SchemaDefinition::ARGNAME_TYPE => DataloadUtils::combineTypes(TYPE_ARRAY, TYPE_STRING),
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The values to replace the placeholders with inside the string', 'pop-component-model'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
        }

        return parent::getFieldDocumentationArgs($fieldName);
    }

    public function resolveSchemaValidationErrorDescription($fieldResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        if ($error = parent::resolveSchemaValidationErrorDescription($fieldResolver, $fieldName, $fieldArgs)) {
            return $error;
        }

        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'var':
                $safeVars = $this->getSafeVars();
                if (!isset($safeVars[$fieldArgs['name']])) {
                    return sprintf(
                        $translationAPI->__('Var \'%s\' does not exist in `$vars`', 'pop-component-model'),
                        $fieldArgs['name']
                    );
                };
                return null;
        }

        return null;
    }

    protected function getSafeVars() {
        if (is_null($this->safeVars)) {
            $this->safeVars = Engine_Vars::getVars();
            HooksAPIFacade::getInstance()->doAction(
                'PoP\ComponentModel\AbstractFieldResolver:safeVars',
                array(&$this->safeVars)
            );
        }
        return $this->safeVars;
    }

    public function resolveValue($fieldResolver, $resultItem, string $fieldName, array $fieldArgs = [])
    {
        switch ($fieldName) {
            case 'if':
                if ($fieldArgs['condition']) {
                    return $fieldArgs['then'];
                } elseif (isset($fieldArgs['else'])) {
                    return $fieldArgs['else'];
                }
                return null;
            case 'not':
                return !$fieldArgs['value'];
            case 'and':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated && $value;
                    return $accumulated;
                }, true);
            case 'or':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated || $value;
                    return $accumulated;
                }, false);
            case 'equals':
                return $fieldArgs['value1'] == $fieldArgs['value2'];
            case 'empty':
                return empty($fieldArgs['value']);
            case 'var':
                $safeVars = $this->getSafeVars();
                return $safeVars[$fieldArgs['name']];
            case 'context':
                return $this->getSafeVars();
            case 'sprintf':
                return sprintf($fieldArgs['string'], ...$fieldArgs['values']);
        }

        return parent::resolveValue($fieldResolver, $resultItem, $fieldName, $fieldArgs);
    }
}

// Static Initialization: Attach
OperatorsFieldValueResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDVALUERESOLVERS);
