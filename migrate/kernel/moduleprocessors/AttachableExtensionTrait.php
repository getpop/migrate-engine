<?php
namespace PoP\Engine;

trait AttachableExtensionTrait
{
    /**
     * It is represented through a static class, because the extensions work at class level, not object level
     */
    public static function getClassesToAttachTo()
    {
        return [];
    }

    public static function attach()
    {
        $attachableExtensionManager = AttachableExtensionManagerFactory::getInstance();
        $extensionClass = get_called_class();
        foreach ($extensionClass::getClassesToAttachTo() as $attachableClass) {
            $attachableExtensionManager->addExtensionClass(
                $attachableClass,
                $extensionClass
            );
        }
    }
}
