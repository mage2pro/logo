<?php
namespace Dfe\Logo\Controller\Adminhtml\Oi\Export;
class Import extends \Dfe\Logo\Controller\Adminhtml\Oi\Export {
	function execute() {
		if (!$this->getRequest()->isPost() && $this->getRequest()->getFiles('import_file')) {
			$this->messageManager->addError(__('Invalid file upload attempt'));
		}
		else {
			try {
				$importHandler = $this->_objectManager->create('Dfe\Logo\M\CsvImportHandler');
				$importHandler->importFromCsvFile($this->getRequest()->getFiles('import_file'));
				$this->messageManager->addSuccess(__('Product custom options have been imported.'));
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			}
			# 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
			catch (\Throwable $t) {
				$this->messageManager->addError($t->getMessage().__('Invalid file upload attempt'));
			}
		}
		$this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
	}
}