<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Engine\Facades\ModuleFilterManagerFacade;
use PoP\Engine\ModuleFilters\ModulePaths;
use PoP\Engine\Facades\ModulePathHelpersFacade;

class Engine_Vars
{
    public static $vars;

    public static function getModulePaths()
    {
        $ret = array();
        if ($paths = $_REQUEST[ModulePaths::URLPARAM_MODULEPATHS]) {
            if (!is_array($paths)) {
                $paths = array($paths);
            }

            // If any path is a substring from another one, then it is its root, and only this one will be taken into account, so remove its substrings
            // Eg: toplevel.pagesection-top is substring of toplevel, so if passing these 2 modulepaths, keep only toplevel
            // Check that the last character is ".", to avoid toplevel1 to be removed
            $paths = array_filter(
                $paths,
                function ($item) use ($paths) {
                    foreach ($paths as $path) {
                        if (strlen($item) > strlen($path) && strpos($item, $path) === 0 && $item[strlen($path)] == POP_CONSTANT_MODULESTARTPATH_SEPARATOR) {
                            return false;
                        }
                    }

                    return true;
                }
            );

            foreach ($paths as $path) {
                // Each path must be converted to an array of the modules
                $ret[] = ModulePathHelpersFacade::getInstance()->recastModulePath($path);
            }
        }

        return $ret;
    }

    public static function getVars()
    {
        if (self::$vars) {
            return self::$vars;
        }

        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();

        // Only initialize the first time. Then, it will call ->resetState() to retrieve new state, no need to create a new instance
        $cmsrouting = \PoP\Routing\CMSRoutingStateFactory::getInstance();
        $nature = $cmsrouting->getNature();
        $route = $cmsrouting->getRoute();

        // Convert them to lower to make it insensitive to upper/lower case values
        $output = strtolower($_REQUEST[GD_URLPARAM_OUTPUT]);
        $dataoutputitems = $_REQUEST[GD_URLPARAM_DATAOUTPUTITEMS];
        $datasources = strtolower($_REQUEST[GD_URLPARAM_DATASOURCES]);
        $datastructure = strtolower($_REQUEST[GD_URLPARAM_DATASTRUCTURE]);
        $dataoutputmode = strtolower($_REQUEST[GD_URLPARAM_DATAOUTPUTMODE]);
        $dboutputmode = strtolower($_REQUEST[GD_URLPARAM_DATABASESOUTPUTMODE]);
        $target = strtolower($_REQUEST[GD_URLPARAM_TARGET]);
        $mangled = Server\Utils::isMangled() ? '' : GD_URLPARAM_MANGLED_NONE;
        $action = strtolower($_REQUEST[GD_URLPARAM_ACTION]);
        $scheme = strtolower($_REQUEST[GD_URLPARAM_SCHEME]);

        $outputs = HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Engine\Engine_Vars:outputs',
            array(
                GD_URLPARAM_OUTPUT_HTML,
                GD_URLPARAM_OUTPUT_JSON,
            )
        );
        if (!in_array($output, $outputs)) {
            $output = GD_URLPARAM_OUTPUT_HTML;
        }

        // Target/Module default values (for either empty, or if the user is playing around with the url)
        $alldatasources = array(
            GD_URLPARAM_DATASOURCES_ONLYMODEL,
            GD_URLPARAM_DATASOURCES_MODELANDREQUEST,
        );
        if (!in_array($datasources, $alldatasources)) {
            $datasources = GD_URLPARAM_DATASOURCES_MODELANDREQUEST;
        }

        $dataoutputmodes = array(
            GD_URLPARAM_DATAOUTPUTMODE_SPLITBYSOURCES,
            GD_URLPARAM_DATAOUTPUTMODE_COMBINED,
        );
        if (!in_array($dataoutputmode, $dataoutputmodes)) {
            $dataoutputmode = GD_URLPARAM_DATAOUTPUTMODE_SPLITBYSOURCES;
        }

        $dboutputmodes = array(
            GD_URLPARAM_DATABASESOUTPUTMODE_SPLITBYDATABASES,
            GD_URLPARAM_DATABASESOUTPUTMODE_COMBINED,
        );
        if (!in_array($dboutputmode, $dboutputmodes)) {
            $dboutputmode = GD_URLPARAM_DATABASESOUTPUTMODE_SPLITBYDATABASES;
        }

