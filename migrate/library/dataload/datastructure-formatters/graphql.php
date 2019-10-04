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
        $ret = [];

        // Add errors
        $errors = [];
        if ($data['dbErrors']) {
            $errors = $this->reformatDBEntries($data['dbErrors']);
        }
        if ($data['schemaErrors']) {
            $errors = array_merge(
                $errors,
                $this->reformatSchemaEntries($data['schemaErrors'])
            );
        }
        if ($data['queryErrors']) {
            $errors = array_merge(
                $errors,
                $this->reformatQueryEntries($data['queryErrors'])
            );
        }
        if ($errors) {
            $ret['errors'] = $errors;
        }

        // Add deprecations
        if ($data['schemaDeprecations']) {
            $ret['deprecations'] = $this->reformatSchemaEntries($data['schemaDeprecations']);
        }

        if ($resultData = parent::getFormattedData($data)) {
            // GraphQL places the queried data under entries 'data' => query => results
            // Replicate this structure. Because we don't have a query name here, replace it with the queried URL path, which is known to the client
            $path = \PoP\ComponentModel\Utils::getURLPath();
            // If there is no path, it is the single point of entry (homepage => root)
            if (!$path) {
                $path = '/';
            }
            $ret['data'] = [
                $path => $resultData,
            ];
        }

        return $ret;
    }

    protected function reformatDBEntries($entries)
    {
        $ret = [];
        foreach ($entries as $dbKey => $id_field_messages) {
            foreach ($id_field_messages as $id => $field_messages) {
                foreach ($field_messages as $field => $messages) {
                    foreach ($messages as $message) {
                        $ret[] = [
                            'type' => 'dataObject',
                            'entityDBKey' => $dbKey,
                            'id' => $id,
                            'fieldOrDirective' => $field,
                            'message' => $message,
                        ];
                    }
                }
            }
        }
        return $ret;
    }

    protected function reformatSchemaEntries($entries)
    {
        $ret = [];
        foreach ($entries as $dbKey => $field_messages) {
            foreach ($field_messages as $field => $messages) {
                foreach ($messages as $message) {
                    $ret[] = [
                        'type' => 'schema',
                        'entityDBKey' => $dbKey,
                        'fieldOrDirective' => $field,
                        'message' => $message,
                    ];
                }
            }
        }
        return $ret;
    }

    protected function reformatQueryEntries($entries)
    {
        $ret = [];
        foreach ($entries as $message) {
            $ret[] = [
                'type' => 'query',
                'message' => $message,
            ];
        }
        return $ret;
    }
}

/**
 * Initialize
 */
new DataStructureFormatter_GraphQL();
