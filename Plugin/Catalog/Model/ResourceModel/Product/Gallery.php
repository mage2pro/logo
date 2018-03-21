<?php
namespace Dfe\Logo\Plugin\Catalog\Model\ResourceModel\Product;
use Dfe\Logo\R\Logo as RM;
use Dfe\Logo\Setup\UpgradeSchema as Schema;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as Sb;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException as LE;
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
		$r->joinLeft(
			['value_logo' => $sb->getTable(Schema::T)]
			,'value.value_id = value_logo.value_id AND value.store_id = value_logo.store_id'
			,['logo_left' => 'left', 'logo_top' => 'top', 'logo_scale' => 'scale']
		);
		$r->joinLeft(
			['default_value_logo' => $sb->getTable(Schema::T)]
			,'default_value.value_id = value_logo.value_id AND default_value.store_id = value_logo.store_id'
			,['logo_left_default' => 'left', 'logo_top_default' => 'top', 'logo_scale_default' => 'scale']
		);
		return $r;
	}

	/**
	 * 2018-03-21
	 * @see \Magento\Catalog\Model\ResourceModel\Product\Gallery::duplicate():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Catalog/Model/ResourceModel/Product/Gallery.php#L352-L415
	 * I have implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\ExternalVideoResourceBackend::afterDuplicate():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/ExternalVideoResourceBackend.php#L30-L46
	 * @param Sb $sb
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 * @throws LE
	 */
	function afterDuplicate(Sb $sb, array $r) {
		$rm = df_o(RM::class); /** @var RM $rm */
		$mediaGalleryEntitiesData = $rm->loadByIds(array_keys($r));
		foreach ($mediaGalleryEntitiesData as $row) {
			$row['value_id'] = $r[$row['value_id']];
			$rm->insertOnDuplicate($row);
		}
		return $r;
	}
}