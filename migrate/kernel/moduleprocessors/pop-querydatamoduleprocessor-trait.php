<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Engine\Facades\InstanceManagerFacade;

trait QueryDataModuleProcessorTrait
{
    protected function getImmutableDataloadQueryArgs(array $module, array &$props)
    {
        return array();
    }
    protected function getMutableonrequestDataloadQueryArgs(array $module, array &$props)
    {
        return array();
    }
    public function getQueryhandler(array $module)
    {
        return GD_DATALOAD_QUERYHANDLER_ACTIONEXECUTION;
    }
    // public function getFilter(array $module)
    // {
    //     return null;
    // }

    public function getImmutableHeaddatasetmoduleDataProperties(array $module, array &$props)
    {
        $ret = parent::getImmutableHeaddatasetmoduleDataProperties($module, $props);

        // Attributes to pass to the query
        $ret[GD_DATALOAD_QUERYARGS] = $this->getImmutableDataloadQueryArgs($module, $props);

        return $ret;
    }

    public function getQueryArgsFilteringModules(array $module, array &$props)
    {
        // Attributes overriding the dataloader args, taken from the request
        return [
            $module,
        ];
    }

    public function getMutableonmodelHeaddatasetmoduleDataProperties(array $module, array &$props)
    {
        $ret = parent::getMutableonmodelHeaddatasetmoduleDataProperties($module, $props);

        // Attributes overriding the dataloader args, taken from the request
        if (!$ret[GD_DATALOAD_IGNOREREQUESTPARAMS]) {
            $ret[GD_DATALOAD_QUERYARGSFILTERINGMODULES] = $this->getQueryArgsFilteringModules($module, $props);
        }

        // // Set the filter if it has one
        // if ($filter = $this->getFilter($module)) {
        //     $ret[GD_DATALOAD_FILTER] = $filter;
        // }

        return $ret;
    }
    public function filterHeadmoduleDataloadQueryArgs(array $module, array &$query, array $source = null)
    {
        if ($active_filterqueryargs_modules = $this->getActiveDataloadQueryArgsFilteringModules($module, $source)) {
            $moduleprocessor_manager = ModuleProcessorManagerFactory::getInstance();
            global $pop_filterinputprocessor_manager;
            foreach ($active_filterqueryargs_modules as $submodule) {

                $submodule_processor = $moduleprocessor_manager->getProcessor($submodule);
                $value = $submodule_processor->getValue($submodule, $source);
                if ($filterInput = $submodule_processor->getFilterInput($submodule)) {
                    $pop_filterinputprocessor_manager->getProcessor($filterInput)->filterDataloadQueryArgs($filterInput, $query, $value);
                }
            }
        }
    }

    public function getActiveDataloadQueryArgsFilteringModules(array $module, array $source = null)
    {
        // Search for cached result
        $cacheKey = json_encode($source ?? []);
        $this->activeDataloadQueryArgsFilteringModules[$cacheKey] = $this->activeDataloadQueryArgsFilteringModules[$cacheKey] ?? [];
        if (!is_null($this->activeDataloadQueryArgsFilteringModules[$cacheKey][$module[1]])) {
            return $this->activeDataloadQueryArgsFilteringModules[$cacheKey][$module[1]];
        }
        // if ($this instanceof \PoP\Engine\DataloadingModule) {

        $modules = [];
        $moduleprocessor_manager = ModuleProcessorManagerFactory::getInstance();
        // if ($filter_module = $this->getFilterSubmodule($module)) {
        // Check if the module has any filtercomponent
        if ($filterqueryargs_modules = array_filter(
            // $moduleprocessor_manager->getProcessor($filter_module)->getDatasetmoduletreeSectionFlattenedModules($filter_module),
            $this->getDatasetmoduletreeSectionFlattenedModules($module),
            function($module) use($moduleprocessor_manager) {
                return $moduleprocessor_manager->getProcessor($module) instanceof \PoP\Engine\DataloadQueryArgsFilter;
            }
        )) {
            // Check if if we're currently filtering by any filtercomponent
            $modules = array_filter(
                $filterqueryargs_modules,
                function($module) use($moduleprocessor_manager, $source) {
                    return !is_null($moduleprocessor_manager->getProcessor($module)->getValue($module, $source));
                }
            );
        }

        $this->activeDataloadQueryArgsFilteringModules[$cacheKey][$module[1]] = $modules;
        return $modules;
    }

