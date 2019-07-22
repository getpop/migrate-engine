<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

abstract class DataQueryBase
{

    // Allow Plugins to inject extra properties. Eg: PoP User Login can inject loggedinuser-fields
    protected $properties;

    public function __construct()
    {
        $this->properties = array();
    
        $dataquery_manager = DataQueryManagerFactory::getInstance();
        $dataquery_manager->add($this->getName(), $this);
    }
    
    /**
     * Function to override
     */
    abstract public function getName();

    public function addProperty($name, $value)
    {
        $this->properties[$name] = array_merge(
            $this->properties[$name] ?? array(),
            $value
        );
    }

    public function getProperty($name)
    {
        return $this->properties[$name];
    }
    
    /**
     * Function to override
     */
    public function getNonCacheableRoute()
    {
        return null;
    }
    /**
     * Function to override
     */
    public function getCacheableRoute()
    {
        return null;
    }
    /**
     * Function to override
     */
    public function getObjectidFieldname()
    {
        return 'id';
    }
    /**
     * What fields can be requested on the outside-looking API to query data. By default: everything that must be loaded from the server
     * and that depends on the logged-in user
     */
    public function getAllowedFields()
    {
        $allowedfields = $this->getNoCacheFields();
        return HooksAPIFacade::getInstance()->applyFilters(
            'Dataquery:'.$this->getName().':allowedfields', 
            $allowedfields
        );
    }
    /**
     * What fields are to be rejected
     */
    public function getRejectedFields()
    {
        $rejectedfields = array();
        return HooksAPIFacade::getInstance()->applyFilters(
            'Dataquery:'.$this->getName().':rejectedfields', 
            $rejectedfields
        );
    }
    /**
     * What layouts can be requested on the outside-looking API to query data. By default: everything that can be lazy
     */
    public function getAllowedLayouts()
    {
        $allowedlayouts = array();
        foreach ($this->getLazyLayouts() as $field => $lazylayouts) {
            foreach ($lazylayouts as $key => $layout) {
                $allowedlayouts[] = $layout;
            }
        }
        return HooksAPIFacade::getInstance()->applyFilters(
            'Dataquery:'.$this->getName().':allowedlayouts', 
            array_unique(
                $allowedlayouts,
                SORT_REGULAR
            )
        );
    }
    /**
     * Fields whose data must be retrieved each single time from the server. Eg: comment-count, since adding a comment doesn't delete the overall cache,
     * so the number cached in html will be out of date
     */
    public function getNoCacheFields()
    {
        return HooksAPIFacade::getInstance()->applyFilters(
            'Dataquery:'.$this->getName().':nocachefields', 
            array()
        );
    }
    /**
     * Fields whose data is retrieved on a subsequent call to the server
     */
    public function getLazyLayouts()
    {
        return HooksAPIFacade::getInstance()->applyFilters(
            'Dataquery:'.$this->getName().':lazylayouts', 
            array()
        );
    }
}
