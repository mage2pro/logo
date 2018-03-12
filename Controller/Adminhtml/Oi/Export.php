<?php

namespace Dfe\Logo\Controller\Adminhtml\Oi;

use Magento\Backend\App\Action;

abstract class Export extends \Magento\Backend\App\AbstractAction
{


    protected $_productResource;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,        
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_productResource = $productResource;    
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
    
    
    protected function _isAllowed()
    {
       return $this->_authorization->isAllowed('Dfe_Logo::export');
    }     
}
