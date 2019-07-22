<?php
namespace PoP\Engine;

interface HelperAPI
{
    public function addQueryArgs(array $key_values, string $url);
    public function removeQueryArgs(array $keys, string $url);
    public function maybeAddTrailingSlash(string $text);
    public function hash($data);
}