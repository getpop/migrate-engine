<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Engine\Facades\InstanceManagerFacade;

abstract class Dataloader
{
    abstract public function getDatabaseKey();
    abstract public function getFieldValueResolverClass();

    public function getDataquery()
    {
        return null;
    }

    public function executeGetData(array $ids)
    {
        return array();
    }

    final public function getDataitems($formatter, $resultset, $ids_data_fields = array())
    {
        $instanceManager = InstanceManagerFacade::getInstance();

        $databaseitems = $dbobjectids = array();
        if ($fieldValueResolverClass = $this->getFieldValueResolverClass()) {
            $fieldValueResolver = $instanceManager->getInstance($fieldValueResolverClass);

            // Iterate data, extract into final results
            if ($resultset) {
                // It is either an array or a single value
                if (is_array($resultset)) {
                    foreach ($resultset as $resultitem) {
                        $this->addDataitem($databaseitems, $dbobjectids, $fieldValueResolver, $resultitem, $formatter, $resultset, $ids_data_fields);
                    }
                } else {
                    $resultitem = $resultset;
                    $this->addDataitem($databaseitems, $dbobjectids, $fieldValueResolver, $resultitem, $formatter, $resultset, $ids_data_fields);
                }
            }
        }

        return array(
            'dbobjectids' => $dbobjectids,
            'dbitems' => $databaseitems,
        );
    }

    final public function addDataitem(&$databaseitems, &$dbobjectids, &$fieldValueResolver, &$resultitem, $formatter, $resultset, $ids_data_fields = array())
    {
        // Obtain the data-fields for that $id
        $id = $fieldValueResolver->getId($resultitem);
        $data_fields = array(
            'primary' => $ids_data_fields[$id] ?? array(),
        );
        $dbobjectids[] = $id;

        HooksAPIFacade::getInstance()->doAction(
            'Dataloader:modifyDataFields',
            array(&$data_fields),
            $this
        );

        // Add to the dataitems
        foreach ($data_fields as $dbname => $db_data_fields) {
            if ($db_data_fields) {
                $databaseitems[$dbname] = $databaseitems[$dbname] ?? array();
                $formatter->addToDataitems($databaseitems[$dbname], $id, $db_data_fields, $resultitem, $fieldValueResolver);
            }
        }
    }


    /**
     * key: id
     * value: data-fields to fetch for that id
     */
    final public function getData(array $ids_data_fields = array())
    {
        // Get the ids
        $ids = array_keys($ids_data_fields);

        // Execute the query, get data to iterate
        return $this->executeGetData($ids);
    }
}
