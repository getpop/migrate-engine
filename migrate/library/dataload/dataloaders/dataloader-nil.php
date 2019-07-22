<?php
namespace PoP\Engine;

class Dataloader_Nil extends \PoP\Engine\QueryDataDataloader
{
    public function getFieldValueResolverClass()
    {
    	return null;
    }

    public function getDatabaseKey()
    {
        return null;
    }
}

