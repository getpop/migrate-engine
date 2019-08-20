<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Engine\Managers\ModuleFilterManager;
use PoP\Engine\ModuleFilters\ModulePaths;
use PoP\Engine\ModuleFilters\HeadModule;

class Utils
{
    public static $errors = array();

    public static function getDomainId($domain)
    {

        // The domain ID is simply removing the scheme, and replacing all dots with '-'
        // It is needed to assign an extra class to the event
        $domain_id = str_replace('.', '-', removeScheme($domain));

        // Allow to override the domainId, to unify DEV and PROD domains
        return HooksAPIFacade::getInstance()->applyFilters('pop_modulemanager:domain_id', $domain_id, $domain);
    }

    public static function isSearchEngine()
    {
        return HooksAPIFacade::getInstance()->applyFilters('\PoP\Engine\Utils:isSearchEngine', false);
    }

    // // public static function getCheckpointConfiguration($page_id = null) {

    // //     return Settings\SettingsManagerFactory::getInstance()->getCheckpointConfiguration($page_id);
    // // }
    // public static function getCheckpoints($page_id = null) {

    //     return Settings\SettingsManagerFactory::getInstance()->getCheckpoints($page_id);
    // }

    // public static function isServerAccessMandatory($checkpoint_configuration) {

    //     // The Static type can be cached since it contains no data
    //     $dynamic_types = array(
    //         GD_DATALOAD_VALIDATECHECKPOINTS_TYPE_DATAFROMSERVER,
    //     );
    //     $mandatory = in_array($checkpoint_configuration['type'], $dynamic_types);

    //     // Allow to add 'requires-user-state' by PoP UserState dependency
    //     return HooksAPIFacade::getInstance()->applyFilters(
    //         '\PoP\Engine\Utils:isServerAccessMandatory',
    //         $mandatory,
    //         $checkpoint_configuration
    //     );
    // }

    // public static function checkpointValidationRequired($checkpoint_configuration) {

    //     return true;
    //     // $type = $checkpoint_configuration['type'];
    //     // return (doingPost() && $type == GD_DATALOAD_VALIDATECHECKPOINTS_TYPE_STATIC) || $type == GD_DATALOAD_VALIDATECHECKPOINTS_TYPE_DATAFROMSERVER || $type == GD_DATALOAD_VALIDATECHECKPOINTS_TYPE_STATELESS;
    // }

    public static function limitResults($results)
    {

        // Cut results if more than 4 times the established limit. This is to protect from hackers adding all post ids.
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $limit = 4 * $cmsengineapi->getOption(\PoP\LooseContracts\NameResolverFactory::getInstance()->getName('popcms:option:limit'));
        if (count($results) > $limit) {
            array_splice($results, $limit);
        }

        return $results;
    }

