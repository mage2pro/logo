<?php
namespace Dfe\Logo\Block\Adminhtml\Product\Edit;
class Js extends \Magento\Backend\Block\Widget {
	protected $_oiValue;
	protected $_fileSizeService;
	protected $_coreRegistry;
	protected $_imageHelper;
	protected $_jsonEncoder;

	function __construct(
		\Dfe\Logo\M\Value $oiValue,
		\Magento\Framework\File\Size $fileSize,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Helper\Image $imageHelper,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Backend\Block\Widget\Context $context,
		array $data = []
	) {
		$this->_oiValue = $oiValue;
		$this->_fileSizeService = $fileSize;
		$this->_coreRegistry = $registry;
		$this->_imageHelper = $imageHelper;
		$this->_jsonEncoder = $jsonEncoder;
		parent::__construct($context, $data);
	}

	function getProduct() {
		if (!$this->hasData('product')) {
			$this->setData('product', $this->_coreRegistry->registry('product'));
		}
		return $this->getData('product');
	}

	function getDataJson() {
		$config = array('image' => [], 'imageSavedAs' => []);
		$product = $this->getProduct();
		$images = $this->_oiValue->getImages((int) $product->getId());
		foreach ($images as $id => $image) {
			$valueId = (int) $id;
			$config['image'][$valueId] =
				$this->_imageHelper
					->init($product, 'product_page_image_small', ['type'=>'thumbnail'])
					->resize(40)
					->setImageFile($image)
					->getUrl()
			;
			$config['imageSavedAs'][$valueId] = $image;
		}
		return $this->_jsonEncoder->encode($config);
	}

	function getUploadUrl() {return $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload');}

	function getFileSizeService() {return $this->_fileSizeService;}
}