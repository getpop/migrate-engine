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
        $engine = \PoP\Engine\EngineFactory::getInstance();
        $entry_module = $engine->getEntryModule();
        if ($moduleatts = $entry_module[2]) {
            if ($fields = $moduleatts['fields']) {
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
