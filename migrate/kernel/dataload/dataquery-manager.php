<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

class DataQueryManager
{
    public $dataqueries;
    
    public function __construct()
    {
        DataQueryManagerFactory::setInstance($this);
        return $this->dataqueries = array();
    }
    
    public function add($name, $dataquery)
    {
        $this->dataqueries[$name] = $dataquery;
    }
    
    public function get($name)
    {
        return $this->dataqueries[$name];
    }

    public function filterAllowedfields($fields)
    {

        // Choose if to reject fields, starting from all of them...
        // By default, if API is enabled, then use this method
        if (HooksAPIFacade::getInstance()->applyFilters('DataQueryManager:filter_by_rejection', !Server\Utils::disableAPI())) {
            return array_values(
                array_diff(
                    $fields,
                    $this->getRejectedFields()
                )
            );
        }

        // ... or choose to allow fields, starting from an empty list
        // Used to restrict access to the site, and only offer those fields needed for lazy loading
        return array_values(
            array_intersect(
                $fields,
                $this->getAllowedFields()
            )
        );
    }

    public function filterAllowedlayouts($layouts)
    {

        // // If allow to return all layouts, then no need to filter them
        // if (HooksAPIFacade::getInstance()->applyFilters('DataQueryManager:allow_all_layouts', false)) {

        //     return $layouts;
        // }

        return array_values(
            array_intersect(
                $layouts,
                $this->getAllowedLayouts()
            )
        );
    }

    public function getAllowedFields()
    {
        $allowedfields = array();
        foreach ($this->dataqueries as $name => $dataquery) {
            $allowedfields = array_merge(
                $allowedfields,
                $dataquery->getAllowedFields()
            );
        }

        return array_unique($allowedfields);
    }

    public function getRejectedFields()
    {
        $rejectedfields = array();
        foreach ($this->dataqueries as $name => $dataquery) {
            $rejectedfields = array_merge(
                $rejectedfields,
                $dataquery->getRejectedFields()
            );
        }

        return array_unique($rejectedfields);
    }

    public function getAllowedLayouts()
    {
        $allowedlayouts = array();
        foreach ($this->dataqueries as $name => $dataquery) {
            $allowedlayouts = array_merge(
                $allowedlayouts,
                $dataquery->getAllowedLayouts()
            );
        }

        return array_unique(
            $allowedlayouts,
            SORT_REGULAR
        );
    }

    public function getCacheableRoutes()
    {
        $cacheableroutes = array();
        foreach ($this->dataqueries as $name => $dataquery) {
            $cacheableroutes[] = $dataquery->getCacheableRoute();
        }

        return $cacheableroutes;
    }

    public function getNonCacheableRoutes()
    {
        $noncacheableroutes = array();
        foreach ($this->dataqueries as $name => $dataquery) {
            $noncacheableroutes[] = $dataquery->getNonCacheableRoute();
        }

        return $noncacheableroutes;
    }
}
    
/**
 * Initialize
 */
new DataQueryManager();
