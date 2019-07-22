<?php
namespace PoP\Engine;

class ConvertibleFieldValueResolverResolverManager
{
    use RelationshipManagerTrait;

    public function __construct()
    {
        ConvertibleFieldValueResolverResolverManagerFactory::setInstance($this);
    }
}
    
/**
 * Initialize
 */
new ConvertibleFieldValueResolverResolverManager();

