<?php
namespace PoP\Engine;

class FilterInputProcessor extends \PoP\Engine\AbstractFilterInputProcessor
{
    public const FILTERINPUT_ORDER = 'filterinput-order';
    public const FILTERINPUT_SEARCH = 'filterinput-search';

    public function getFilterInputsToProcess()
    {
        return array(
            [self::class, self::FILTERINPUT_ORDER],
            [self::class, self::FILTERINPUT_SEARCH],
        );
    }

    public function filterDataloadQueryArgs(array $filterInput, array &$query, $value)
    {
        switch ($filterInput[1]) {
            case self::FILTERINPUT_ORDER:
                $query['orderby'] = $value['orderby'];
                $query['order'] = $value['order'];
                break;
            case self::FILTERINPUT_SEARCH:
                $query['search'] = $value;
                break;
        }
    }
}



