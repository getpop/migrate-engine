<?php
use PoP\ComponentModel\Facades\Engine\EngineFacade;

\PoP\ComponentModel\TemplateUtils::validatePopLoaded(true);
\PoP\ComponentModel\TemplateUtils::maybeRedirect();
\PoP\ComponentModel\TemplateUtils::generateData();

// Indicate that this is a json response in the HTTP Header
header('Content-type: application/json');

$engine = EngineFacade::getInstance();
// $engine->checkRedirect();
// $engine->generateData();
// // $engine->outputData();

$formatter = \PoP\ComponentModel\Utils::getDatastructureFormatter();
echo json_encode($engine->getOutputData(), $formatter->getJsonEncodeType());

// // Allow extra functionalities. Eg: Save the logged-in user meta information
// $engine->triggerOutputdataHooks();
