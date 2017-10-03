<?php


namespace Experius\PageNotFound\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_experius_page_not_found = $setup->getConnection()->newTable($setup->getTable('experius_page_not_found'));

        
        $table_experius_page_not_found->addColumn(
            'page_not_found_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_experius_page_not_found->addColumn(
            'from_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'from_url'
        );
        

        
        $table_experius_page_not_found->addColumn(
            'to_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'to_url'
        );
        

        
        $table_experius_page_not_found->addColumn(
            'count',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'count'
        );
        

        
        $table_experius_page_not_found->addColumn(
            'count_redirect',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'count_redirect'
        );
        

        $setup->getConnection()->createTable($table_experius_page_not_found);

        $setup->endSetup();
    }
}