    public static function getRouteURL($route)
    {
        // For the route, the ID is the URI applied on the homeURL instead of the domain
        // (then, the id for domain.com/en/slug/ is "slug" and not "en/slug")
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $cmsenginehelpers = \PoP\Engine\HelperAPIFactory::getInstance();
        $homeurl = $cmsenginehelpers->maybeAddTrailingSlash($cmsengineapi->getHomeURL());
        return $homeurl.$route.'/';
    }
    public static function getRouteTitle($route)
    {
        $title = HooksAPIFacade::getInstance()->applyFilters(
            'route:title',
            $route,
            $route
        );
        return $title;
    }
    // public static function getCurrentPath()
    // {
    //     // For the route, the ID is the URI applied on the homeURL instead of the domain
    //     // (then, the id for domain.com/en/slug/ is "slug" and not "en/slug")
    //     $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
    //     $homeurl = $cmsengineapi->getHomeURL();
    //     $path = substr(self::getCurrentUrl(), strlen($homeurl));
    //     $params_pos = strpos($path, '?');
    //     if ($params_pos !== false) {
    //         $path = substr($path, 0, $params_pos);
    //     }
    //     return trim($path, '/');
    // }
    public static function getCurrentUrl()
    {

        // Strip the Target and Output off it, users don't need to see those
        $remove_params = HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Engine\Utils:current_url:remove_params',
            array(
                GD_URLPARAM_SETTINGSFORMAT,
                GD_URLPARAM_VERSION,
                GD_URLPARAM_TARGET,
                ModuleFilterManager::URLPARAM_MODULEFILTER,
                ModulePaths::URLPARAM_MODULEPATHS,
                HeadModule::URLPARAM_HEADMODULE,
                GD_URLPARAM_ACTIONPATH,
                GD_URLPARAM_DATAOUTPUTITEMS,
                GD_URLPARAM_DATASOURCES,
                GD_URLPARAM_DATAOUTPUTMODE,
                GD_URLPARAM_DATABASESOUTPUTMODE,
                GD_URLPARAM_OUTPUT,
                GD_URLPARAM_DATASTRUCTURE,
                GD_URLPARAM_MANGLED,
                GD_URLPARAM_EXTRAROUTES,
                GD_URLPARAM_ACTION, // Needed to remove ?action=preload, ?action=loaduserstate, ?action=loadlazy
                GD_URLPARAM_STRATUM,
            )
        );
        $cmsenginehelpers = \PoP\Engine\HelperAPIFactory::getInstance();
        $url = $cmsenginehelpers->removeQueryArgs($remove_params, fullUrl());

        // Allow plug-ins to do their own logic to the URL
        $url = HooksAPIFacade::getInstance()->applyFilters('\PoP\Engine\Utils:getCurrentUrl', $url);

