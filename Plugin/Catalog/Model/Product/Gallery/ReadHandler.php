<?php
namespace Dfe\Logo\Plugin\Catalog\Model\Product\Gallery;
use Dfe\Logo\Setup\UpgradeSchema as Schema;
use Magento\Catalog\Api\Data\ProductAttributeInterface as IA;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as Sb;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as RG;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Store\Model\Store;
// 2018-03-21
final class ReadHandler {
	/**
	 * 2018-03-21
	 * @see \Magento\Catalog\Model\Product\Gallery\ReadHandler::execute()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Catalog/Model/Product/Gallery/ReadHandler.php#L49-L72
	 * I implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\ReadHandler::afterExecute():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/Catalog/Product/Gallery/ReadHandler.php#L16-L49
	 * @see
	 * @param Sb $sb
	 * @param P $r
	 * @return P
	 */
	function afterExecute(Sb $sb, P $r) {
		if ($m = self::media($r, $sb->getAttribute())) { /** @var array(array(string => mixed)) $m */
			$ids = array_column($m, 'value_id'); /** @var int[] $ids */
			$r[$sb->getAttribute()->getAttributeCode()] = $this->addLogo($m, $this->loadLogo(
				$ids, $r->getStoreId()
			));
		}
		return $r;
	}

	/**
	 * 2018-03-21
	 * @used-by afterExecute()
	 * @param array $mediaCollection
	 * @param array $data
	 * @return array
	 */
	private function addLogo(array $mediaCollection, array $data) {
		$data = df_map_r(function(array $i) {return [$i['value_id'], dfa_unset($i, 'value_id')];}, $data);
		foreach ($mediaCollection as &$mediaItem) {
			if (array_key_exists($mediaItem['value_id'], $data)) {
				$mediaItem = array_merge($mediaItem, $data[$mediaItem['value_id']]);
			}
		}
		return ['images' => $mediaCollection];
	}

	/**
	 * 2018-03-21
	 * @used-by afterExecute()
	 * @param array $ids
	 * @param null $sid
	 * @return mixed
	 */
	private function loadLogo(array $ids, $sid = null) {
		$rg = df_o(RG::class); /** @var RG $rm */
		$r = $rg->loadDataFromTableByValueId(Schema::T, $ids, Store::DEFAULT_STORE_ID, [
			'value_id' => 'value_id'
			,'logo_left_default' => 'left'
			,'logo_scale_default' => 'scale'
			,'logo_top_default' => 'top'
		]
		,[[
			['store_value' => $rg->getTable(Schema::T)]
			,df_ccc(' AND ',
				"{$rm->getMainTableAlias()}.value_id = store_value.value_id"
				,!$sid ? null : "store_value.store_id = $sid"
			)
			,['logo_left' => 'left', 'logo_scale' => 'scale', 'logo_top' => 'top']
		]]);
		foreach ($r as &$i) {
			foreach (['value_id', 'store_id', 'left', 'scale', 'top'] as $k) {
				$kd = "{$k}_default";
				if (empty($i[$k]) && !empty($i[$kd])) {
					$i[$k] = $i[$kd];
				}
				unset($i[$kd]);
			}
		}
		return $r;
	}

	/**
	 * 2018-03-21
	 * I implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\AbstractHandler::getMediaEntriesDataCollection():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/Catalog/Product/Gallery/AbstractHandler.php#L40-L55
	 * @used-by afterExecute()
	 * @param P $p
	 * @param A|IA $a
	 * @return array(array(string => mixed))
	 */
	static function media(P $p, IA $a) {return
		!($i = dfa($p[$a->getAttributeCode()], 'images')) || !is_array($i) ? [] : $i
	;}
}