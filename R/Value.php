<?php
namespace Dfe\Logo\R;
use Magento\Framework\Exception\LocalizedException as LE;
class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	function _construct() {$this->_init('optionimages_value', 'oi_value_id');}

	/**
	 * 2018-03-13
	 * @param $pid
	 * @return array
	 * @throws LE
	 */
	function getImages($pid) {
		$c = $this->getConnection();
		$select = $c->select()
			->from(['p' => $this->getTable('catalog_product_entity')], [])
			->join(['o' => $this->getTable('catalog_product_option')], 'o.product_id = p.entity_id', [])
			->join(['v' => $this->getTable('catalog_product_option_type_value')],
				'v.option_id = o.option_id', ['option_type_id']
			)
			->join(['oi' => $this->getMainTable()],
				"oi.option_type_id = v.option_type_id AND oi.image != ''" , ['image']
			)
			->where('? = p.entity_id', $pid)
		;
		return $c->fetchPairs($select);
	}

	/**
	 * 2018-03-13
	 * @used-by getImagesWithOptionIdAndTitle()
	 * @used-by \Dfe\Logo\Plugin\Catalog\Block\Product\View\Options::optionId()
	 * @param $pid
	 * @return array
	 * @throws LE
	 */
	function getImagesWithOptionId($pid) {
		$c = $this->getConnection();
		$select = $c->select()
			->from(['p' => $this->getTable('catalog_product_entity')], [])
			->join(['o' => $this->getTable('catalog_product_option')], 'o.product_id = p.entity_id', ['option_id'])
			->join(['v' => $this->getTable('catalog_product_option_type_value')],
				'v.option_id = o.option_id', ['option_type_id']
			)
			->join(['oi' => $this->getMainTable()],
				"oi.option_type_id = v.option_type_id AND oi.image != ''" , ['image']
			)
			->where('? = p.entity_id', $pid)
		;
		return $c->fetchAll($select);
	}

	/**
	 * 2018-03-17
	 * «Logo name appear below logo image»
	 * https://www.upwork.com/d/contracts/19713402
	 * @used-by \Dfe\Logo\M\Value::getImages()
	 * @param $pid
	 * @return array
	 * @throws LE
	 */
	function getImagesWithOptionIdAndTitle($pid) {
		$r = $this->getImagesWithOptionId($pid);
		/** @var array(string => string|int) $titles */
		$titles = $this->titles(array_column($r, 'option_type_id'));
		$sid = df_store_id(); /** @var int $sid */
		return array_map(function(array $i) use($titles, $sid) {
			$tid = $i['option_type_id']; /** @var int $tid */
			/** @var array(string => string|int) $matchedTitles */
			$matchedTitles = array_column(
				array_filter($titles, function(array $t) use ($tid) {return $tid === $t['option_type_id'];})
				,'title', 'store_id'
			);
			return $i + [self::K_TITLE => dfa($matchedTitles, $sid, dfa($matchedTitles, 0))];
		}, $r);
	}

	/**
	 * 2018-03-17
	 * @used-by getImagesWithOptionIdAndTitle()
	 * @used-by \Dfe\Logo\Frontend::_toHtml()
	 */
	const K_TITLE = 'title';

	/**
	 * 2018-03-17
	 * «Logo name appear below logo image»
	 * https://www.upwork.com/d/contracts/19713402
	 * @param int[] $optionTypeIds
	 * @return array
	 */
	function titles(array $optionTypeIds) {
		$c = $this->getConnection();
		$s = $c->select()
			->from($this->getTable('catalog_product_option_type_title'))
			->where('option_type_id IN (?)', $optionTypeIds)
		;
		return $c->fetchAll($s);
	}


  function getImagesOfProducts($productIds)
  { 
	$images = [];

	if (count($productIds) == 0)
	  return [];

	$select = $this->getConnection()->select()
	  ->from(array('cp' => $this->getTable('catalog_product_entity')), array('entity_id'))
	  ->join(array('ca' => $this->getTable('catalog_product_option')), 'ca.product_id = cp.entity_id', [])
	  ->join(array('va' => $this->getTable('catalog_product_option_type_value')), 'va.option_id = ca.option_id', array('option_type_id'))
	  ->join(array('oi' => $this->getMainTable()),"oi.option_type_id = va.option_type_id AND oi.image != ''" , array('image'))
	  ->where('cp.entity_id IN (?)', $productIds);

	$result = $this->getConnection()->fetchAll($select);

	foreach((array)$result as $row){
	  $images[$row['entity_id']][$row['option_type_id']] = $row['image'];
	}

	return $images;
  }


  function duplicate($oldOptionId, $newOptionId)
  {	

	$productOptionValueTable = $this->getTable('catalog_product_option_type_value');

	$select = $this->getConnection()->select()
	  ->from($productOptionValueTable, array('option_type_id'))
	  ->where('option_id=?', $oldOptionId);
	$oldTypeIds = $this->getConnection()->fetchCol($select);

	$select = $this->getConnection()->select()
	  ->from($productOptionValueTable, array('option_type_id'))
	  ->where('option_id=?', $newOptionId);
	$newTypeIds = $this->getConnection()->fetchCol($select);

	foreach ($oldTypeIds as $ind => $oldTypeId) {
		  $sql = 'REPLACE INTO `' . $this->getMainTable() . '` '
			  . 'SELECT NULL, ' . $newTypeIds[$ind] . ', `image`'
			  . 'FROM `' . $this->getMainTable() . '` WHERE `option_type_id`=' . $oldTypeId;
	  $this->getConnection()->query($sql);
	}
  }
}