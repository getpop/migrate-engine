<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

class DataQuery_HookBase
{
    public function __construct()
    {
        $name = $this->getDataqueryName();
        HooksAPIFacade::getInstance()->addFilter('Dataquery:'.$name.':allowedfields', array($this, 'addAllowedFields'));
        HooksAPIFacade::getInstance()->addFilter('Dataquery:'.$name.':rejectedfields', array($this, 'addRejectedFields'));
        HooksAPIFacade::getInstance()->addFilter('Dataquery:'.$name.':allowedlayouts', array($this, 'addAllowedLayouts'));
        HooksAPIFacade::getInstance()->addFilter('Dataquery:'.$name.':nocachefields', array($this, 'addNocacheFields'));
        HooksAPIFacade::getInstance()->addFilter('Dataquery:'.$name.':lazylayouts', array($this, 'addLazyLayouts'));
    }
    public function getDataqueryName()
    {
        return '';
    }
    public function addAllowedFields($allowedfields)
    {
        return array_unique(
            array_merge(
                $allowedfields,
                $this->getAllowedFields()
            )
        );
    }
    public function addRejectedFields($rejectedfields)
    {
        return array_unique(
            array_merge(
                $rejectedfields,
                $this->getRejectedFields()
            )
        );
    }
    public function addAllowedLayouts($allowedlayouts)
    {
        return array_unique(
            array_merge(
                $allowedlayouts,
                $this->getAllowedLayouts()
            ),
            SORT_REGULAR
        );
    }
    public function addNocacheFields($nocachefields)
    {
        return array_merge(
            $nocachefields,
            $this->getNoCacheFields()
        );
    }
    public function addLazyLayouts($lazylayouts)
    {
        return array_merge(
            $lazylayouts,
            $this->getLazyLayouts()
        );
    }

    public function getAllowedFields()
    {
        return $this->getNoCacheFields();
    }
    public function getRejectedFields()
    {
        return array();
    }
    public function getAllowedLayouts()
    {
        $allowedlayouts = array();
        foreach ($this->getLazyLayouts() as $field => $lazylayouts) {
            foreach ($lazylayouts as $key => $layout) {
                $allowedlayouts[] = $layout;
            }
        }
        return array_unique(
            $allowedlayouts,
            SORT_REGULAR
        );
    }
    

    /**
     * Implement functions below in the hook implementation
     */
    public function getNoCacheFields()
    {
        return array();
    }
    public function getLazyLayouts()
    {
        return array();
    }
}
