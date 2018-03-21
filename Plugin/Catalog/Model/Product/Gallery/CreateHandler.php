<?php
namespace Dfe\Logo\Plugin\Catalog\Model\Product\Gallery;
use Df\Core\Exception as DFE;
use Dfe\Logo\Plugin\Catalog\Model\Product\Gallery\ReadHandler as RH;
use Dfe\Logo\Setup\UpgradeSchema as Schema;
use Magento\Catalog\Api\Data\ProductAttributeInterface as IA;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Gallery\CreateHandler as Sb;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\CreateHandler as VCH;
// 2018-03-21
final class CreateHandler {
	/**
	 * 2018-03-21
	 * @see Sb::execute()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Catalog/Model/Product/Gallery/CreateHandler.php#L104-L208
	 * I implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\CreateHandler::afterExecute():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/Catalog/Product/Gallery/CreateHandler.php#L45-L66
	 * @param Sb $sb
	 * @param P $r
	 * @return P
	 */
	function afterExecute(Sb $sb, P $r) {
		if ($m = RH::media($r, $sb->getAttribute())) { /** @var array(array(string => mixed)) $m */
			$ak = VCH::ADDITIONAL_STORE_DATA_KEY;
			$logos = df_clean(array_map(function(array $i) use($ak) {return
				!empty($i['removed']) || 'image' !== dfa($i, 'media_type') ? null : array_intersect_key(
					$i, [$ak => ''] + self::$map
				)
			;}, $m));
			$sid = $r->getStoreId();
			$rg = RH::rg();
			foreach ($logos as $i) {
				$i['store_id'] = $sid;
				$rg->saveDataRow(Schema::T, $this->prepareLogoRowDataForSave($i));
			}
			foreach ($logos as $i) {
				if (!empty($i[$ak])) {
					foreach ($i[$ak] as $ia) {
						$rg->saveDataRow(Schema::T, $this->prepareLogoRowDataForSave($i['value_id'] + $ia));
					}
				}
			}
		}
		return $r;
	}

	/**
	 * 2018-03-21
	 * @see Sb::execute()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Catalog/Model/Product/Gallery/CreateHandler.php#L104-L208
	 * I implemented it by analogy with
	 * @see \Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\CreateHandler::beforeExecute():
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/ProductVideo/Model/Plugin/Catalog/Product/Gallery/CreateHandler.php#L21-L43
	 * @param Sb $sb
	 * @param P $p
	 * @throws DFE
	 */
	function beforeExecute(Sb $sb, P $p) {
		$a = $sb->getAttribute(); /** @var A|IA $a */
		$c = $a->getAttributeCode(); /** @var string $c */
		if ($m = RH::media($p, $a)) { /** @var array(array(string => mixed)) $m */
			$d = $this->loadLogo($m, $p->getStoreId());
			$p[$c] = ['images' => array_map(function(array $i) use($d) {return
				!($vid = dfa($i, 'save_data_from'))
				|| !($a = df_clean(array_map(function(array $i) use($vid) {return
					$vid !== $i['value_id'] ? null : dfa_unset($i, 'value_id')
				;}, $d)))
				? $i : [VCH::ADDITIONAL_STORE_DATA_KEY => $a] + $i
			;}, $m)] + $p[$c];
		}
	}

	/**
	 * 2018-03-21
	 * @used-by beforeExecute()
	 * @param array(array(string => mixed)) $m
	 * @param int $eSid
	 * @return array
	 * @throws DFE
	 */
	private function loadLogo(array $m, $eSid) {return
		!($ids = df_clean(array_values(df_map(function(array $i) {return
			empty($i['removed'])
			&& 'image' === dfa($i, 'media_type')
			&& isset($i['save_data_from'])
			? $i['save_data_from'] : null
		;}, $m))))
		? [] : array_filter(
			RH::rg()->loadDataFromTableByValueId(Schema::T, $ids, null, self::$map)
			,function (array $i) use ($eSid) {return $i['store_id'] != $eSid;}
		)
	;}

	/**
	 * 2018-03-21
	 * @used-by afterExecute()
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	private function prepareLogoRowDataForSave(array $r) {
		foreach (self::$map as $sourceKey => $dbKey) {
			if (array_key_exists($sourceKey, $r) && $sourceKey != $dbKey) {
				$r[$dbKey] = $r[$sourceKey];
				unset($r[$sourceKey]);
			}
		}
		return array_intersect_key($r, array_flip(self::$map));
	}

	/**
	 * 2018-03-21
	 * @used-by afterExecute()
	 * @used-by loadLogo()
	 * @used-by prepareLogoRowDataForSave()
	 * @var array(string => string)
	 */
	private static $map = [
		'value_id' => 'value_id', 'store_id' => 'store_id'
		,'logo_left' => 'left', 'logo_scale' => 'scale', 'logo_top' => 'top'
	];
}