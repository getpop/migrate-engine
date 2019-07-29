<?php
namespace PoP\Engine\Server;

class Utils
{
    protected static $override_configuration;

    public static function init()
    {
        // Allow to override the configuration with values passed in the query string:
        // "config": comma-separated string with all fields with value "true"
        // Whatever fields are not there, will be considered "false"
        self::$override_configuration = array();
        if (self::enableConfigByParams()) {
            self::$override_configuration = $_REQUEST[POP_URLPARAM_CONFIG] ? explode(POP_CONSTANT_PARAMVALUE_SEPARATOR, $_REQUEST[POP_URLPARAM_CONFIG]) : array();
        }
    }

    public static function doingOverrideConfiguration()
    {
        return !empty(self::$override_configuration);
    }

    public static function getOverrideConfiguration($key)
    {
        // If no values where defined in the configuration, then skip it completely
        if (empty(self::$override_configuration)) {
            return null;
        }

        // Check if the key has been given value "true"
        if (in_array($key, self::$override_configuration)) {
            return true;
        }

        // Otherwise, it has value "false"
        return false;
    }

    public static function isMangled()
    {
        // By default, it is mangled, if not mangled then param "mangled" must have value "none"
        // Coment Leo 13/01/2017: getVars() can't function properly since it references objects which have not been initialized yet,
        // when called at the very beginning. So then access the request directly
        return !$_REQUEST[GD_URLPARAM_MANGLED] || $_REQUEST[GD_URLPARAM_MANGLED] != GD_URLPARAM_MANGLED_NONE;
    }

    public static function enableConfigByParams()
    {
        return isset($_ENV['ENABLE_CONFIG_BY_PARAMS']) ? strtolower($_ENV['ENABLE_CONFIG_BY_PARAMS']) == "true" : false;
    }

    public static function failIfSubcomponentDataloaderUndefined()
    {
        return isset($_ENV['FAIL_IF_SUBCOMPONENT_DATALOADER_IS_UNDEFINED']) ? strtolower($_ENV['FAIL_IF_SUBCOMPONENT_DATALOADER_IS_UNDEFINED']) == "true" : false;
    }

    public static function enableExtraRoutesByParams()
    {
        return isset($_ENV['ENABLE_EXTRA_ROUTES_BY_PARAMS']) ? strtolower($_ENV['ENABLE_EXTRA_ROUTES_BY_PARAMS']) == "true" : false;
    }

    /**
     * Use 'modules' or 'm' in the JS context. Used to compress the file size in PROD
     */
    public static function compactResponseJsonKeys()
    {
        // Do not compact if not mangled
        if (!self::isMangled()) {
            return false;
        }

        return isset($_ENV['COMPACT_RESPONSE_JSON_KEYS']) ? strtolower($_ENV['COMPACT_RESPONSE_JSON_KEYS']) == "true" : false;
    }

    public static function useCache()
    {
        // If we are overriding the configuration, then do NOT use the cache
        // Otherwise, parameters from the config have need to be added to $vars, however they can't,
        // since we want the $vars model_instance_id to not change when testing with the "config" param
        if (self::doingOverrideConfiguration()) {
            return false;
        }
        return isset($_ENV['USE_CACHE']) ? strtolower($_ENV['USE_CACHE']) == "true" : false;
    }

    public static function disableAPI()
    {
        return isset($_ENV['DISABLE_API']) ? strtolower($_ENV['DISABLE_API']) == "true" : false;
    }

    public static function externalSitesRunSameSoftware()
    {
        return isset($_ENV['EXTERNAL_SITES_RUN_SAME_SOFTWARE']) ? strtolower($_ENV['EXTERNAL_SITES_RUN_SAME_SOFTWARE']) == "true" : false;
    }

    public static function disableCustomCMSCode()
    {
        return isset($_ENV['DISABLE_CUSTOM_CMS_CODE']) ? strtolower($_ENV['DISABLE_CUSTOM_CMS_CODE']) == "true" : false;
    }
}

/**
 * Initialization
 */
Utils::init();
