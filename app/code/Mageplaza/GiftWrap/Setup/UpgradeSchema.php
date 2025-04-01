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
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\GiftWrap\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $columns = [
                'name'           => ['type' => Table::TYPE_TEXT, 'length' => 255, 'nullable' => false],
                'store_id'       => ['type' => Table::TYPE_TEXT, 'length' => 255, 'nullable' => true],
                'customer_group' => ['type' => Table::TYPE_TEXT, 'length' => 255, 'nullable' => true],
            ];

            foreach ($columns as $column => $type) {
                $connection->modifyColumn($setup->getTable('mageplaza_giftwrap_category'), $column, $type);
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if ($setup->tableExists('mageplaza_giftwrap_wrap')) {
                $wrapTable = $setup->getTable('mageplaza_giftwrap_wrap');
                if (!$connection->tableColumnExists($wrapTable, 'wrap_type')) {
                    $connection->addColumn($wrapTable, 'wrap_type', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length'   => '255',
                        'default'  => 'wrap',
                        'comment'  => 'Wrap Type'
                    ]);
                }
                if (!$connection->tableColumnExists($wrapTable, 'qty_limit')) {
                    $connection->addColumn($wrapTable, 'qty_limit', [
                        'type'     => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment'  => 'Qty Limit'
                    ]);
                }
            }

            if ($setup->tableExists('mageplaza_giftwrap_history')) {
                $wrapHistoryTable     = $setup->getTable('mageplaza_giftwrap_history');
                $amountHistoryColumns = [
                    'mp_gift_wrap_invoice_amount'         => 'Invoice Amount',
                    'mp_gift_wrap_invoice_base_amount'    => 'Invoice Base Amount',
                    'mp_gift_wrap_creditmemo_amount'      => 'Creditmemo Amount',
                    'mp_gift_wrap_creditmemo_base_amount' => 'Creditmemo Base Amount'
                ];

                foreach ($amountHistoryColumns as $column => $columnName) {
                    if (!$connection->tableColumnExists($wrapHistoryTable, $column)) {
                        $connection->addColumn($wrapHistoryTable, $column, [
                            'type'     => Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'length'   => '12,4',
                            'comment'  => $columnName
                        ]);
                    }
                }
            }
        }

        $setup->endSetup();
    }
}
