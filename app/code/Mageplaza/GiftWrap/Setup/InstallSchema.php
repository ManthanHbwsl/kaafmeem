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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\GiftWrap\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (!$setup->tableExists('mageplaza_giftwrap_category')) {
            $table = $connection->newTable($setup->getTable('mageplaza_giftwrap_category'))
                ->addColumn('category_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ])
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('description', Table::TYPE_TEXT, '1M')
                ->addColumn('store_id', Table::TYPE_TEXT, 255, ['nullable' => false])
                ->addColumn('customer_group', Table::TYPE_TEXT, 255, ['nullable' => false])
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addIndex($setup->getIdxName('mageplaza_giftwrap_category', ['category_id']), ['category_id'])
                ->setComment('Mageplaza GiftWrap Category');

            $connection->createTable($table);
        }

        if (!$setup->tableExists('mageplaza_giftwrap_wrap')) {
            $table = $connection->newTable($setup->getTable('mageplaza_giftwrap_wrap'))
                ->addColumn('wrap_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ])
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('price_type', Table::TYPE_SMALLINT, null, ['nullable' => false])
                ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0])
                ->addColumn('description', Table::TYPE_TEXT, '1M')
                ->addColumn('image', Table::TYPE_TEXT, 255)
                ->addColumn('category', Table::TYPE_TEXT, 255)
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addIndex($setup->getIdxName('mageplaza_giftwrap_wrap', ['wrap_id']), ['wrap_id'])
                ->setComment('Mageplaza GiftWrap Wrap');

            $connection->createTable($table);
        }

        if (!$setup->tableExists('mageplaza_giftwrap_history')) {
            $table = $connection->newTable($setup->getTable('mageplaza_giftwrap_history'))
                ->addColumn('history_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ])
                ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false])
                ->addColumn('incr_id', Table::TYPE_TEXT, 32, ['nullable' => false])
                ->addColumn('wrap_id', Table::TYPE_INTEGER, null, ['nullable' => false])
                ->addColumn('wrap', Table::TYPE_TEXT, 255)
                ->addColumn('category', Table::TYPE_TEXT, '1M')
                ->addColumn('image', Table::TYPE_TEXT, 255)
                ->addColumn('product_list', Table::TYPE_TEXT, 255)
                ->addColumn('message', Table::TYPE_TEXT, '1M')
                ->addColumn('order_date', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addIndex(
                    $setup->getIdxName('mageplaza_giftwrap_history', ['history_id', 'wrap_id']),
                    ['history_id', 'wrap_id']
                )
                ->setComment('Mageplaza GiftWrap History');

            $connection->createTable($table);
        }

        $setup->endSetup();
    }
}
