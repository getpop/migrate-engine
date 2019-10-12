<?php
namespace PoP\Engine;
use PoP\ComponentModel\QueryHandlers\ParamConstants;

class QueryHandler_Redirect extends QueryHandler_ActionExecution
{
    // function prepareQueryArgs(&$query_args) {

    //     parent::prepareQueryArgs($query_args);

    //     // Add the Redirect to Param. If there is none, use the referrer.
    //     // This is useful when coming from the Login link above the Template, which can't pass the 'redirect_to' data
    //     $query_args[GD_URLPARAM_REDIRECTTO] = $query_args[GD_URLPARAM_REDIRECTTO] ?? $_SERVER['HTTP_REFERER'];
    // }

    public function getQueryParams($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs): array
    {
        $ret = parent::getQueryParams($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs);

        $query_args = $data_properties[ParamConstants::QUERYARGS];

        // Add the Redirect to Param. If there is none, use the referrer.
        // This is useful when coming from the Login link above the Template, which can't pass the 'redirect_to' data
        $ret[GD_URLPARAM_REDIRECTTO] = $query_args[GD_URLPARAM_REDIRECTTO] ?? $_SERVER['HTTP_REFERER'];

        return $ret;
    }

    // function getUniquetodomainQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids) {
    
    //     $ret = parent::getUniquetodomainQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

    //     $query_args = $data_properties[ParamConstants::QUERYARGS];

    //     // Add the Redirect to
    //     $ret[ParamConstants::PARAMS][GD_URLPARAM_REDIRECTTO] = $query_args[GD_URLPARAM_REDIRECTTO];

    //     return $ret;
    // }
    
    // function getDatafeedback($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids) {
    
    //     $ret = parent::getDatafeedback($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

    //     $query_args = $data_properties[ParamConstants::QUERYARGS];
        
    //     // Add the Redirect to
    //     $ret[ParamConstants::PARAMS][GD_URLPARAM_REDIRECTTO] = $query_args[GD_URLPARAM_REDIRECTTO];
        
    //     return $ret;
    // }
}
    
/**
 * Initialize
 */
new QueryHandler_Redirect();
