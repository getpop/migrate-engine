<?php
namespace PoP\Engine;

class QueryHandlerManager
{
    public $queryhandlers;
    
    public function __construct()
    {
        QueryHandlerManagerFactory::setInstance($this);
        return $this->queryhandlers = array();
    }
    
    public function add($name, $queryhandler)
    {
        $this->queryhandlers[$name] = $queryhandler;
    }
    
    public function get($name)
    {
        $queryhandler = $this->queryhandlers[$name];
        if (!$queryhandler) {
            throw new \Exception(sprintf('No QueryHandler with name \'%s\' (%s)', $name, fullUrl()));
        }

        return $queryhandler;
    }
}
    
/**
 * Initialize
 */
new QueryHandlerManager();
