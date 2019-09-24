<?php
namespace PoP\Engine;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\FieldUtils;
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

    public function getSchemaValidationErrorDescription($fieldResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'not':
                if (!isset($fieldArgs['field'])) {
                    return $translationAPI->__('Argument \'field\' cannot be empty', 'pop-component-model');
                }
                $notFieldName = FieldUtils::getFieldName($fieldArgs['field']);
                if (!in_array($notFieldName, $fieldResolver->getFieldNamesToResolve())) {
                    return sprintf(
                        $translationAPI->__('Field with name \'%s\' does not exist for this entity', 'pop-component-model'),
                        $notFieldName
                    );
                }
                return null;
            case 'and':
            case 'or':
                if (!isset($fieldArgs['fields'])) {
                    return $translationAPI->__('Argument \'fields\' cannot be empty', 'pop-component-model');
                }
                $doNotExist = [];
                $resolvedFieldNames = $fieldResolver->getFieldNamesToResolve();
                foreach (explode(',', $fieldArgs['fields']) as $andField) {
                    $andFieldName = FieldUtils::getFieldName($andField);
                    if (!in_array($andFieldName, $resolvedFieldNames)) {
                        $doNotExist[] = $andFieldName;
                    }
                }
                if ($doNotExist) {
                    return count($doNotExist) == 1 ?
                        sprintf(
                            $translationAPI->__('Field with name \'%s\' does not exist for this entity', 'pop-component-model'),
                            $doNotExist[0]
                        ) :
                        sprintf(
                            $translationAPI->__('Fields with names \'%s\' do not exist for this entity', 'pop-component-model'),
                            implode($translationAPI->__('\', \''), $doNotExist)
                        );
                }
                return null;
            case 'equals':
                $missing = [];
                $mandatoryFieldNames = ['field', 'value'];
                foreach ($mandatoryFieldNames as $mandatoryFieldName) {
                    if (!isset($fieldArgs[$mandatoryFieldName])) {
                        $missing[] = $mandatoryFieldName;
                    }
                }
                if ($missing) {
                    return count($missing) == 1 ?
                        sprintf(
                            $translationAPI->__('Argument \'%s\' cannot be empty', 'pop-component-model'),
                            $missing[0]
                        ) :
                        sprintf(
                            $translationAPI->__('Arguments \'%s\' cannot be empty', 'pop-component-model'),
                            implode($translationAPI->__('\', \''), $missing)
                        );
                }
                $equalsFieldName = FieldUtils::getFieldName($fieldArgs['field']);
                if (!in_array($equalsFieldName, $fieldResolver->getFieldNamesToResolve())) {
                    return sprintf(
                        $translationAPI->__('Field with name \'%s\' does not exist for this entity', 'pop-component-model'),
                        $equalsFieldName
                    );
                }
                return null;
            case 'empty':
                if (!isset($fieldArgs['field'])) {
                    return $translationAPI->__('Argument \'field\' cannot be empty', 'pop-component-model');
                }
                $emptyFieldName = FieldUtils::getFieldName($fieldArgs['field']);
                if (!in_array($emptyFieldName, $fieldResolver->getFieldNamesToResolve())) {
                    return sprintf(
                        $translationAPI->__('Field with name \'%s\' does not exist for this entity', 'pop-component-model'),
                        $emptyFieldName
                    );
                }
                return null;
            case 'echo':
                if (!isset($fieldArgs['value'])) {
                    return $translationAPI->__('Argument \'value\' cannot be empty', 'pop-component-model');
                }
                return null;
            case 'var':
                if (!isset($fieldArgs['name'])) {
                    return $translationAPI->__('Argument \'name\' cannot be empty', 'pop-component-model');
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

        return parent::getSchemaValidationErrorDescription($fieldResolver, $fieldName, $fieldArgs);
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

    public function getValue($fieldResolver, $resultItem, string $fieldName, array $fieldArgs = [])
    {
        switch ($fieldName) {
            case 'not':
                $notField = $fieldArgs['field'];
                $notFieldName = FieldUtils::getFieldName($notField);
                $notFieldArgs = FieldUtils::getFieldArgs($notField);
                return !$fieldResolver->getValue($resultItem, $notFieldName, $notFieldArgs);
            case 'and':
            case 'or':
                $opFields = explode(',', $fieldArgs['fields']);
                $value = true;
                foreach ($opFields as $opField) {
                    $opFieldName = FieldUtils::getFieldName($opField);
                    $opFieldArgs = FieldUtils::getFieldArgs($opField);
                    if ($fieldName == 'and') {
                        $value = $value && $fieldResolver->getValue($resultItem, $opFieldName, $opFieldArgs);
                    } elseif ($fieldName == 'or') {
                        $value = $value || $fieldResolver->getValue($resultItem, $opFieldName, $opFieldArgs);
                    }
                }
                return $value;
            case 'equals':
                $equalsFieldName = FieldUtils::getFieldName($fieldArgs['field']);
                $equalsFieldArgs = FieldUtils::getFieldArgs($fieldArgs['field']);
                $equalsValue = $fieldArgs['value'];
                return $equalsValue == $fieldResolver->getValue($resultItem, $equalsFieldName, $equalsFieldArgs);
            case 'empty':
                $emptyField = $fieldArgs['field'];
                $emptyFieldName = FieldUtils::getFieldName($emptyField);
                $emptyFieldArgs = FieldUtils::getFieldArgs($emptyField);
                return empty($fieldResolver->getValue($resultItem, $emptyFieldName, $emptyFieldArgs));
            case 'echo':
                return $fieldArgs['value'];
            case 'var':
                $safeVars = $this->getSafeVars();
                return $safeVars[$fieldArgs['name']];
            case 'context':
                return $this->getSafeVars();
        }

        return parent::getValue($fieldResolver, $resultItem, $fieldName, $fieldArgs);
    }
}

// Static Initialization: Attach
FieldValueResolver::attach(POP_ATTACHABLEEXTENSIONGROUP_FIELDVALUERESOLVERS);
