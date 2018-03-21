<?php

namespace Dfe\Logo\Controller\Adminhtml\Oi\Export;

class Export extends \Dfe\Logo\Controller\Adminhtml\Oi\Export
{


  function execute()
  {
    $content = $this->_objectManager->create('Dfe\Logo\M\Value')->getOptionsCsv();
    
    return $this->_fileFactory->create('product_options.csv', $content); 
                    
  }  

}
