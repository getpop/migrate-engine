<?php
namespace PoP\Engine;

trait DataloaderAPITrait
{
    public function maybeFilterDataloadQueryArgs(array &$query, array $options = [], $defaultFilterDataloadingModule = null)
    {
        // Accept field atts to filter the API fields
        $vars = \PoP\Engine\Engine_Vars::getVars();
        if (!\PoP\Engine\Server\Utils::disableAPI() && $vars['scheme'] == POP_SCHEME_API) {
            if ($filterDataloadQueryArgsParams = $options['filter-dataload-query-args']) {
                if ($filterDataloadQueryArgsSource = $filterDataloadQueryArgsParams['source']) {
                    if ($filterDataloadingModule = $filterDataloadQueryArgsParams['module'] ?? $defaultFilterDataloadingModule) {
                        $moduleprocessor_manager = ModuleProcessorManagerFactory::getInstance();
                        $moduleprocessor_manager->getProcessor($filterDataloadingModule)->filterHeadmoduleDataloadQueryArgs($filterDataloadingModule, $query, $filterDataloadQueryArgsSource);
                    }
                }
            }
        }
    }
}
