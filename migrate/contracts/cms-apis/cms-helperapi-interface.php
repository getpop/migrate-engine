<?php
namespace PoP\Engine;

interface HelperAPI
{
    public function addQueryArgs(array $key_values, string $url): string;
    public function removeQueryArgs(array $keys, string $url): string;
    public function maybeAddTrailingSlash(string $text): string;
    public function hash($data): string;
}
