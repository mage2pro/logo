<?php
namespace Dfe\Logo\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content as B;
// 2018-03-20
final class CatalogProductGalleryPrepareLayout implements ObserverInterface {
	/**
	 * 2018-03-20
	 * I implemented it by analogy with
	 * @see \Magento\ProductVideo\Observer\ChangeTemplateObserver::execute():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Observer/ChangeTemplateObserver.php#L11-L22
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {
		$b = $o['block']; /** @var B $b */
		$b->setTemplate('Dfe_Logo::gallery.phtml');
	}
}