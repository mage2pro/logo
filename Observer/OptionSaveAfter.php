<?php
namespace Dfe\Logo\Observer;
use Magento\Framework\Event\ObserverInterface;
class OptionSaveAfter implements ObserverInterface {
  protected $_mediaConfig;
  protected $_mediaDirectory;  
  protected $_objectManager; 

  function __construct(
	  \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
	  \Magento\Framework\Filesystem $filesystem,
	  \Magento\Framework\ObjectManagerInterface $objectManager
  ) {        
	  $this->_mediaConfig     = $mediaConfig;
	  $this->_mediaDirectory  = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
	  $this->_objectManager   = $objectManager;
  } 
 

	function execute(\Magento\Framework\Event\Observer $observer) {
		$object = $observer->getEvent()->getObject();
		if ($object->getResourceName() == 'Magento\Catalog\Model\ResourceModel\Product\Option\Value') {
			$image = $object->getImage();
			if (preg_match("/.tmp$/i", $image)) {
				$image = $this->_moveImageFromTmp($image);
			}
			else if ($object['imageSavedAs'] && !$object->getDeleteImage()) {
				$image = $object['imageSavedAs'];
			}
			// 2018-03-14
			if ((!empty($image) || $object->getDeleteImage())) {
				df_assert(!df_check_url_absolute($image));
				$model = $this->_objectManager->create('Dfe\Logo\Model\Value')->load($object->getId(), 'option_type_id');
				$model->setOptionTypeId($object->getId());
				$model->setImage($image);
				$model->save();
			}
		}
		return $this;
	}
	
  protected function _moveImageFromTmp($file) {
	  if (strrpos($file, '.tmp') == strlen($file) - 4) {
		  $file = substr($file, 0, strlen($file) - 4);
	  }
	  $destFile = dirname(
		  $file
	  ) . '/' . \Magento\MediaStorage\Model\File\Uploader::getNewFileName(
		  $this->_mediaDirectory->getAbsolutePath($this->_mediaConfig->getMediaPath($file))
	  );
	  $this->_mediaDirectory->renameFile(
		$this->_mediaConfig->getTmpMediaPath($file),
		$this->_mediaConfig->getMediaPath($destFile)
	  );
	  return str_replace('\\', '/', $destFile);
  }
}