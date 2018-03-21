<?php
namespace Dfe\Logo\R;
use Dfe\Logo\Setup\UpgradeSchema as S;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Model\ResourceModel\AbstractResource as AR;
// 2018-03-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Logo extends \Df\Framework\Model\ResourceModel\Db\AbstractDb {
	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see AR::_construct()
	 * @used-by AR::__construct()
	 */
	function _construct() {$this->_init(S::T, 'value_id');}

	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Dfe\Logo\Plugin\Catalog\Model\ResourceModel\Product\Gallery::afterDuplicate()
	 * @param array(array(string => int|float)) $data
	 * @param string[] $fields [optional]
	 * @return int
	 * @throws LE
	 */
	function insertOnDuplicate(array $data, array $fields = []) {return $this->c()->insertOnDuplicate(
		$this->t(), $data, $fields
	);}

	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Dfe\Logo\Plugin\Catalog\Model\ResourceModel\Product\Gallery::afterDuplicate()
	 * @param int[] $ids
	 * @return array(array(string => int|float))
	 * @throws LE
	 */
	function loadByIds(array $ids) {
		$c = $this->c(); /** @var Mysql|IAdapter $c */
		$s = $c->select()->from($this->t())->where('value_id IN(?)', $ids); /** @var Select $s */
		return $c->fetchAll($s);
	}
}