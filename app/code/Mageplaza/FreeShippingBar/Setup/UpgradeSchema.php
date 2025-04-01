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
 * @category    Mageplaza
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\FreeShippingBar\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('mageplaza_freeshippingbar_rules');
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            if ($installer->getConnection()->tableColumnExists($tableName, 'customer_group_id') === false) {
                $installer->getConnection()->addColumn($tableName, 'customer_group_id', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Customer Group Id',
                    'after' => 'store_id'
                ]);
            }
            if ($installer->getConnection()->tableColumnExists($tableName, 'from_date') === false) {
                $installer->getConnection()->addColumn($tableName, 'from_date', [
                    'type' => Table::TYPE_DATETIME,
                    'comment' => 'From date',
                    'after' => 'to_date'
                ]);
            }
        }

        $installer->endSetup();
    }
}
