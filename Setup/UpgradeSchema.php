<?php

namespace Experius\PageNotFound\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->addIndexesToTables($setup);
        }
        $setup->endSetup();
    }

    public function addIndexesToTables($setup)
    {
        $setup->getConnection()->query("ALTER TABLE `{$setup->getTable('experius_page_not_found')}` ADD INDEX `EXPERIUS_PAGE_NOT_FOUND_FROM_URL` (`from_url`(255));");
    }
}
