<?php

// BlockList because it serves both Carousel and InfiniteScroll
define('GD_DATALOAD_QUERYHANDLER_LIST', 'list');

class GD_DataLoad_QueryHandler_List extends \PoP\ComponentModel\QueryHandlerBase
{
    public function getName()
    {
        return GD_DATALOAD_QUERYHANDLER_LIST;
    }

    public function prepareQueryArgs(&$query_args)
    {
        parent::prepareQueryArgs($query_args);

        $pagenumber = $query_args[GD_URLPARAM_PAGENUMBER];
        $limit = $query_args[GD_URLPARAM_LIMIT];
        
        // Do not allow more than 10 times the set amount (so that hackers cannot bring the website down)
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $posts_per_page = $cmsengineapi->getOption(\PoP\LooseContracts\NameResolverFactory::getInstance()->getName('popcms:option:limit'));
        if ($limit > $posts_per_page * 10) {
            $limit = $posts_per_page * 10;
        }

        $query_args[GD_URLPARAM_PAGENUMBER] = $pagenumber ? intval($pagenumber) : 1;
        // Allow for Limit to be 0 (eg: Events Calendar), in that case it's valid, keep it
        $query_args[GD_URLPARAM_LIMIT] = $limit ? intval($limit) : $posts_per_page;
    }

    public function getQueryState($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs): array
    {
        $ret = parent::getQueryState($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs);
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();

        // Needed to loadLatest, to know from what time to get results
        if ($data_properties[GD_DATALOAD_DATASOURCE] == POP_DATALOAD_DATASOURCE_MUTABLEONREQUEST) {
            $ret[GD_URLPARAM_TIMESTAMP] = POP_CONSTANT_CURRENTTIMESTAMP;
        }
        
        // If it is lazy load, no need to calculate pagenumber / stop-fetching / etc
        if ($data_properties[GD_DATALOAD_LAZYLOAD] || $data_properties[GD_DATALOAD_EXTERNALLOAD] || $data_properties[GD_DATALOAD_DATASOURCE] != POP_DATALOAD_DATASOURCE_MUTABLEONREQUEST || $vars['loading-latest']) {
            return $ret;
        }

        // If data is not to be loaded, then "stop-fetching" as to not show the Load More button
        if ($data_properties[GD_DATALOAD_SKIPDATALOAD]) {
            $ret[GD_URLPARAM_STOPFETCHING] = true;
            return $ret;
        }
        
        $ret[GD_URLPARAM_STOPFETCHING] = PoP_BaseCollectionData_Utils::stopFetching($dbObjectIDOrIDs, $data_properties);
        
        return $ret;
    }

    public function getQueryParams($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs): array
    {
        $ret = parent::getQueryParams($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbObjectIDOrIDs);
        $vars = \PoP\ComponentModel\Engine_Vars::getVars();

        // If data is not to be loaded, then "stop-fetching" as to not show the Load More button
        if ($data_properties[GD_DATALOAD_SKIPDATALOAD] || $data_properties[GD_DATALOAD_DATASOURCE] != POP_DATALOAD_DATASOURCE_MUTABLEONREQUEST) {
            return $ret;
        }

        $query_args = $data_properties[GD_DATALOAD_QUERYARGS];

        if ($limit = $query_args[GD_URLPARAM_LIMIT]) {
            $ret[GD_URLPARAM_LIMIT] = $limit;
        }

        $pagenumber = $query_args[GD_URLPARAM_PAGENUMBER];
        $nextpaged = '';
        if (!PoP_BaseCollectionData_Utils::stopFetching($dbObjectIDOrIDs, $data_properties)) {
            // When loading latest, we need to return the same $pagenumber as we got, because it must not alter the params
            $nextpagenumber = ($vars['loading-latest']) ? $pagenumber : $pagenumber + 1;
        }
        $ret[GD_URLPARAM_PAGENUMBER] = $nextpagenumber;
        
        return $ret;
    }
    
    // function getUniquetodomainQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids) {
    
    //     $ret = parent::getUniquetodomainQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

    //     // Needed to loadLatest, to know from what time to get results
    //     $ret[GD_URLPARAM_TIMESTAMP] = POP_CONSTANT_CURRENTTIMESTAMP;

    //     // If data is not to be loaded, then "stop-fetching" as to not show the Load More button
    //     if ($data_properties[GD_DATALOAD_SKIPDATALOAD]) {

    //         $ret[GD_URLPARAM_STOPFETCHING] = true;
    //         return $ret;
    //     }
        
    //     // If it is lazy load, no need to calculate pagenumber / stop-fetching / etc
    //     if ($data_properties[GD_DATALOAD_LAZYLOAD]) {

