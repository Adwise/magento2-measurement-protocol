<?php

namespace Adwise\Analytics\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /** @var string */
    private static $connectionName = 'sales';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $this->addAnalyticsStatus($setup);
        }
        if (version_compare($context->getVersion(), '1.4.0') < 0 ){
            $this->addAnalyticsExportedAmount($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addAnalyticsStatus(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection(self::$connectionName);

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'aw_analytics_export',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'GA exported'
            ]
        );
    }

    public function addAnalyticsExportedAmount(SchemaSetupInterface $setup) {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'aw_analytics_amount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'comment' => 'Amount GA has'
            ]
        );
    }
}
