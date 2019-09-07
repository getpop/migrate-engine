<?php
namespace PoP\Engine\Impl;

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
        return [
            'data' => [
                $path => parent::getFormattedData($data),
            ],
        ];
    }
}

/**
 * Initialize
 */
new DataStructureFormatter_GraphQL();
