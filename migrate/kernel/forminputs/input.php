<?php
namespace PoP\Engine;

class GD_FormInput
{
    public $name;
    public $filter;
    public $selected;

    public function __construct($params = array())
    {
        $this->name = $params['name'];

        // Selected value. If provided, use it
        if (isset($params['selected'])) {
            $this->selected = $params['selected'];
        }
    }

    public function isMultiple()
    {
        return false;
    }
    
    public function getSelectedvalueFromRequest()
    {

        // If not set, it will be NULL
        $value =  $_REQUEST[$this->getName()];

        // If it is multiple and the URL contains an empty value (eg: &searchfor[]=&), it will interpret it as array(''),
        // but instead it must be an empty array
        // if ($this->isMultiple() && $value && count($value) == 1 && $value[0] == '') {

        //     $value = array();
        // }
        if ($this->isMultiple() && !is_null($value)) {
            $value = array_filter($value);
        }

        return $value;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * $_REQUEST has priority (for when editing post / user data, after submitting form this will override original post / user metadata values)
     */
    public function getValue(/*$filter = null*/)
    {

        // Empty values (eg: '', array()) can be the value. Only if NULL get a default value
        if (!is_null($this->selected)) {
            return $this->selected;
        }

        // Otherwise, if filtering, get the value from the request
        // if (!$filter || \PoP\Engine\FilterUtils::filteringBy($filter)) {
        $selected = $this->getSelectedvalueFromRequest();
        if (!is_null($selected)) {
            return $selected;
        }
        // }
        
        return $this->getDefaultValue();
    }
    
    /**
     * Function to override
     */
    public function getDefaultValue()
    {
        return null;
    }
}