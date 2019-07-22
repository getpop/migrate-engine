<?php
namespace PoP\Engine;

class AttachableExtensionManager
{
    protected $extensionClasses = [];

    public function __construct()
    {
        AttachableExtensionManagerFactory::setInstance($this);
    }

    public function addExtensionClass(string $attachableClass, string $extensionClass): void {
        $this->extensionClasses[$attachableClass][] = $extensionClass;
    }

    public function getExtensionClasses(string $attachableClass): array {
        return $this->extensionClasses[$attachableClass] ?? [];
    }
}

/**
 * Initialization
 */
new AttachableExtensionManager();
