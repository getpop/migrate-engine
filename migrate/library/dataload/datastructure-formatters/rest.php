<?php
namespace PoP\Engine\Impl;

define('GD_DATALOAD_DATASTRUCTURE_REST', 'rest');

class DataStructureFormatter_REST extends DataStructureFormatter_MirrorQuery
{
    public function getName()
    {
        return GD_DATALOAD_DATASTRUCTURE_REST;
    }
    
    protected function getFields()
    {
        // Get the fields from the entry module's module atts
        $engine = \PoP\ComponentModel\EngineFactory::getInstance();
        $entry_module = $engine->getEntryModule();
        if ($moduleAtts = $entry_module[2]) {
            if ($fields = $moduleAtts['fields']) {
                return $fields;
            }
        }
        
        return parent::getFields();
    }
}
    
/**
 * Initialize
 */
new DataStructureFormatter_REST();
