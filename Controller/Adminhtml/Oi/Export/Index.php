<?php

namespace Dfe\Logo\Controller\Adminhtml\Oi\Export;

class Index extends \Dfe\Logo\Controller\Adminhtml\Oi\Export
{


  public function execute()
  {  
      $this->_view->loadLayout();
      $this->_setActiveMenu('Dfe_Logo::oi_export')
          ->_addBreadcrumb(
              __('Catalog'),
              __('Catalog'))
          ->_addBreadcrumb(
              __('Product Option Images'),
              __('Product Option Images')
      );     
      $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Product Option Images'));       
      $this->_view->renderLayout();
  } 

}
