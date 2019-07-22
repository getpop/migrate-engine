<?php
namespace PoP\Engine;

interface FunctionAPI
{
    public function getOption($option, $default = false);
    public function redirect($url);
    public function getVersion();
    public function getHomeURL();
    public function getSiteURL();
    public function isError($object);
    public function getContentDir();
    public function getContentURL();
}
