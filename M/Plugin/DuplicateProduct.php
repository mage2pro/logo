<?php
namespace Dfe\Logo\M\Plugin;
class DuplicateProduct {
    protected $_oiValue;
    function __construct(\Dfe\Logo\M\Value $oiValue) {$this->_oiValue = $oiValue;}
    function aroundDuplicate(
    	\Magento\Catalog\Model\Product\Option\Value $value, \Closure $proceed, $oldOptionId, $newOptionId
	) {
        $result = $proceed($oldOptionId, $newOptionId);
        $this->_oiValue->getResource()->duplicate($oldOptionId, $newOptionId);
        return $result;
    }
}