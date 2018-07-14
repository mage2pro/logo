<?php

namespace Dfe\Logo\Plugin\Catalog\Ui\DataProvider\Product;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;

class ProductCustomOptionsDataProvider
{
    protected $_product; 
    protected $_oiValue;       
    protected $_imageHelper;    
        
    
    function __construct(
        \Magento\Catalog\Model\Product $product,     
        \Dfe\Logo\M\Value $oiValue,  
        \Magento\Catalog\Helper\Image $imageHelper                           
    ) {
        $this->_product = $product;     
        $this->_oiValue = $oiValue;    
        $this->_imageHelper = $imageHelper;                               
    }



    function afterGetData(\Magento\Catalog\Ui\DataProvider\Product\ProductCustomOptionsDataProvider $subject, $data)
    {      
    
        $productIds = [];
        foreach($data['items'] as $product){
          $productIds[] = $product['entity_id'];
        }  
          
        $images = $this->_oiValue->getResource()->getImagesOfProducts($productIds);
        
        foreach($data['items'] as $k => $product){
          if (!isset($product['options']))
            continue;
            
          $productId = $product['entity_id'];
            
          foreach($product['options'] as $kk => $option){
            if (!isset($option['values']))
              continue; 
              
            foreach($option['values'] as $kkk => $value){                
              $valueId = $value['option_type_id'];      
                        
              if (!isset($images[$productId][$valueId]))
                continue;
                                
              $image = $images[$productId][$valueId];
              $data['items'][$k]['options'][$kk]['values'][$kkk]['image'] = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
              $data['items'][$k]['options'][$kk]['values'][$kkk]['imageSavedAs'] = $image;
            }           
          }     
        }
    
        return $data;   
    }
    
    

}