    public function getMutableonrequestHeaddatasetmoduleDataProperties(array $module, array &$props)
    {
        $ret = parent::getMutableonrequestHeaddatasetmoduleDataProperties($module, $props);

        $ret[GD_DATALOAD_QUERYARGS] = $this->getMutableonrequestDataloadQueryArgs($module, $props);

        return $ret;
    }

    public function getDbobjectIds(array $module, array &$props, &$data_properties)
    {
        $instanceManager = InstanceManagerFacade::getInstance();

        // Prepare the Query to get data from the DB
        $datasource = $data_properties[GD_DATALOAD_DATASOURCE];
        if ($datasource == POP_DATALOAD_DATASOURCE_MUTABLEONREQUEST && !$data_properties[GD_DATALOAD_IGNOREREQUESTPARAMS]) {
            // Merge with $_REQUEST, so that params passed through the URL can be used for the query (eg: ?limit=5)
            // But whitelist the params that can be taken, to avoid hackers peering inside the system and getting custom data (eg: params "include", "post-status" => "draft", etc)
            $whitelisted_params = HooksAPIFacade::getInstance()->applyFilters(
                'QueryDataModuleProcessorTrait:request:whitelisted_params',
                array(
                    GD_URLPARAM_REDIRECTTO,
                    GD_URLPARAM_PAGENUMBER,
                    GD_URLPARAM_LIMIT,
                    // Used for the Comments to know what post to fetch comments from when filtering
                    GD_URLPARAM_COMMENTPOSTID,
                )
            );
            $params_from_request = array_filter(
                $_REQUEST,
                function ($param) use ($whitelisted_params) {
                    return in_array($param, $whitelisted_params);
                },
                ARRAY_FILTER_USE_KEY
            );

            // Handle special cases
            // Avoid users querying all results (by passing limit=-1 or limit=0)
            $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
            if (isset($params_from_request[GD_URLPARAM_LIMIT])) {
                $limit = intval($params_from_request[GD_URLPARAM_LIMIT]);
                if ($limit === -1 || $limit === 0) {
                    $params_from_request[GD_URLPARAM_LIMIT] = $cmsengineapi->getOption(\PoP\LooseContracts\NameResolverFactory::getInstance()->getName('popcms:option:limit'));
                }
            }
            $params_from_request = HooksAPIFacade::getInstance()->applyFilters(
                'QueryDataModuleProcessorTrait:request:filter_params',
                $params_from_request
            );

            // Finally merge it into the data properties
            $data_properties[GD_DATALOAD_QUERYARGS] = array_merge(
                $data_properties[GD_DATALOAD_QUERYARGS],
                $params_from_request
            );
        }

        if ($queryhandler_name = $this->getQueryhandler($module)) {
            // Allow the queryhandler to override/normalize the query args
            $queryhandler_manager = QueryHandlerManagerFactory::getInstance();
            $queryhandler = $queryhandler_manager->get($queryhandler_name);
            $queryhandler->prepareQueryArgs($data_properties[GD_DATALOAD_QUERYARGS]);
        }

        $dataloader = $instanceManager->getInstance($this->getDataloaderClass($module));
        return $dataloader->getDbobjectIds($data_properties);
    }

    public function getDatasetmeta(array $module, array &$props, array $data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids)
    {
        $ret = parent::getDatasetmeta($module, $props, $data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids);

        if ($queryhandler_name = $this->getQueryhandler($module)) {
            $queryhandler_manager = QueryHandlerManagerFactory::getInstance();
            $queryhandler = $queryhandler_manager->get($queryhandler_name);

            if ($query_state = $queryhandler->getQueryState($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids)) {
                $ret['querystate'] = $query_state;
            }
            if ($query_params = $queryhandler->getQueryParams($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids)) {
                $ret['queryparams'] = $query_params;
            }
            if ($query_result = $queryhandler->getQueryResult($data_properties, $dataaccess_checkpoint_validation, $actionexecution_checkpoint_validation, $executed, $dbobjectids)) {
                $ret['queryresult'] = $query_result;
            }
        }

        return $ret;
    }
}
