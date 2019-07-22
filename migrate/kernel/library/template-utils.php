<?php
namespace PoP\Engine;

class TemplateUtils
{
    public static function validatePopLoaded($json = false)
    {

        // If the theme was not loaded because some depended-upon plugin was not loaded, then do nothing, just show an error
        if (!popLoaded()) {
            if ($json) {
                header('Content-type: application/json');
                echo json_encode(
                    array(
                        'error' => POP_MSG_STARTUPERROR,
                    )
                );
            } else {
                echo POP_MSG_STARTUPERROR;
            }
            exit;
        }
    }

    public static function maybeRedirect()
    {
        if ($redirect = Settings\SettingsManagerFactory::getInstance()->getRedirectUrl()) {
            if ($query = $_SERVER['QUERY_STRING']) {
                $redirect .= '?'.$query;
            }

            $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
            $cmsengineapi->redirect($redirect);
            exit;
        }
    }

    public static function generateData()
    {
        $engine = EngineFactory::getInstance();
        $engine->generateData();
    }
}