    //         return $ret;
    //     }

    //     // If loading static data, then that's it
    //     if ($data_properties[GD_DATALOAD_DATASOURCE] != POP_DATALOAD_DATASOURCE_MUTABLEONREQUEST) {

    //         return $ret;
    //     }

    //     $query_args = $data_properties[GD_DATALOAD_QUERYARGS];
    //     $pagenumber = $query_args[GD_URLPARAM_PAGENUMBER];
    //     $stop_loading = PoP_BaseCollectionData_Utils::stopFetching($dbobjectids, $data_properties);
        
    //     $ret[GD_URLPARAM_STOPFETCHING] = $stop_loading;

    //     // When loading latest, we need to return the same $pagenumber as we got, because it must not alter the params
    //     $nextpaged = $vars['loading-latest'] ? $pagenumber : $pagenumber + 1;
    //     $ret[GD_DATALOAD_PARAMS][GD_URLPARAM_PAGENUMBER] = $stop_loading ? '' : $nextpaged;

    //     // Do not send this value back when doing loadLatest, or it will mess up the original structure loading
    //     // Doing 'unset' as to also take it out if an ancestor class (eg: GD_DataLoad_BlockQueryHandler) has set it
    //     if ($vars['loading-latest']) {

    //         unset($ret[GD_URLPARAM_STOPFETCHING]);
    //     }
        
    //     return $ret;
    // }

    // function getSharedbydomainsQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids) {
    
    //     $ret = parent::getSharedbydomainsQuerystate($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

    //     $query_args = $data_properties[GD_DATALOAD_QUERYARGS];

    //     $limit = $query_args[GD_URLPARAM_LIMIT];
    //     $ret[GD_DATALOAD_PARAMS][GD_URLPARAM_LIMIT] = $limit;

    //     return $ret;
    // }

    // function getDatafeedback($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids) {
    
    //     $ret = parent::getDatafeedback($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

    //     $query_args = $data_properties[GD_DATALOAD_QUERYARGS];

    //     $limit = $query_args[GD_URLPARAM_LIMIT];
    //     $ret[GD_DATALOAD_PARAMS][GD_URLPARAM_LIMIT] = $limit;

    //     // If it is lazy load, no need to calculate show-msg / pagenumber / stop-fetching / etc
    //     if ($data_properties[GD_DATALOAD_LAZYLOAD]) {

    //         return $ret;
    //     }

    //     $pagenumber = $query_args[GD_URLPARAM_PAGENUMBER];

    //     // Print feedback messages always, if none then an empty array
    //     $msgs = array();

    //     // Show error message if no items, but only if the checkpoint did not fail
    //     $checkpoint_failed = \PoP\ComponentModel\GeneralUtils::isError($dataaccess_checkpoint_validation);
    //     if (!$checkpoint_failed) {
    //         if (empty($dbobjectids)) {

    //             // Do not show the message when doing loadLatest
    //             if (!$vars['loading-latest']) {
                
    //                 // If pagenumber < 2 => There are no results at all
    //                 $msgs[] = array(
    //                     'codes' => array(
    //                         ($pagenumber < 2) ? 'noresults' : 'nomore',
    //                     ),
    //                     GD_JS_CLASS => 'alert-warning',
    //                 );
    //             }
    //         }
    //     }
    //     $ret['msgs'] = $msgs;

    //     // stop-fetching is loaded twice: in the params and in the feedback. This is because we can't access the params from the .tmpl files
    //     // (the params object is created only when initializing JS => after rendering the html with Handlebars so it's not available by then)
    //     // and this value is needed in fetchmore.tmpl
    //     $stop_loading = PoP_BaseCollectionData_Utils::stopFetching($dbobjectids, $data_properties);
        
    //     $ret[GD_URLPARAM_STOPFETCHING] = $stop_loading;

    //     // Add the Fetch more link for the Search Engine
    //     if (!$stop_loading && $data_properties[GD_DATALOAD_SOURCE]) {

    //         $ret[POP_IOCONSTANT_QUERYNEXTURL] = add_query_arg(GD_URLPARAM_PAGENUMBER, $pagenumber+1, $data_properties[GD_DATALOAD_SOURCE]);
    //     }

    //     // Do not send this value back when doing loadLatest, or it will mess up the original structure loading
    //     // Doing 'unset' as to also take it out if an ancestor class (eg: GD_DataLoad_BlockQueryHandler) has set it
    //     if ($vars['loading-latest']) {

    //         unset($ret[GD_URLPARAM_STOPFETCHING]);
    //     }

    //     return $ret;
    // }
}
    
/**
 * Initialize
 */
new GD_DataLoad_QueryHandler_List();
