<?php
namespace Dfe\Logo\Plugin\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as Sb;
use Magento\Framework\DB\Select;
// 2018-03-21
final class Gallery {
	/**
	 * 2018-03-21
	 * @see \Magento\Catalog\Model\ResourceModel\Product\Gallery::createBatchBaseSelect():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Catalog/Model/ResourceModel/Product/Gallery.php#L154-L203
	 * I have implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\ExternalVideoResourceBackend::afterCreateBatchBaseSelect():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/ExternalVideoResourceBackend.php#L48-L90
	 * @param Sb $sb
	 * @param Select $r
	 * @return Select
	 */
	function afterCreateBatchBaseSelect(Sb $sb, Select $r) {
		return $r;
	}
}