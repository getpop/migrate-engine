<?php

// public const POP_ROUTE_DESCRIPTION = 'description';
// public const POP_ROUTE_AUTHORS = 'authors';
if (!defined('POP_ROUTE_DESCRIPTION')) {
    define('POP_ROUTE_DESCRIPTION', \PoP\Definitions\DefinitionUtils::getUniqueDefinition('description', POP_DEFINITIONGROUP_ROUTES));
}
if (!defined('POP_ROUTE_AUTHORS')) {
    define('POP_ROUTE_AUTHORS', \PoP\Definitions\DefinitionUtils::getUniqueDefinition('authors', POP_DEFINITIONGROUP_ROUTES));
}