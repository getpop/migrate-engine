<?php
use PoP\Hooks\Facades\HooksAPIFacade;

function getDomain($url)
{
    $parse = parse_url($url);
    return $parse['scheme'].'://'.$parse['host'];
}
function removeScheme($domain)
{
    $arr = explode("//", $domain);
    return count($arr) == 1 ? $arr[0] : $arr[1];
}
function removeDomain($url)
{
    return substr($url, strlen(getDomain($url)));
}

/**
 * Return the requested full URL
 *
 * @param boolean $useHostRequestedByClient If true, get the host from user-provided HTTP_HOST, otherwise from the server-defined SERVER_NAME
 * @return string
 */
function fullUrl(bool $useHostRequestedByClient = false): string
{
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    /**
     * If accessing from Nginx, the server_name might point to localhost
     * instead of the actual server domain. So provide the change to use
     * the user-requested host
     *
     * @see https://stackoverflow.com/questions/2297403/what-is-the-difference-between-http-host-and-server-name-in-php
     */
    $host = $useHostRequestedByClient ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    return $protocol . "://" . $host . $port . $_SERVER['REQUEST_URI'];
}

function arrayFlatten(array $array, bool $firstLevelOnly = false)
{
    $return = array();
    if ($firstLevelOnly) {
        array_walk(
            $array,
            function ($a) use (&$return) {
                $return[] = $a;
            }
        );
    } else {
        array_walk_recursive(
            $array,
            function ($a) use (&$return) {
                $return[] = $a;
            }
        );
    }
    return $return;
}

function doingPost()
{
    return ('POST' == $_SERVER['REQUEST_METHOD']);
}

// Returns true if this is an Ajax call
function doingAjax()
{
    $doingAjax = defined('DOING_AJAX') && DOING_AJAX;
    return HooksAPIFacade::getInstance()->applyFilters('gd_doing_ajax', $doingAjax);
}

function multiexplode($delimiters, $string)
{
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}
