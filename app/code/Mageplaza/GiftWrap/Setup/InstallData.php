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

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Mageplaza\GiftWrap\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class InstallData
 * @package Mageplaza\GiftWrap\Setup
 */
class InstallData implements InstallDataInterface
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        LoggerInterface $logger
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->logger = $logger;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $quoteData = [
            ['table' => 'quote', 'column' => 'mp_gift_wrap_tax'],
            ['table' => 'quote', 'column' => 'mp_gift_wrap_amount'],
            ['table' => 'quote', 'column' => 'mp_gift_wrap_base_amount'],
        ];
        $salesData = [
            ['table' => 'order', 'column' => 'mp_gift_wrap_tax'],
            ['table' => 'order', 'column' => 'mp_gift_wrap_amount'],
            ['table' => 'order', 'column' => 'mp_gift_wrap_base_amount'],
            ['table' => 'invoice', 'column' => 'mp_gift_wrap_amount'],
            ['table' => 'invoice', 'column' => 'mp_gift_wrap_base_amount'],
            ['table' => 'creditmemo', 'column' => 'mp_gift_wrap_amount'],
            ['table' => 'creditmemo', 'column' => 'mp_gift_wrap_base_amount'],
        ];

        foreach ($quoteData as $item) {
            $quoteInstaller->addAttribute($item['table'], $item['column'], [
                'type' => Table::TYPE_DECIMAL,
                'visible' => false
            ]);
        }
        foreach ($salesData as $item) {
            $salesInstaller->addAttribute($item['table'], $item['column'], [
                'type' => Table::TYPE_DECIMAL,
                'visible' => false
            ]);
        }

        $quoteInstaller->addAttribute('quote_item', Data::GIFT_WRAP_DATA, [
            'type' => Table::TYPE_TEXT,
            'size' => '1M',
            'visible' => false
        ]);
        $salesInstaller->addAttribute('order_item', Data::GIFT_WRAP_DATA, [
            'type' => Table::TYPE_TEXT,
            'size' => '1M',
            'visible' => false
        ]);
        $salesInstaller->addAttribute('invoice', 'mp_gift_wrap_all_cart_applied', [
            'type' => Table::TYPE_SMALLINT,
            'visible' => false
        ]);
        $salesInstaller->addAttribute('creditmemo', 'mp_gift_wrap_all_cart_applied', [
            'type' => Table::TYPE_SMALLINT,
            'visible' => false
        ]);

        $this->copyIconDefault();
    }

    /**
     * Copy icon default to media path
     */
    protected function copyIconDefault()
    {
        try {
            /** @var Filesystem\Directory\WriteInterface $mediaDirectory */
            $mediaDirectory = ObjectManager::getInstance()->get(Filesystem::class)
                ->getDirectoryWrite(DirectoryList::MEDIA);

            $mediaDirectory->create('mageplaza/giftwrap/default');
            $targetPath = $mediaDirectory->getAbsolutePath('mageplaza/giftwrap/default/gift.png');

            $DS = DIRECTORY_SEPARATOR;
            $oriPath = dirname(__DIR__) .
                                $DS . 'view' .
                                $DS . 'frontend' .
                                $DS . 'web' .
                                $DS . 'images' .
                                $DS . 'default' .
                                $DS . 'gift.png';

            $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
