<?php
namespace PoP\Engine;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Engine_Vars;

class FieldValueResolver extends \PoP\ComponentModel\AbstractDBDataFieldValueResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(\PoP\ComponentModel\FieldResolverBase::class);
    }

    public function getFieldNamesToResolve(): array
    {
        return [
            'IF',
            'NOT',
            'AND',
            'OR',
            'EQUALS',
            'EMPTY',
            'VAR',
            'CONTEXT',
            'SPRINTF',
        ];
    }

    public function getFieldDocumentationType(string $fieldName): ?string
    {
        $types = [
            'IF' => TYPE_MIXED,
            'NOT' => TYPE_BOOL,
            'AND' => TYPE_BOOL,
            'OR' => TYPE_BOOL,
            'EQUALS' => TYPE_BOOL,
            'EMPTY' => TYPE_BOOL,
            'VAR' => TYPE_MIXED,
            'CONTEXT' => TYPE_OBJECT,
            'SPRINTF' => TYPE_STRING,
        ];
        return $types[$fieldName];
    }

    public function getFieldDocumentationDescription(string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'IF' => $translationAPI->__('If a boolean property is true, execute a field, else, execute another field', 'pop-component-model'),
            'NOT' => $translationAPI->__('Return the opposite value of a boolean property', 'pop-component-model'),
            'AND' => $translationAPI->__('Return an `AND` operation among several boolean properties', 'pop-component-model'),
            'OR' => $translationAPI->__('Return an `OR` operation among several boolean properties', 'pop-component-model'),
            'EQUALS' => $translationAPI->__('Indicate if the result from a field equals a certain value', 'pop-component-model'),
            'EMPTY' => $translationAPI->__('Indicate if the result from a field is empty', 'pop-component-model'),
            'VAR' => $translationAPI->__('Retrieve the value of a certain property from the `$vars` context object', 'pop-component-model'),
            'CONTEXT' => $translationAPI->__('Retrieve the `$vars` context object', 'pop-component-model'),
            'SPRINTF' => $translationAPI->__('Replace placeholders inside a string with provided values', 'pop-component-model'),
        ];
        return $descriptions[$fieldName];
    }

    public function getFieldDocumentationArgs(string $fieldName): ?array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'IF':
                return [
                    [
                        'name' => 'condition',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The condition to check if its value is `true` or `false`', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'then',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The value to return if the condition evals to `true`', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'else',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The value to return if the condition evals to `false`', 'pop-component-model'),
                    ],
                ];

            case 'NOT':
                return [
                    [
                        'name' => 'value',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The value from which to return its opposite value', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'AND':
            case 'OR':
                return [
                    [
                        'name' => 'values',
                        'type' => TYPE_ARRAY,
                        'description' => sprintf(
                            $translationAPI->__('The array of values on which to execute the `%s` operation', 'pop-component-model'),
                            strtoupper($fieldName)
                        ),
                        'mandatory' => true,
                    ],
                ];

            case 'EQUALS':
                return [
                    [
                        'name' => 'value1',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The first value to compare', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'value2',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The second value to compare', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'EMPTY':
                return [
                    [
                        'name' => 'value',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The value to check if it is empty', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'VAR':
                return [
                    [
                        'name' => 'name',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The name of the variable to retrieve from the `$vars` context object', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'SPRINTF':
                return [
                    [
                        'name' => 'string',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The string containing the placeholders', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'values',
                        'type' => TYPE_ARRAY,
                        'description' => $translationAPI->__('The values to replace the placeholders with inside the string', 'pop-component-model'),
                        'mandatory' => true,
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
            case 'VAR':
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
            case 'IF':
                if ($fieldArgs['condition']) {
                    return $fieldArgs['then'];
                } elseif (isset($fieldArgs['else'])) {
                    return $fieldArgs['else'];
                }
                return null;
            case 'NOT':
                return !$fieldArgs['value'];
            case 'AND':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated && $value;
                    return $accumulated;
                }, true);
            case 'OR':
                return array_reduce($fieldArgs['values'], function($accumulated, $value) {
                    $accumulated = $accumulated || $value;
                    return $accumulated;
                }, false);
            case 'EQUALS':
                return $fieldArgs['value1'] == $fieldArgs['value2'];
            case 'EMPTY':
                return empty($fieldArgs['value']);
            case 'VAR':
                $safeVars = $this->getSafeVars();
                return $safeVars[$fieldArgs['name']];
            case 'CONTEXT':
                return $this->getSafeVars();
            case 'SPRINTF':
                return sprintf($fieldArgs['string'], ...$fieldArgs['values']);
        }

        return parent::resolveValue($fieldResolver, $resultItem, $fieldName, $fieldArgs);
    }
}

// Static Initialization: Attach
FieldValueResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDVALUERESOLVERS);
