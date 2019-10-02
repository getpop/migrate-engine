<?php
namespace PoP\Engine;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldUtils;
use PoP\ComponentModel\FieldValidationUtils;
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
            'if',
            'not',
            'and',
            'or',
            'equals',
            'empty',
            'echo',
            'var',
            'context',
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
            'echo' => TYPE_MIXED,
            'var' => TYPE_MIXED,
            'context' => TYPE_OBJECT,
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
            'echo' => $translationAPI->__('Echo a value', 'pop-component-model'),
            'var' => $translationAPI->__('Retrieve the value of a certain property from the `$vars` context object', 'pop-component-model'),
            'context' => $translationAPI->__('Retrieve the `$vars` context object', 'pop-component-model'),
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
                        'name' => 'condition-field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field, of boolean type, to eval if its execution value is `true`', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'then-field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field to execute if the condition field evals to `true`', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'else-field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field to execute if the condition field evals to `false`', 'pop-component-model'),
                    ],
                ];

            case 'not':
                return [
                    [
                        'name' => 'field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field, of boolean type, from which to obtain the opposite value', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'and':
            case 'or':
                return [
                    [
                        'name' => 'fields',
                        'type' => TYPE_STRING,
                        'description' => sprintf(
                            $translationAPI->__('The fields (of boolean type) on whose results to execute the `%s` operation, separated with \',\'', 'pop-component-model'),
                            strtoupper($fieldName)
                        ),
                        'mandatory' => true,
                    ],
                ];

            case 'equals':
                return [
                    [
                        'name' => 'field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field to execute and compare against the provided value', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                    [
                        'name' => 'value',
                        'type' => TYPE_MIXED,
                        'description' => $translationAPI->__('The value against which to compare the result from the field', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'empty':
                return [
                    [
                        'name' => 'field',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The field to execute and check if its value is empty', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'echo':
                return [
                    [
                        'name' => 'value',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('Value to echo back', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];

            case 'var':
                return [
                    [
                        'name' => 'name',
                        'type' => TYPE_STRING,
                        'description' => $translationAPI->__('The name of the variable to retrieve from the `$vars` context object', 'pop-component-model'),
                        'mandatory' => true,
                    ],
                ];
        }

        return parent::getFieldDocumentationArgs($fieldName);
    }

    public function resolveSchemaValidationErrorDescription($fieldResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'if':
                if ($maybeError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['condition-field', 'then-field'], $fieldName, $fieldArgs)) {
                    return $maybeError;
                }
                if ($maybeError = FieldValidationUtils::validateFieldsExist(
                    $fieldResolver,
                    array_filter([
                        $fieldArgs['condition-field'],
                        $fieldArgs['then-field'],
                        $fieldArgs['else-field']
                    ])
                )) {
                    return $maybeError;
                }
                return null;
            case 'not':
                if ($maybeError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['field'], $fieldName, $fieldArgs)) {
                    return $maybeError;
                }
                if ($maybeError = FieldValidationUtils::validateFieldsExist($fieldResolver, [$fieldArgs['field']])) {
                    return $maybeError;
                }
                return null;
            case 'and':
            case 'or':
                if ($maybeError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['fields'], $fieldName, $fieldArgs)) {
                    return $maybeError;
                }
                if ($maybeError = FieldValidationUtils::validateFieldsExist($fieldResolver, array_map('trim', explode(',', $fieldArgs['fields'])))) {
                    return $maybeError;
                }
                return null;
            case 'equals':
                if ($missingError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['field', 'value'], $fieldName, $fieldArgs)) {
                    return $missingError;
                }
                if ($maybeError = FieldValidationUtils::validateFieldsExist($fieldResolver, [$fieldArgs['field']], $fieldName, $fieldArgs)) {
                    return $maybeError;
                }
                return null;
            case 'empty':
                if ($missingError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['field'], $fieldName, $fieldArgs)) {
                    return $missingError;
                }
                if ($maybeError = FieldValidationUtils::validateFieldsExist($fieldResolver, [$fieldArgs['field']])) {
                    return $maybeError;
                }
                return null;
            case 'echo':
                if ($missingError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['value'], $fieldName, $fieldArgs)) {
                    return $missingError;
                }
                return null;
            case 'var':
                if ($missingError = FieldValidationUtils::validateNotMissingFieldArguments($fieldResolver, ['name'], $fieldName, $fieldArgs)) {
                    return $missingError;
                }
                $safeVars = $this->getSafeVars();
                if (!isset($safeVars[$fieldArgs['name']])) {
                    return sprintf(
                        $translationAPI->__('Var \'%s\' does not exist in `$vars`', 'pop-component-model'),
                        $fieldArgs['name']
                    );
                };
                return null;
        }

        return parent::resolveSchemaValidationErrorDescription($fieldResolver, $fieldName, $fieldArgs);
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
                $conditionField = $fieldArgs['condition-field'];
                $executeField = null;
                if ($fieldResolver->resolveValue($resultItem, $conditionField)) {
                    $executeField = $fieldArgs['then-field'];
                } elseif (isset($fieldArgs['else-field'])) {
                    $executeField = $fieldArgs['else-field'];
                }
                if ($executeField) {
                    return $fieldResolver->resolveValue($resultItem, $executeField);
                }
                return null;
            case 'not':
                $notField = $fieldArgs['field'];
                return !$fieldResolver->resolveValue($resultItem, $notField);
            case 'and':
            case 'or':
                $opFields = explode(',', $fieldArgs['fields']);
                $value = true;
                foreach ($opFields as $opField) {
                    if ($fieldName == 'and') {
                        $value = $value && $fieldResolver->resolveValue($resultItem, $opField);
                    } elseif ($fieldName == 'or') {
                        $value = $value || $fieldResolver->resolveValue($resultItem, $opField);
                    }
                }
                return $value;
            case 'equals':
                $equalsField = $fieldArgs['field'];
                $equalsValue = $fieldArgs['value'];
                return $equalsValue == $fieldResolver->resolveValue($resultItem, $equalsField);
            case 'empty':
                $emptyField = $fieldArgs['field'];
                return empty($fieldResolver->resolveValue($resultItem, $emptyField));
            case 'echo':
                return $fieldArgs['value'];
            case 'var':
                $safeVars = $this->getSafeVars();
                return $safeVars[$fieldArgs['name']];
            case 'context':
                return $this->getSafeVars();
        }

        return parent::resolveValue($fieldResolver, $resultItem, $fieldName, $fieldArgs);
    }
}

// Static Initialization: Attach
FieldValueResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDVALUERESOLVERS);
