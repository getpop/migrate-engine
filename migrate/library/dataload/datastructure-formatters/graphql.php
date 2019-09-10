<?php
namespace PoP\Engine\Impl;

use PoP\Translation\Facades\TranslationAPIFacade;

define('GD_DATALOAD_DATASTRUCTURE_GRAPHQL', 'graphql');

class DataStructureFormatter_GraphQL extends DataStructureFormatter_MirrorQuery
{
    public function getName()
    {
        return GD_DATALOAD_DATASTRUCTURE_GRAPHQL;
    }

    public function getFormattedData($data)
    {
        // GraphQL places the queried data under entries 'data' => query => results
        // Replicate this structure. Because we don't have a query name here, replace it with the queried URL path, which is known to the client
        $path = \PoP\ComponentModel\Utils::getURLPath();
        // If there is no path, it is the single point of entry (homepage => root)
        if (!$path) {
            $path = '/';
        }
        $ret = [
            'data' => [
                $path => parent::getFormattedData($data),
            ],
        ];
        // Add errors
        $errors = [];
        if ($data['dbErrors']) {

        }
        if ($data['schemaErrors']) {
            $errors = array_merge(
                $errors,
                $this->reformatSchemaEntries($data['schemaErrors'])
            );
        }
        if ($errors) {
            $ret['errors'] = $errors;
        }

        // Add deprecations
        if ($data['schemaDeprecations']) {
            $ret['deprecations'] = $this->reformatSchemaEntries($data['schemaDeprecations']);
        }
        return $ret;
    }

    protected function reformatSchemaEntries($entries)
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $ret = [];
        foreach ($entries as $field => $message) {
            $ret[] = [
                'message' => sprintf(
                    $translationAPI->__('Field \'%s\': %s', 'pop-api-graphql'),
                    $field,
                    $message
                ),
            ];
        }
        return $ret;
    }
}

/**
 * Initialize
 */
new DataStructureFormatter_GraphQL();
