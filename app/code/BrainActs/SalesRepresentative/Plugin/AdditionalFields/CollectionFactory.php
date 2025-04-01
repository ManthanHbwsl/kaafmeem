<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Plugin\AdditionalFields;

//@codingStandardsIgnoreFile
class CollectionFactory
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return mixed
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName === 'sales_order_grid_data_source' && $result instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection) {
            $result = $this->updateResultOrder($result);
        }

        if ($requestName === 'customer_listing_data_source' && $result instanceof \Magento\Customer\Model\ResourceModel\Grid\Collection) {
            $result = $this->updateCustomerResult($result);
        }
        return $result;
    }

    /**
     * @param $result
     * @return mixed
     */
    private function updateResultOrder($result)
    {
        $tableName= 'brainacts_salesrep_member_order';
        $tableMember = $result->getTable($tableName);
        $bsmTable = $result->getTable('brainacts_salesrep_member');
        $select = new \Zend_Db_Expr(
            "(SELECT group_concat(firstname, ' ', lastname SEPARATOR ', ') FROM " . $tableMember .
            " LEFT JOIN {$bsmTable} ON {$tableMember}.member_id = {$bsmTable}.member_id
                    WHERE order_id=main_table.entity_id)"
        );

        //add code to join table, and mapping field to select
        $expr = 'CONCAT(related_member.firstname, " ", related_member.lastname)';
        $result->getSelect()->columns(['representative' => $select])
            ->joinLeft(
                ['related' => $tableMember],
                'related.order_id = main_table.entity_id',
                ['member_id']
            )->joinLeft(
                ['related_member' => $result->getTable('brainacts_salesrep_member')],
                'related.member_id = related_member.member_id',
                ['member_name' => new \Zend_Db_Expr($expr)]
            )->group(['main_table.entity_id']);

        return $result;
    }

    /**
     * @param $result
     * @return mixed
     */
    private function updateCustomerResult($result)
    {
        $tableName= 'brainacts_salesrep_member_customer';
        $tableMember = $result->getTable($tableName);
        $bsmTable = $result->getTable('brainacts_salesrep_member');
        $select = new \Zend_Db_Expr(
            "(SELECT group_concat(firstname, ' ', lastname SEPARATOR ', ') FROM " . $tableMember .
            " LEFT JOIN {$bsmTable} ON {$tableMember}.member_id = {$bsmTable}.member_id
                    WHERE customer_id=main_table.entity_id)"
        );

        //add code to join table, and mapping field to select
        $expr = 'CONCAT(related_member.firstname, " ", related_member.lastname)';
        $result->getSelect()->columns(['representative' => $select])
            ->joinLeft(
                ['related' => $tableMember],
                'related.customer_id = main_table.entity_id',
                ['member_id']
            )->joinLeft(
                ['related_member' => $result->getTable('brainacts_salesrep_member')],
                'related.member_id = related_member.member_id',
                ['member_name' => new \Zend_Db_Expr($expr)]
            )->group(['main_table.entity_id']);

        return $result;
    }
}
