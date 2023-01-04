<?php
namespace Dfe\Logo\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\DataType\Text;
class CustomOptions {
	protected $_oiValue;
	protected $locator;
	protected $_imageHelper;
	protected $_urlBuilder;

	function __construct(
		\Dfe\Logo\M\Value $oiValue,
		\Magento\Catalog\Model\Locator\LocatorInterface $locator,
		\Magento\Catalog\Helper\Image $imageHelper,
		\Magento\Framework\UrlInterface $urlBuilder
	) {
		$this->_oiValue = $oiValue;
		$this->locator = $locator;
		$this->_imageHelper = $imageHelper;
		$this->_urlBuilder = $urlBuilder;
	}

	function beforeModifyData(\Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject, $data) {
		$product = $this->locator->getProduct();
		$options = [];
		$images = $this->_oiValue->getImages((int) $product->getId());
		foreach ((array) $product->getOptions() as $index => $option) {
			$values = $option->getValues() ?: [];
			foreach ($values as $value) {
			  $image = '';
			  $imageUrl = '';
			  if (isset($images[$value->getOptionTypeId()])){
				$image = $images[$value->getOptionTypeId()];
				$imageUrl = $this->_imageHelper->init($product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
			  }

			  $options[$index][$subject::GRID_TYPE_SELECT_NAME][] = [
				'image' => $imageUrl,
				'imageSavedAs' => $image
			  ];
			}
		}
		$data = array_replace_recursive(
			$data,
			[
				$product->getId() => [
					$subject::DATA_SOURCE_DEFAULT => [
						$subject::FIELD_ENABLE => 1,
						$subject::GRID_OPTIONS_NAME => $options
					]
				]
			]
		);
		return [$data];
	}

	function afterModifyMeta(\Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject, $meta) {
		if (isset($meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'][$subject::GRID_OPTIONS_NAME]['children']['record']['children'][$subject::CONTAINER_OPTION]['children'][$subject::GRID_TYPE_SELECT_NAME]['children']['record']['children'])){
		  $row = $meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'][$subject::GRID_OPTIONS_NAME]['children']['record']['children'][$subject::CONTAINER_OPTION]['children'][$subject::GRID_TYPE_SELECT_NAME]['children']['record']['children'];
 
		  $this->array_insert($row, 4, array('image' => $this->getOiImageFieldConfig(45)));

		  $meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'][$subject::GRID_OPTIONS_NAME]['children']['record']['children'][$subject::CONTAINER_OPTION]['children'][$subject::GRID_TYPE_SELECT_NAME]['children']['record']['children'] = $row;

		  $meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'][$subject::GRID_OPTIONS_NAME]['arguments']['data']['config']['pageSize'] = 1000;
		  $meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'][$subject::GRID_OPTIONS_NAME]['children']['record']['children'][$subject::CONTAINER_OPTION]['children'][$subject::GRID_TYPE_SELECT_NAME]['arguments']['data']['config']['pageSize'] = 1000;
		}
		$meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children']['oi_js_code'] = $this->getHeaderContainerConfig(40);
		return $meta;
	}

	protected function getOiImageFieldConfig($sortOrder)
	{
		return [
			'arguments' => [
				'data' => [
					'config' => [
						'label' => __('Image'),
						'additionalClasses' => 'oi-image-column',
						'componentType' => Field::NAME,
						'formElement' => Input::NAME,
						'elementTmpl' => "Dfe_Logo/form/element/input_image",
						'dataScope' => 'image',
						'dataType' => Text::NAME,
						'sortOrder' => $sortOrder,
						'deleteImageText' => __('Delete Image'),
						'browseFilesText' => __('Browse Files...'),
						'uploadUrl' => $this->getUploadUrl(),
						'imports' => [
							'image' => '${ $.provider }:${ $.parentScope }.image',
							'imageSavedAs' => '${ $.provider }:${ $.parentScope }.imageSavedAs'
						]
					],
				],
			],
		];
	}

	protected function getHeaderContainerConfig($sortOrder) {
		return [
			'arguments' => [
				'data' => [
					'config' => [
						'label' => null,
						'formElement' => Container::NAME,
						'componentType' => Container::NAME,
						'template' => "Dfe_Logo/form/components/js",
						'sortOrder' => $sortOrder,
						'content' => '',
						'idColumn' => 'aaa'
					],
				],
			],
		];
	}

	protected function array_insert(&$array, $position, $insert_array) {
		$first_array = array_splice ($array, 0, $position);
		$array = array_merge ($first_array, $insert_array, $array);
	}

	function getUploadUrl() {return $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload');}
}