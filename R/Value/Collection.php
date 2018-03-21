<?php
namespace Dfe\Logo\R\Value;
use Dfe\Logo\M\Value as M;
use Dfe\Logo\R\Value as R;
// 2018-03-21
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * 2018-03-21
	 * @override
	 * @see \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::_construct()
	 * @used-by \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::__construct()
	 */
    function _construct() {$this->_init(M::class, R::class);}
}