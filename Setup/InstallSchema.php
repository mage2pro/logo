<?php

namespace Dfe\Logo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        

        $installer->getConnection()->dropTable($installer->getTable('optionimages_value'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionimages_value'))
            ->addColumn('oi_value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'Logo Value Id')
            ->addColumn('option_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Option Type Id')      
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
                'default'   => ''        
                ), 'Image')                          
            ->addIndex($installer->getIdxName('optionimages_value', array('option_type_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('option_type_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))                
            ->addForeignKey(
                $installer->getFkName('optionimages_value', 'option_type_id', 'catalog_product_option_type_value', 'option_type_id'),
                'option_type_id', $installer->getTable('catalog_product_option_type_value'), 'option_type_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)       
            ->setComment('Logo Value');
    
        $installer->getConnection()->createTable($table);
   
   
        $setup->endSetup();

    }
}