        return urldecode($url);
    }

    public static function getURLPath()
    {
        // Allow to remove the language information from qTranslate (https://domain.com/en/...)
        $route = HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Routing:uri-route',
            $_SERVER['REQUEST_URI']
        );
        $params_pos = strpos($route, '?');
        if ($params_pos !== false) {
            $route = substr($route, 0, $params_pos);
        }
        return trim($route, '/');
    }

    public static function getFieldName(string $field): string
    {
        $pos = strpos($field, POP_CONSTANT_FIELDATTS_START);
        if ($pos !== false) {
            return substr($field, 0, $pos);
        }
        return $field;
    }

    public static function getFieldAtts(string $field, ?array $variables = null): array
    {
        // Variables: allow to pass a field argument "key:$input", and then resolve it as ?variable[input]=value
        // Expected input is similar to GraphQL: https://graphql.org/learn/queries/#variables
        // If not passed the variables parameter, use $_REQUEST["variables"] by default
        $variables = $variables ?? $_REQUEST['variables'] ?? [];
        // We check that the format is "$fieldName($prop1;$prop2;...;$propN)"
        if (substr($field, -1*strlen(POP_CONSTANT_FIELDATTS_END)) == POP_CONSTANT_FIELDATTS_END) {
            $pos = strpos($field, POP_CONSTANT_FIELDATTS_START);
            if ($pos !== false) {
                $fieldAtts = [];
                $attsStr = substr($field, $pos+strlen(POP_CONSTANT_FIELDATTS_START), -1*(strlen(POP_CONSTANT_FIELDATTS_END)));
                foreach (explode(POP_CONSTANT_FIELDATTS_ATTSEPARATOR, $attsStr) as $attStr) {
                    $attParts = explode(POP_CONSTANT_FIELDATTS_ATTKEYVALUESEPARATOR, $attStr);
                    $fieldAttKey = $attParts[0];
                    $fieldAttValue = $attParts[1];
                    // The value may be a variable, if it starts with "$". Then, retrieve the actual value from the request
                    if ($fieldAttValue and substr($fieldAttValue, 0, 1) == POP_CONSTANT_FIELDATTS_ATTVARIABLEPREFIX) {
                        $fieldAttValue = $variables[substr($fieldAttValue, 1)];
                    }
                    $fieldAtts[$fieldAttKey] = $fieldAttValue;
                }
                return $fieldAtts;
            }
        }
        return [];
    }

    public static function listField(string $field): array
    {
        return [self::getFieldName($field), self::getFieldAtts($field)];
    }

    public static function getField(string $fieldName, array $fieldAtts): string
    {
        $items = [];
        foreach ($fieldAtts as $key => $value) {
            $items[] = $key.POP_CONSTANT_FIELDATTS_ATTKEYVALUESEPARATOR.$value;
        }
        return $fieldName.POP_CONSTANT_FIELDATTS_START.implode(POP_CONSTANT_FIELDATTS_ATTSEPARATOR, $items).POP_CONSTANT_FIELDATTS_END;
    }

    public static function maybeConvertDotNotationToArray($dotNotationOrArray)
    {
        if (is_array($dotNotationOrArray)) {
            return $dotNotationOrArray;
        }

        return self::convertDotNotationToArray($dotNotationOrArray);
    }

    public static function convertDotNotationToArray(string $dotNotation)
    {
        // If it is a string, split the items with ',', the inner items with '.', and the inner fields with '|'
        $fields = array();
        $pointer = &$fields;

        // Split the items by ","
        foreach (explode(POP_CONSTANT_PARAMVALUE_SEPARATOR, $dotNotation) as $commafields) {
            // For each item, advance to the last level by following the "."
            $dotfields = explode(POP_CONSTANT_DOTSYNTAX_DOT, $commafields);
            for ($i = 0; $i < count($dotfields)-1; $i++) {
                $pointer[$dotfields[$i]] = $pointer[$dotfields[$i]] ?? array();
                $pointer = &$pointer[$dotfields[$i]];
            }

            // The last level can contain several fields, separated by "|"
            $pipefields = $dotfields[count($dotfields)-1];
            foreach (explode(POP_CONSTANT_PARAMFIELD_SEPARATOR, $pipefields) as $pipefield) {
                $pointer[] = $pipefield;
            }
            $pointer = &$fields;
        }

        return $fields;
    }

    public static function getFramecomponentModules()
    {
        return HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Engine\Utils:getFramecomponentModules',
            array()
        );
    }

    public static function addRoute($url, $route)
    {
        $cmsenginehelpers = \PoP\Engine\HelperAPIFactory::getInstance();
        return $cmsenginehelpers->addQueryArgs([GD_URLPARAM_ROUTE => $route], $url);
    }

    public static function getPagePath($page_id)
    {
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $cmspagesapi = \PoP\Pages\FunctionAPIFactory::getInstance();

        // Generate the page path. Eg: http://mesym.com/events/past/ will render events/past
        $permalink = $cmspagesapi->getPageURL($page_id);

        $domain = $cmsengineapi->getHomeURL();

        // Remove the domain from the permalink => page path
        $page_path = substr($permalink, strlen($domain));

        // Remove the first and last '/'
        $page_path = trim($page_path, '/');

        return $page_path;
    }

    public static function getDatastructureFormatter()
    {
        $vars = Engine_Vars::getVars();

        $datastructureformat_manager = DataStructureFormatManagerFactory::getInstance();
        return $datastructureformat_manager->getDatastructureFormatter($vars['datastructure']);
    }

    public static function fetchingSite()
    {
        $vars = Engine_Vars::getVars();
        return $vars['fetching-site'];
    }

    public static function loadingSite()
    {

        // If we are doing JSON (or any other output) AND we setting the target, then we're loading content dynamically and we need it to be JSON
        // Otherwise, it is the first time loading website => loadingSite
        $vars = Engine_Vars::getVars();
        return $vars['loading-site'];
    }

    public static function isRoute($route_or_routes)
    {
        $vars = Engine_Vars::getVars();
        $route = $vars['route'];
        if (is_array($route_or_routes)) {
            return in_array($route, $route_or_routes);
        }

        return $route == $route_or_routes;
    }
}
