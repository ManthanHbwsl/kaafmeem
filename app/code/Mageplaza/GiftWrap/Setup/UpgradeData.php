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
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class UpgradeData
 * @package Mageplaza\GiftWrap\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $quoteData = [
            ['table' => 'quote', 'column' => 'mp_gift_wrap_postcard_amount'],
            ['table' => 'quote', 'column' => 'mp_gift_wrap_postcard_base_amount'],
        ];
        $salesData = [
            ['table' => 'order', 'column' => 'mp_gift_wrap_postcard_amount'],
            ['table' => 'order', 'column' => 'mp_gift_wrap_postcard_base_amount'],
            ['table' => 'invoice', 'column' => 'mp_gift_wrap_postcard_amount'],
            ['table' => 'invoice', 'column' => 'mp_gift_wrap_postcard_base_amount'],
            ['table' => 'invoice', 'column' => 'mp_gift_wrap_postcard_all_cart_applied'],
            ['table' => 'creditmemo', 'column' => 'mp_gift_wrap_postcard_amount'],
            ['table' => 'creditmemo', 'column' => 'mp_gift_wrap_postcard_base_amount'],
            ['table' => 'creditmemo', 'column' => 'mp_gift_wrap_postcard_all_cart_applied'],
        ];

        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $quoteInstaller->addAttribute('quote_item', Data::GIFT_WRAP_POSTCARD_DATA, [
                'type'    => Table::TYPE_TEXT,
                'size'    => '1M',
                'visible' => false
            ]);
            $salesInstaller->addAttribute('order_item', Data::GIFT_WRAP_POSTCARD_DATA, [
                'type'    => Table::TYPE_TEXT,
                'size'    => '1M',
                'visible' => false
            ]);

            foreach ($quoteData as $item) {
                $quoteInstaller->addAttribute($item['table'], $item['column'], [
                    'type'    => Table::TYPE_DECIMAL,
                    'visible' => false
                ]);
            }

            foreach ($salesData as $item) {
                $salesInstaller->addAttribute($item['table'], $item['column'], [
                    'type'    => $item['column'] !== 'mp_gift_wrap_postcard_all_cart_applied'
                        ? Table::TYPE_DECIMAL : Table::TYPE_SMALLINT,
                    'visible' => false
                ]);
            }
        }

        $setup->endSetup();
    }
}
