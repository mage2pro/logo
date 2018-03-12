<?php

namespace Dfe\Logo\Model\ResourceModel\Value;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    function _construct()
    {
        $this->_init('Dfe\Logo\Model\Value', 'Dfe\Logo\Model\ResourceModel\Value');
    }

}
