<?php
namespace PoP\Engine;

class APIUtils
{
    public static function getEndpoint($url, $dataoutputitems = null)
    {
        $cmsenginehelpers = \PoP\Engine\HelperAPIFactory::getInstance();
        $dataoutputitems = $dataoutputitems ?? [
            GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA,
            GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
            GD_URLPARAM_DATAOUTPUTITEMS_DATASETMODULESETTINGS,
        ];
        $endpoint = $cmsenginehelpers->addQueryArgs([
            GD_URLPARAM_SCHEME => POP_SCHEME_API,
            GD_URLPARAM_OUTPUT => GD_URLPARAM_OUTPUT_JSON,
            GD_URLPARAM_DATAOUTPUTMODE => GD_URLPARAM_DATAOUTPUTMODE_COMBINED,
            // GD_URLPARAM_DATABASESOUTPUTMODE => GD_URLPARAM_DATABASESOUTPUTMODE_COMBINED,
            GD_URLPARAM_DATAOUTPUTITEMS => implode(
                POP_CONSTANT_PARAMVALUE_SEPARATOR,
                $dataoutputitems
            ),
        ], $url);

        if ($mangled = $_REQUEST[GD_URLPARAM_MANGLED]) {
            $endpoint = $cmsenginehelpers->addQueryArgs([
                GD_URLPARAM_MANGLED => $mangled,
            ], $endpoint);
        }

        return $endpoint;
    }
}
