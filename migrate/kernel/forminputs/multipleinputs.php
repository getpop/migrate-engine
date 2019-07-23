<?php
namespace PoP\Engine;

class GD_FormInput_MultipleInputs extends GD_FormInput_MultiSelect
{
    public $subnames;
    
    public function getSubnames()
    {
        return $this->subnames;
    }
    
    public function __construct($params = array())
    {
        parent::__construct($params);
        
        $this->subnames = $params['subnames'] ? $params['subnames'] : array();

        // Re-implement to get the values to get from all the subnames
        if (!isset($params['selected'])) {
            $name = $this->getName();
            $selected = array();
            foreach ($this->getSubnames() as $subname) {
                $fullsubname = PoP_InputUtils::getMultipleinputsName($name, $subname);
                if (isset($_REQUEST[$fullsubname])) {
                    $selected[$subname] = $_REQUEST[$fullsubname];
                }
            }
            // Only if there is any subfield value we assign it to $this->selected. Otherwise, it must keep the null value, as to obtain the value from dbObject[dbObjectField] in formcomponentValue
            if ($selected) {
                $this->selected = $selected;
            }
        }
    }
}
