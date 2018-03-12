<?php

namespace Dfe\Logo\Controller\Adminhtml\Oi\Export;

class Export extends \Dfe\Logo\Controller\Adminhtml\Oi\Export
{


  public function execute()
  {
    $content = $this->_objectManager->create('Dfe\Logo\Model\Value')->getOptionsCsv();
    
    return $this->_fileFactory->create('product_options.csv', $content); 
                    
  }  

}
