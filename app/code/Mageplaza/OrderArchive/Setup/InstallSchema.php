<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Mageplaza\OrderArchive\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(Unused)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if (!$connection->tableColumnExists($installer->getTable('sales_order'), 'is_archive')) {
            $connection->addColumn($installer->getTable('sales_order'), 'is_archive', [
                'type'    => Table::TYPE_BOOLEAN,
                1,
                ['identity' => false, 'nullable' => false, 'primary' => true],
                'comment' => 'Is Archive'
            ]);
        }

        if (!$connection->tableColumnExists($installer->getTable('sales_order_grid'), 'is_archive')) {
            $connection->addColumn($installer->getTable('sales_order_grid'), 'is_archive', [
                'type'    => Table::TYPE_BOOLEAN,
                1,
                ['identity' => false, 'nullable' => false, 'primary' => true],
                'comment' => 'Is Archive'
            ]);
        }

        $installer->endSetup();
    }
}