        if ($dataoutputitems) {
            if (!is_array($dataoutputitems)) {
                $dataoutputitems = explode(POP_CONSTANT_PARAMVALUE_SEPARATOR, strtolower($dataoutputitems));
            } else {
                $dataoutputitems = array_map('strtolower', $dataoutputitems);
            }
        }
        $alldataoutputitems = HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Engine\Engine_Vars:dataoutputitems',
            array(
                GD_URLPARAM_DATAOUTPUTITEMS_META,
                GD_URLPARAM_DATAOUTPUTITEMS_DATASETMODULESETTINGS,
                GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA,
                GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
                GD_URLPARAM_DATAOUTPUTITEMS_SESSION,
            )
        );
        $dataoutputitems = array_intersect(
            $dataoutputitems ?? array(),
            $alldataoutputitems
        );
        if (!$dataoutputitems) {
            $dataoutputitems = HooksAPIFacade::getInstance()->applyFilters(
                '\PoP\Engine\Engine_Vars:default-dataoutputitems',
                array(
                    GD_URLPARAM_DATAOUTPUTITEMS_META,
                    GD_URLPARAM_DATAOUTPUTITEMS_DATASETMODULESETTINGS,
                    GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA,
                    GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
                    GD_URLPARAM_DATAOUTPUTITEMS_SESSION,
                )
            );
        }
        
        // If not target, or invalid, reset it to "main"
        // We allow an empty target if none provided, so that we can generate the settings cache when no target is provided
        // (ie initial load) and when target is provided (ie loading pageSection)
        $targets = HooksAPIFacade::getInstance()->applyFilters(
            '\PoP\Engine\Engine_Vars:targets',
            array(
                POP_TARGET_MAIN,
            )
        );
        if (!in_array($target, $targets)) {
            $target = POP_TARGET_MAIN;
        }

        $platformmanager = StratumManagerFactory::getInstance();
        $stratum = $platformmanager->getStratum();

        /**
         * Override values for the API mode!
         * Whenever doing ?scheme=api, the specific configuration below must be set in the vars
         */
        if (!Server\Utils::disableAPI() && $scheme == POP_SCHEME_API) {

            // For the API, the response is always JSON
            $output = GD_URLPARAM_OUTPUT_JSON;
        
            // Fetch datasetmodulesettings: needed to obtain the dbKeyPath to know where to find the database entries
            $dataoutputitems = [
                GD_URLPARAM_DATAOUTPUTITEMS_DATASETMODULESETTINGS,
                GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA,
                GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
            ];

            // dataoutputmode => Combined: there is no need to split the sources, then already combined them
            $dataoutputmode = GD_URLPARAM_DATAOUTPUTMODE_COMBINED;

            // dboutputmode => Combined: needed since we don't know under what database does the dbKeyPath point to. Then simply integrate all of them
            // Also, needed for REST/GraphQL APIs since all their data comes bundled all together
            $dboutputmode = GD_URLPARAM_DATABASESOUTPUTMODE_COMBINED;

            // Only the data stratum is needed
            $stratum = POP_STRATUM_DATA;
        }

        $strata = $platformmanager->getStrata($stratum);
        $stratum_isdefault = $platformmanager->isDefaultStratum();
        
        $modulefilter_manager = ModuleFilterManagerFacade::getInstance();
        $modulefilter = $modulefilter_manager->getSelectedModuleFilterName();
        
        $fetchingSite = is_null($modulefilter);
        $loadingSite = $fetchingSite && $output == GD_URLPARAM_OUTPUT_HTML;

        // Format: if not set, then use the default one: "settings-format" can be defined at the settings, and persists as the default option
        // Use 'format' the first time to set the 'settingsformat' value. So if "loadingSite()" is true, take the value from 'format'
        if ($loadingSite) {
            $settingsformat = strtolower($_REQUEST[GD_URLPARAM_FORMAT]);
        } else {
            $settingsformat = strtolower($_REQUEST[GD_URLPARAM_SETTINGSFORMAT]);
        }
        $format = isset($_REQUEST[GD_URLPARAM_FORMAT]) ? strtolower($_REQUEST[GD_URLPARAM_FORMAT]) : $settingsformat;
        // Comment Leo 13/11/2017: If there is not format, then set it to 'default'
        // This is needed so that the /generate/ generated configurations under a $model_instance_id (based on the value of $vars)
        // can match the same $model_instance_id when visiting that page
        if (!$format) {
            $format = POP_VALUES_DEFAULT;
        }
        self::$vars = array(
            'nature' => $nature,
            'route' => $route,
            'output' => $output,
            'modulefilter' => $modulefilter,
            'actionpath' => $_REQUEST[GD_URLPARAM_ACTIONPATH],
            'target' => $target,
            'dataoutputitems' => $dataoutputitems,
            'datasources' => $datasources,
            'datastructure' => $datastructure,
            'dataoutputmode' => $dataoutputmode,
            'dboutputmode' => $dboutputmode,
            'mangled' => $mangled,
            'format' => $format,
            'settingsformat' => $settingsformat,
            'action' => $action,
            'scheme' => $scheme,
            'loading-site' => $loadingSite,
            'fetching-site' => $fetchingSite,
            'stratum' => $stratum,
            'strata' => $strata,
            'stratum-isdefault' => $stratum_isdefault,
        );

        if (Server\Utils::enableConfigByParams()) {
            self::$vars['config'] = $_REQUEST[POP_URLPARAM_CONFIG];
        }

        // Set the routing state (eg: PoP Queried Object can add its information)
        self::$vars['routing-state'] = [];

        // Allow for plug-ins to add their own vars
        HooksAPIFacade::getInstance()->doAction(
            '\PoP\Engine\Engine_Vars:addVars',
            array(&self::$vars)
        );

        self::augmentVarsProperties();

        return self::$vars;
    }

    public static function augmentVarsProperties()
    {
        $nature = self::$vars['nature'];
        self::$vars['routing-state']['is-standard'] = $nature == POP_NATURE_STANDARD;
        self::$vars['routing-state']['is-home'] = $nature == POP_NATURE_HOME;
        self::$vars['routing-state']['is-404'] = $nature == POP_NATURE_404;

        HooksAPIFacade::getInstance()->doAction(
            'augmentVarsProperties',
            array(&self::$vars)
        );
    }
}
