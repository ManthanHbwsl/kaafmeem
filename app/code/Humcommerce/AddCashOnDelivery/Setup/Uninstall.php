<?php
declare(strict_types=1);

namespace Humcommerce\AddCashOnDelivery\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();
       
        foreach ($this->_getTables() as $tableName) {

            $setup->getConnection()->dropColumn(
                $setup->getTable($tableName),
                'cod_amount'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable($tableName),
                'base_cod_amount'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable($tableName),
                'total_cod_amount'
            );
        }
        
        $setup->endSetup();
    }

    /**
     * @return string[]
     */
    private function _getTables()
    {
        return [
            'sales_order',
            'sales_invoice',
            'sales_creditmemo',
        ];
    }
}
