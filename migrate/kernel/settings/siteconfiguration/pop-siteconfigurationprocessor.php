<?php
namespace PoP\Engine\Settings;

class SiteConfigurationProcessorBase
{
    public function __construct()
    {
        SiteConfigurationProcessorManagerFactory::getInstance()->set($this);
    }

    public function getEntryModule(): ?array
    {
        return null;
    }
}
