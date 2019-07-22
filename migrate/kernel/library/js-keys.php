<?php

// Make sure each JS Key is unique
define('GD_JS_MODULE', \PoP\Engine\Server\Utils::compactResponseJsonKeys() ? 'm' : 'module');
define('GD_JS_SUBMODULES', \PoP\Engine\Server\Utils::compactResponseJsonKeys() ? 'ms' : 'submodules');
define('GD_JS_MODULEOUTPUTNAME', \PoP\Engine\Server\Utils::compactResponseJsonKeys() ? 's' : 'moduleoutputname');
