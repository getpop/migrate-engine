<?php
namespace PoP\Engine;

trait RelationshipManagerTrait
{
    private $relationships = [];
    
    public function addRelationship(string $key, string $value)
    {
        $this->relationships[$key][] = $value;
    }

    public function addRelationships(string $key, array $values)
    {
        $this->relationships[$key] = array_merge(
            $this->relationships[$key] ?? [],
            $values
        );
    }

    public function getRelationships(string $key)
    {
        return $this->relationships[$key] ?? [];
    }
}
