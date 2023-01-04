<?php

namespace Dfe\Logo\M;

class CsvImportHandler
{

	protected $_oldSku = [];

	protected $_optionTypes = array(
		'date',
		'date_time',
		'time',
		'file',
		'field',
		'area',
		'drop_down',
		'radio',
		'checkbox',
		'multiple'
	);

	protected $_selectableOptionTypes = array(
		'drop_down',
		'radio',
		'checkbox',
		'multiple'
	);


	protected $_option;
	protected $_productFactory;
	protected $_resource;
	protected $_csvParser;
	protected $_objectManager;

	function __construct(
		\Magento\Catalog\Model\Product\Option $option,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Framework\File\Csv $csvParser,
		\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		$this->_option = $option;
		$this->_productFactory = $productFactory;
		$this->_resource = $resource;
		$this->_csvParser = $csvParser;
		$this->_objectManager = $objectManager;

		$this->_initSkus();
	}

	/**
	 * Initialize existent product SKUs.
	 *
	 * @return $this
	 */
	protected function _initSkus()
	{
		$columns = array('entity_id', 'sku');
		foreach ($this->_productFactory->create()->getProductEntitiesInfo($columns) as $info) {
		  $this->_oldSku[$info['sku']] = $info['entity_id'];
		}
		return $this;
	}


	function importFromCsvFile($file)
	{
		if (!isset($file['tmp_name'])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
		}

		$rawData = $this->_csvParser->getData($file['tmp_name']);

		$fieldNames = [];
		foreach ($rawData[0] as $v) {
		  $v = strtolower( preg_replace('/\s+/', '_', preg_replace('/[^\w\s]/', '', $v)));
		  if ($v == '' || in_array($v, $fieldNames)){
			throw new \Magento\Framework\Exception\LocalizedException(__('Import failed. The first row in the import.csv file must contain unique column names.'));
			return;
		  }
		  $fieldNames[] = $v;
		}

			  $options = [];
			  $hasRequired =  [];
			
			
		$countRows = 0;
		foreach ($rawData as $rowIndex => $csvData) {
		  // skip headers
		  if ($rowIndex == 0)
			continue;

		  if (count($csvData) == 1 && $csvData[0] === null)
			continue;

		  $importData = [];
		  foreach ($fieldNames as $k => $v)
			$importData[$v] = isset($csvData[$k]) ? $csvData[$k] : '';

				if (empty($importData['product_sku'])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Skip import row, required field %1 is not defined', 'product_sku'));
			return;
		  }

				if (!isset($this->_oldSku[$importData['product_sku']])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Product with specified SKU "%1" is not found', $importData['product_sku']));
			return;
		  }
  
				if (empty($importData['option_title'])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Skip import row, required field %1 is not defined', 'option_title'));
			return;
		  }

				if (empty($importData['type'])) {
			throw new \Magento\Framework\Exception\LocalizedException(__('Skip import row, required field %1 is not defined', 'type'));
			return;
		  }

		  if (!in_array($importData['type'], $this->_optionTypes)){
			throw new \Magento\Framework\Exception\LocalizedException(__('Skip import row, value "%1" is not valid for field "%2". Valid values for the field "%3" are: %4.', $importData['type'], 'type', 'type', implode(", ", $this->_optionTypes)));
			return;
		  }


		  if (in_array($importData['type'], $this->_selectableOptionTypes) && empty($importData['value_title'])){
			throw new \Magento\Framework\Exception\LocalizedException(__('Skip import row, required field "%1" for option type "%2" is not defined', 'value_title', $importData['type']));
			return;
		  }

		  $importData['product_id'] = $this->_oldSku[$importData['product_sku']];
				  $importData['price_type'] = $importData['price_type'] == 'percent' ? 'percent' : 'fixed';          


		  if ($importData['is_require'] == 1)
			$hasRequired[$importData['product_id']] = true;

		  $option_ind = $importData['option_title'] . $importData['type'] . $importData['is_require'] . $importData['option_sort_order'];

		  $options[$importData['product_id']][$option_ind]['title'] = $importData['option_title'];
		  $options[$importData['product_id']][$option_ind]['type'] = $importData['type'];
		  $options[$importData['product_id']][$option_ind]['is_require'] = $importData['is_require'];
		  $options[$importData['product_id']][$option_ind]['sort_order'] = $importData['option_sort_order'];
  
		  $group = $this->_option->getGroupByType($importData['type']);

		  if ($group == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT){
			$options[$importData['product_id']][$option_ind]['values'][] = array(
				'title'=>$importData['value_title'],
				'price'=>$importData['price'],
				'price_type'=>$importData['price_type'],
				'sku'=>$importData['sku'],
				'sort_order'=>$importData['value_sort_order'],
				'image'=>$importData['image']
			  );
		  } else {
			$options[$importData['product_id']][$option_ind]['price'] = $importData['price'];
			$options[$importData['product_id']][$option_ind]['price_type'] = $importData['price_type'];
			$options[$importData['product_id']][$option_ind]['sku'] = $importData['sku'];
			if ($group == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_FILE){
			  $options[$importData['product_id']][$option_ind]['file_extension'] = $importData['file_extension'];
			  $options[$importData['product_id']][$option_ind]['image_size_x'] = $importData['image_size_x'];
			  $options[$importData['product_id']][$option_ind]['image_size_y'] = $importData['image_size_y'];
			} elseif ($group == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_TEXT){
			  $options[$importData['product_id']][$option_ind]['max_characters'] = $importData['max_characters'];
			}
		  }
 
		}

		$connection = $this->_resource->getConnection();
		$productModel = $this->_productFactory->create();
		$productModel->setStoreId(0);
		foreach ($options as $productId => $productOptions) {

		  $connection->query("DELETE FROM `{$connection->getTableName('catalog_product_option')}` WHERE `product_id` = {$productId}");

		  foreach ($productOptions as $option) {
			$optionModel = $this->_objectManager->create('Magento\Catalog\Model\Product\Option');
			$optionModel->setData($option)
				->setOptionId(null)
				->setProductId($productId)
				->save();
		  }
  
		  $required = isset($hasRequired[$productId]) ? 1 : 0;
		  $connection->query("UPDATE `{$connection->getTableName('catalog_product_entity')}` SET `has_options`=1, `required_options`={$required} WHERE `entity_id` = {$productId}");

		}

	}

}
