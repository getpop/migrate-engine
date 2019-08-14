<?php
namespace PoP\Engine\Impl;

define('GD_DATALOAD_DATASTRUCTURE_MIRRORQUERY', 'mirrorquery');

class DataStructureFormatter_MirrorQuery extends \PoP\Engine\DataStructureFormatterBase
{
    public function getName()
    {
        return GD_DATALOAD_DATASTRUCTURE_MIRRORQUERY;
    }

    protected function getFields()
    {
        // Allow REST to override with default fields
        $vars = \PoP\Engine\Engine_Vars::getVars();
        return $vars['fields'];
    }

    public function getFormattedData($data)
    {
        // Re-create the shape of the query by iterating through all dbObjectIDs and all required fields,
        // getting the data from the corresponding dbKeyPath
        $ret = [];
        if ($fields = $this->getFields()) {
            $databases = $data['databases'] ?? [];
            $datasetModuleData = $data['datasetmoduledata'] ?? [];
            foreach ($datasetModuleData as $moduleName => $dbObjectIDs) {
                $dbKeyPaths = $data['datasetmodulesettings'][$moduleName]['dbkeys'] ?? [];
                $dbObjectIDorIDs = $dbObjectIDs['dbobjectids'];
                $this->addData($ret, $fields, $databases, $dbObjectIDorIDs, 'id', $dbKeyPaths, false);
            }
        }

        return $ret;
    }
    // GraphQL/REST cannot have getExtraRoutes()!!!!! Because the fields can't be applied to different resources! (Eg: author/leo/ and author/leo/?route=posts)
    // public function getFormattedData($data)
    // {
    //     // Re-create the shape of the query by iterating through all dbObjectIDs and all required fields,
    //     // getting the data from the corresponding dbKeyPath
    //     $ret = [];
    //     if ($fields = $this->getFields()) {
    //         $engine = EngineFactory::getInstance();
    //         list($has_extra_routes) = $engine->listExtraRouteVars();
    //         $vars = \PoP\Engine\Engine_Vars::getVars();
    //         $dataoutputmode = $vars['dataoutputmode'];

    //         $databases = $data['databases'] ?? [];
    //         $datasetModuleData = $data['datasetmoduledata'] ?? [];
    //         $datasetModuleSettings = $data['datasetmodulesettings'] ?? [];
    //         if ($dataoutputmode == GD_URLPARAM_DATAOUTPUTMODE_SPLITBYSOURCES) {
    //             if ($has_extra_routes) {
    //                 $datasetModuleData = array_merge_recursive(
    //                     $datasetModuleData['immutable'] ?? [],
    //                     ($has_extra_routes ? array_values($datasetModuleData['mutableonmodel'])[0] : $datasetModuleData['mutableonmodel']) ?? [],
    //                     ($has_extra_routes ? array_values($datasetModuleData['mutableonrequest'])[0] : $datasetModuleData['mutableonrequest']) ?? []
    //                 );
    //                 $datasetModuleSettings = array_merge_recursive(
    //                     $datasetModuleSettings['immutable'] ?? [],
    //                     ($has_extra_routes ? array_values($datasetModuleSettings['mutableonmodel'])[0] : $datasetModuleSettings['mutableonmodel']) ?? [],
    //                     ($has_extra_routes ? array_values($datasetModuleSettings['mutableonrequest'])[0] : $datasetModuleSettings['mutableonrequest']) ?? []
    //                 );
    //             }
    //         } elseif ($dataoutputmode == GD_URLPARAM_DATAOUTPUTMODE_COMBINED) {
    //             if ($has_extra_routes) {
    //                 $datasetModuleData = array_values($datasetModuleData)[0];
    //                 $datasetModuleSettings = array_values($datasetModuleSettings)[0];
    //             }
    //         }
    //         foreach ($datasetModuleData as $moduleName => $dbObjectIDs) {
    //             $dbKeyPaths = $datasetModuleSettings[$moduleName]['dbkeys'] ?? [];
    //             $dbObjectIDorIDs = $dbObjectIDs['dbobjectids'];
    //             $this->addData($ret, $fields, $databases, $dbObjectIDorIDs, 'id', $dbKeyPaths, false);
    //         }
    //     }

    //     return $ret;
    // }

    protected function addData(&$ret, $fields, &$databases, $dbObjectIDorIDs, $dbObjectKeyPath, &$dbKeyPaths, $concatenateField = true)
    {
        // Property fields have numeric key only. From them, obtain the fields to print for the object
        $propertyFields = array_filter(
            $fields,
            function ($key) {
                return is_numeric($key);
            },
            ARRAY_FILTER_USE_KEY
        );
        // All other fields must be nested, to keep fetching data for the object relationships
        $nestedFields = array_diff($fields, $propertyFields);

        // The results can be a single ID or value, or an array of IDs
        if (is_array($dbObjectIDorIDs)) {
            foreach ($dbObjectIDorIDs as $dbObjectID) {
                // Add a new array for this DB object, where to return all its properties
                $ret[] = [];
                $dbObjectRet = &$ret[count($ret)-1];
                $this->addDBObjectData($dbObjectRet, $propertyFields, $nestedFields, $databases, $dbObjectID, $dbObjectKeyPath, $dbKeyPaths, $concatenateField);
            }
        }
        else {
            $dbObjectID = $dbObjectIDorIDs;
            $this->addDBObjectData($ret, $propertyFields, $nestedFields, $databases, $dbObjectID, $dbObjectKeyPath, $dbKeyPaths, $concatenateField);
        }
    }

    protected function addDBObjectData(&$dbObjectRet, $propertyFields, $nestedFields, &$databases, $dbObjectID, $dbObjectKeyPath, &$dbKeyPaths, $concatenateField)
    {
        // Add all properties requested from the object
        $dbObject = $databases[$dbKeyPaths[$dbObjectKeyPath]][$dbObjectID] ?? [];
        foreach ($propertyFields as $propertyField) {
            $dbObjectRet[$propertyField] = $dbObject[$propertyField];
        }

        // Add the nested levels
        foreach ($nestedFields as $nestedField => $nestedPropertyFields) {
            // The first field, "id", needs not be concatenated. All the others do need
            $nextField = ($concatenateField ? $dbObjectKeyPath.'.' : '').$nestedField;

            // Add a new subarray for the nested property
            $dbObjectNestedPropertyRet = &$dbObjectRet[$nestedField];
            // If the value of the nested property is NULL, then no need to return it (to avoid guessing if it's a null ID or a null array, in which case the response may be different)
            if (!is_null($dbObject[$nestedField])) {
                // If it is an empty array, then directly add an empty array as the result
                if (is_array($dbObject[$nestedField]) && empty($dbObject[$nestedField])) {
                    $dbObjectRet[$nestedField] = [];
                } else {
                    $this->addData($dbObjectNestedPropertyRet, $nestedPropertyFields, $databases, $dbObject[$nestedField], $nextField, $dbKeyPaths);
                }
            }
        }
    }
}

/**
 * Initialize
 */
new DataStructureFormatter_MirrorQuery();
