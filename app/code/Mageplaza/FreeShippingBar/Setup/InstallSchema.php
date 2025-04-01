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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\FreeShippingBar\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        /** Table mageplaza_freeshippingbar_rule */
        if ($installer->tableExists('mageplaza_freeshippingbar_rules')) {
            $connection->dropTable($installer->getTable('mageplaza_freeshippingbar_rules'));
        }
        $table = $connection->newTable($installer->getTable('mageplaza_freeshippingbar_rules'))
            ->addColumn('rule_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ], 'Rule ID')
            ->addColumn('name', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Name')
            ->addColumn('status', Table::TYPE_INTEGER, null, ['nullable => false'], 'Status')
            ->addColumn('priority', Table::TYPE_INTEGER, null, ['nullable => false'], 'Priority')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
            ->addColumn('from_date', Table::TYPE_TIMESTAMP, null, [], 'From Date')
            ->addColumn('to_date', Table::TYPE_DATETIME, null, [], 'To Date')
            ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
            ->addColumn('store_id', Table::TYPE_TEXT, 255, ['nullable => false'], 'Store Id')
            ->addColumn('goal', Table::TYPE_INTEGER, null, ['nullable => false'], 'Goal')
            ->addColumn('first_message', Table::TYPE_TEXT, '64k', ['nullable => false'], 'First Message')
            ->addColumn('below_goal_message', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Below Goal Message')
            ->addColumn('achieve_message', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Achieve Message')
            ->addColumn('click_able', Table::TYPE_INTEGER, null, ['nullable => false'], 'Click Able')
            ->addColumn('url_shippingbar', Table::TYPE_TEXT, '64k', [], 'Url Free Shipping Bar')
            ->addColumn('open_new_page', Table::TYPE_INTEGER, null, ['nullable => false'], 'Open New Page')
            ->addColumn('template', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Template')
            ->addColumn('bar_background', Table::TYPE_TEXT, 255, ['nullable => false'], 'Bar Background')
            ->addColumn('bar_opacity', Table::TYPE_DECIMAL, '12,4', [], 'Bar Opacity')
            ->addColumn('bar_text_color', Table::TYPE_TEXT, 255, ['nullable => false'], 'Bar Text Color')
            ->addColumn('bar_link_color', Table::TYPE_TEXT, 255, [], 'Bar Link Color')
            ->addColumn('goal_text_color', Table::TYPE_TEXT, 255, ['nullable => false'], 'Goal Text Color')
            ->addColumn('image', Table::TYPE_TEXT, 255, [], 'Image Background Pattern')
            ->addColumn('string_font_connect', Table::TYPE_TEXT, 255, ['nullable => false'], 'String Font Connect')
            ->addColumn('font_text', Table::TYPE_TEXT, 255, ['nullable => false'], 'Font Text')
            ->addColumn('font_size', Table::TYPE_INTEGER, null, ['nullable => false'], 'Font Size')
            ->addColumn('position', Table::TYPE_TEXT, 255, ['nullable => false'], 'Position')
            ->addColumn('allowshow', Table::TYPE_TEXT, 255, ['nullable => false'], 'Allow Show')
            ->addColumn('snippet_code', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Snippet Code')
            ->addColumn('include_pages', Table::TYPE_TEXT, '64k', [], 'Include Pages')
            ->addColumn('include_pages_url_contain', Table::TYPE_TEXT, '64k', [], 'Include Pages Url Contain')
            ->addColumn('exclude_pages', Table::TYPE_TEXT, '64k', [], 'Exclude Pages')
            ->addColumn('exclude_pages_url_contain', Table::TYPE_TEXT, '64k', [], 'Include Pages Url Contain')
            ->setComment('Free Shipping Bar Rules Table');
        $connection->createTable($table);

        $installer->endSetup();
    }
}
