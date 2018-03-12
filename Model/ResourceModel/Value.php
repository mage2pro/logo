<?php

namespace Dfe\Logo\Model\ResourceModel;

class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

  function _construct()
  {    
    $this->_init('optionimages_value', 'oi_value_id');
  }  


  function getImages($productId)
  {        
    $select = $this->getConnection()->select()
      ->from(array('cp' => $this->getTable('catalog_product_entity')), array())        
      ->join(array('ca' => $this->getTable('catalog_product_option')), 'ca.product_id = cp.entity_id', array())      
      ->join(array('va' => $this->getTable('catalog_product_option_type_value')), 'va.option_id = ca.option_id', array('option_type_id'))
      ->join(array('oi' => $this->getMainTable()),"oi.option_type_id = va.option_type_id AND oi.image != ''" , array('image'))        
      ->where('cp.entity_id=?', $productId);                         
      
    return $this->getConnection()->fetchPairs($select);                                 
  } 


  function getImagesOfProducts($productIds)
  { 
    $images = array();
    
    if (count($productIds) == 0)
      return array();
         
    $select = $this->getConnection()->select()
      ->from(array('cp' => $this->getTable('catalog_product_entity')), array('entity_id'))        
      ->join(array('ca' => $this->getTable('catalog_product_option')), 'ca.product_id = cp.entity_id', array())      
      ->join(array('va' => $this->getTable('catalog_product_option_type_value')), 'va.option_id = ca.option_id', array('option_type_id'))
      ->join(array('oi' => $this->getMainTable()),"oi.option_type_id = va.option_type_id AND oi.image != ''" , array('image'))        
      ->where('cp.entity_id IN (?)', $productIds);                         

    $result = $this->getConnection()->fetchAll($select); 

    foreach((array)$result as $row){
      $images[$row['entity_id']][$row['option_type_id']] = $row['image'];
    }

    return $images;                               
  }


  function duplicate($oldOptionId, $newOptionId)
  {	

    $productOptionValueTable = $this->getTable('catalog_product_option_type_value');		
        
    $select = $this->getConnection()->select()
      ->from($productOptionValueTable, array('option_type_id'))
      ->where('option_id=?', $oldOptionId);
    $oldTypeIds = $this->getConnection()->fetchCol($select);

    $select = $this->getConnection()->select()
      ->from($productOptionValueTable, array('option_type_id'))
      ->where('option_id=?', $newOptionId);
    $newTypeIds = $this->getConnection()->fetchCol($select);

    foreach ($oldTypeIds as $ind => $oldTypeId) {
          $sql = 'REPLACE INTO `' . $this->getMainTable() . '` '
              . 'SELECT NULL, ' . $newTypeIds[$ind] . ', `image`'
              . 'FROM `' . $this->getMainTable() . '` WHERE `option_type_id`=' . $oldTypeId;
      $this->getConnection()->query($sql);			
    }

  }



}
