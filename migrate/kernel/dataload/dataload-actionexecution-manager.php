<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

class ActionExecutionManager
{
    private $results = [];
    
    public function __construct()
    {
        ActionExecutionManagerFactory::setInstance($this);

        HooksAPIFacade::getInstance()->addAction(
            'augmentVarsProperties',
            function() {
                $this->results = [];
            }
        );
    }

    public function setResult(string $class, $result)
    {
        $this->results[$class] = $result;
    }
    
    public function getResult(string $class)
    {
        return $this->results[$class];
    }
}
    
/**
 * Initialize
 */
new ActionExecutionManager();
