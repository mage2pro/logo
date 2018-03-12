<?php

namespace Dfe\Logo\Model;

class Value extends \Magento\Framework\Model\AbstractModel
{ 

    protected $_productFactory;     
    protected $_option;
    
    function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,     
        \Magento\Catalog\Model\Product\Option $option,           
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Dfe\Logo\Model\ResourceModel\Value $resource,
        \Dfe\Logo\Model\ResourceModel\Value\Collection $resourceCollection,
        array $data = array()
    ) { 
        $this->_productFactory = $productFactory;
        $this->_option = $option;                         
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    
    function getImages($productId)
    {        
      return $this->getResource()->getImages($productId);                             
    }     


    
    function getOptionsCsv()
    {

      $headers = new \Magento\Framework\DataObject(array(
        'product_sku' => 'product_sku',
        'option_title' => 'option_title',
        'type' => 'type',
        'is_require' => 'is_require',
        'option_sort_order' => 'option_sort_order',
        'max_characters' => 'max_characters',
        'file_extension' => 'file_extension',
        'image_size_x' => 'image_size_x',
        'image_size_y' => 'image_size_y',
        'value_title' => 'value_title',
        'price' => 'price',
        'price_type' => 'price_type',
        'sku' => 'sku',
        'value_sort_order' => 'value_sort_order',
        'image' => 'image'
      ));
        
      $template = '"{{product_sku}}","{{option_title}}","{{type}}","{{is_require}}","{{option_sort_order}}","{{max_characters}}","{{file_extension}}","{{image_size_x}}","{{image_size_y}}","{{value_title}}","{{price}}","{{price_type}}","{{sku}}","{{value_sort_order}}","{{image}}"';
      
      $csv = $headers->toString($template) . "\n";          
      
        
      $oi_values = array();		
      foreach ($this->getCollection() as $value)
        $oi_values[$value->getOptionTypeId()] = $value->getImage();
        
      $product = $this->_productFactory->create();
      $products = $product->getCollection()->addFieldToFilter('has_options', 1);	
      foreach ($products as $product){
        $row = array();
        $row['product_sku'] = $product->getSku();
        $options = $this->_option->getProductOptionCollection($product);
        foreach ($options as $option) {
          $row['option_title'] = str_replace('"', '""', $option->getTitle());
          $row['type'] = $option->getType();
          $row['is_require'] = $option->getIsRequire();
          $row['option_sort_order'] = $option->getSortOrder();
          $row['max_characters'] = $option->getMaxCharacters();
          $row['file_extension'] = $option->getFileExtension();
          $row['image_size_x'] = $option->getImageSizeX();
          $row['image_size_y'] = $option->getImageSizeY();
          
           if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
        
            foreach ($option->getValues() as $value) {
               $row['value_title'] = str_replace('"', '""', $value->getTitle());
               $row['price'] =$value->getPrice();
               $row['price_type'] = $value->getPriceType();
               $row['sku'] = str_replace('"', '""', $value->getSku());
               $row['value_sort_order'] = $value->getSortOrder();
               $row['image'] = isset($oi_values[$value->getOptionTypeId()]) ? $oi_values[$value->getOptionTypeId()] : '';								

               $rowObject = new \Magento\Framework\DataObject($row);
               $csv .= $rowObject->toString($template) . "\n";                					
            }
            
          } else {
  
            $row['value_title'] = '';	
            $row['price'] = $option->getPrice();	
            $row['price_type'] = $option->getPriceType();	
            $row['sku'] = str_replace('"', '""', $option->getSku());		
            $row['value_sort_order'] = '';			
            $row['image'] = '';		
            
            $rowObject = new \Magento\Framework\DataObject($row);
            $csv .= $rowObject->toString($template) . "\n";					
          }	
        }
      }  
      
      return $csv;    
    }      
    
}