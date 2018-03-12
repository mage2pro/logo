<?php

namespace Dfe\Logo\Model\Plugin;

class DuplicateProduct
{

    protected $_oiValue;   


    public function __construct(
        \Dfe\Logo\Model\Value $oiValue
    ) {
        $this->_oiValue = $oiValue;
    }


    public function aroundDuplicate(\Magento\Catalog\Model\Product\Option\Value $value, \Closure $proceed, $oldOptionId, $newOptionId)
    {
        $result = $proceed($oldOptionId, $newOptionId);
        
        $this->_oiValue->getResource()->duplicate($oldOptionId, $newOptionId);
        
        return $result;
    }


}
