<?php

namespace Dfe\Logo\Block\Product\View;

class Js extends \Magento\Framework\View\Element\Template
{

    protected $_oiValue;         
    protected $_coreRegistry;    
    protected $_jsonEncoder; 
    protected $_imageHelper;        
    protected $_mediaConfig;
  
          
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dfe\Logo\Model\Value $oiValue,                    
        \Magento\Framework\Registry $coreRegistry, 
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,  
        \Magento\Catalog\Helper\Image $imageHelper, 
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,                                                                                            
        array $data = array()
    ) {
        $this->_oiValue        = $oiValue;                     
        $this->_coreRegistry   = $coreRegistry;   
        $this->_jsonEncoder    = $jsonEncoder;   
        $this->_imageHelper    = $imageHelper; 
        $this->_mediaConfig    = $mediaConfig;       
                                                                                    
        parent::__construct($context, $data);
    }


    public function getProduct()
    {
      if (!$this->hasData('product')) {
        $this->setData('product', $this->_coreRegistry->registry('product'));
      }
      return $this->getData('product');
    }

	/**
	 * 2018-03-13
	 * @return string
	 */
    function getDataJson() {
		$r = ['image' => array(), 'thumbnail' => []];
		$images = $this->_oiValue->getImages((int)$this->getProduct()->getId());
		foreach ($images as $id => $image) {
			$valueId = (int)$id;
			$r['image'][$valueId] = $this->_mediaConfig->getMediaUrl($image);
			$r['thumbnail'][$valueId] =
				$this->_imageHelper->init(
					$this->getProduct()
					,'product_page_image_small'
					,['type'=>'thumbnail']
				)->resize(100)->setImageFile($image)->getUrl()
			;
		}
		return $this->_jsonEncoder->encode($r);
    }
}