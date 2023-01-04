<?php
namespace Dfe\Logo\Setup;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as G;
use Magento\Framework\DB\Adapter\AdapterInterface as IA;
use Magento\Framework\DB\Ddl\Table as T;
// 2018-03-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2018-03-21
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process():void {
		if ($this->v('1.2.3')) {
			$t = $this->c()->newTable($this->t(self::T)); /** @var T $t */
			$t->addColumn(
                'value_id'
				,T::TYPE_INTEGER
				,null
				, ['unsigned' => true, 'nullable' => false]
				, 'Media Entity ID'
            );
			$t->addColumn(
				'store_id'
				,T::TYPE_SMALLINT,
				null,
				['unsigned' => true, 'nullable' => false, 'default' => '0'],
				'Store ID'
            );
			$t->addColumn(
				'left'
				,T::TYPE_DECIMAL
				,'4,2'
				,['unsigned' => true, 'nullable' => false, 'default' => '0']
				,'Logotype Offset Left (%, 0..100)'
            );
			$t->addColumn(
				'top'
				,T::TYPE_DECIMAL
				,'4,2'
				,['unsigned' => true, 'nullable' => false, 'default' => '0']
				,'Logotype Offset Top (%, 0..100)'
            );
			$t->addColumn(
				'scale'
				,T::TYPE_DECIMAL
				,'4,2'
				,['unsigned' => true, 'nullable' => false, 'default' => '0']
				,'Logotype Scale (%, 0..100)'
            );
            $t->addIndex(
                $this->setup()->getIdxName(self::T, ['value_id', 'store_id'], IA::INDEX_TYPE_UNIQUE)
				,['value_id', 'store_id']
				,['type' => IA::INDEX_TYPE_UNIQUE]
            );
			$t->addForeignKey(
				$this->setup()->getFkName(self::T, 'value_id', $this->t(G::GALLERY_TABLE), 'value_id')
				,'value_id', $this->t(G::GALLERY_TABLE), 'value_id', T::ACTION_CASCADE
			);
            $t->addForeignKey(
                $this->setup()->getFkName(self::T, 'store_id', $this->t('store'), 'store_id')
				,'store_id', $this->t('store'),'store_id', T::ACTION_CASCADE
            );
            $t->setComment('https://github.com/mage2pro/logo');
			$this->c()->createTable($t);
		}
	}

	/**
	 * 2018-03-21
	 * @used-by self::_process()
	 */
	const T = 'catalog_product_entity_media_gallery_value_logo';
}