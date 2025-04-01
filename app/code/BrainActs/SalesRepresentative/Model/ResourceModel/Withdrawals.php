<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel;

use BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Withdrawals
 *
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Withdrawals extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_init('brainacts_salesrep_withdrawals', 'withdrawal_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(WithdrawalsInterface::class)->getEntityConnection();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $withdrawalId = $this->getWithdrawalId($object, $value, $field);
        if ($withdrawalId) {
            $this->entityManager->load($object, $withdrawalId);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param $value
     * @param null $field
     * @return bool
     */
    private function getWithdrawalId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(WithdrawalsInterface::class);

        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }
}
