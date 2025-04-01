<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Humcommerce\AddCashOnDelivery\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\DB\Ddl\Table;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class AddCashOnDelivery implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        foreach ($this->_getTables() as $tableName) {

            $table = $this->moduleDataSetup->getTable($tableName);

            $this->moduleDataSetup->getConnection()->addColumn(
                $table,
                'cod_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Cash on Delivery',
                    'length' => '12,4',
                    'unsigned' => false,
                ]
            );
            
            $this->moduleDataSetup->getConnection()->addColumn(
                $table,
                'base_cod_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Base Cash on Delivery',
                    'length' => '12,4',
                    'unsigned' => false,
                ]
            );

            $this->moduleDataSetup->getConnection()->addColumn(
                $table,
                'total_cod_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Total Cash on Delivery',
                    'length' => '12,4',
                    'unsigned' => false,
                ]
            );

            $this->updateTable($table);
        }
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

     /**
     * Upgrade Table
     *
     * @return void
     */
    public function updateTable($table)
    {
        //update the column with previous cash on delivery values
        $this->moduleDataSetup->getConnection()->update(
            $table,
            ['cod_amount' => new \Zend_Db_Expr('cod_fee')]
        );

        $this->moduleDataSetup->getConnection()->update(
            $table,
            ['base_cod_amount' => new \Zend_Db_Expr('base_cod_fee')]
        );

        $this->moduleDataSetup->getConnection()->update(
            $table,
            ['total_cod_amount' => new \Zend_Db_Expr('cod_fee')]
        );

        //update the column with amasty cash on delivery values
        if($table == 'mgzs_sales_order'){
            $queryColumn =  'entity_id = ?';
        }else{
            $queryColumn =  'order_id = ?';
        }
        $amastyTable = $this->moduleDataSetup->getTable('amasty_cash_on_delivery_fee_order');
        $query = $this->moduleDataSetup->getConnection()->select()
            ->from($amastyTable, ['order_id', 'amount', 'base_amount', 'tax_amount'])
            ->where('amount IS NOT NULL');

        $data = $this->moduleDataSetup->getConnection()->fetchAll($query);

        foreach ($data as $row) {
            $this->moduleDataSetup->getConnection()->update(
                $table,
                ['cod_amount' => $row['amount']],
                [$queryColumn => $row['order_id']]
            );
            $this->moduleDataSetup->getConnection()->update(
                $table,
                ['base_cod_amount' => $row['base_amount']],
                [$queryColumn => $row['order_id']]
            );
            $this->moduleDataSetup->getConnection()->update(
                $table,
                ['total_cod_amount' => $row['amount']+$row['tax_amount']],
                [$queryColumn => $row['order_id']]
            );
        }

        
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

}
