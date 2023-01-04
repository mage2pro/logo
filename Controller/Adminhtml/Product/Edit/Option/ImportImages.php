<?php

namespace Dfe\Logo\Controller\Adminhtml\Product\Edit\Option;

class ImportImages extends \Magento\Backend\App\AbstractAction
{

  protected $_oiValue;
  protected $_product;    
  protected $_imageHelper;   
  protected $_jsonEncoder;
  

  function __construct(
	  \Magento\Backend\App\Action\Context $context,
	  \Dfe\Logo\M\Value $oiValue,
	  \Magento\Catalog\Model\Product $product,
	  \Magento\Catalog\Helper\Image $imageHelper,
	  \Magento\Framework\Json\EncoderInterface $jsonEncoder
  ) {
	  $this->_oiValue = $oiValue;
	  $this->_product = $product;
	  $this->_imageHelper = $imageHelper;
	  $this->_jsonEncoder = $jsonEncoder;

	  parent::__construct($context);
  } 
  

  function execute()
  { 
   
	$config = array('image' => [], 'imageSavedAs' => []);

	$productId = (int) $this->getRequest()->getParam('product_id');

	$images = $this->_oiValue->getImages($productId);
	foreach ($images as $id => $image) {
	  $valueId = (int) $id;
	  $config['image'][$valueId] = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
	  $config['imageSavedAs'][$valueId] = $image;
	}

	$this->getResponse()->setBody($this->_jsonEncoder->encode($config));
  } 

}
