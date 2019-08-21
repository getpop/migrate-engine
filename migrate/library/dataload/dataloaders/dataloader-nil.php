<?php
namespace PoP\Engine;

class Dataloader_Nil extends \PoP\ComponentModel\QueryDataDataloader
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

