<?php
namespace PoP\Engine;

interface FunctionAPI
{
    public function getOption($option, $default = false);
    public function redirect($url);
    public function getVersion();
    public function getHomeURL(): string;
    public function getSiteURL(): string;
    public function getHost(): string;
    public function isError($object): bool;
    public function getContentDir();
    public function getContentURL();
}
