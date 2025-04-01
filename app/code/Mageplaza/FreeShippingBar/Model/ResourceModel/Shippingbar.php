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

namespace Mageplaza\FreeShippingBar\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Shippingbar
 * @package Mageplaza\FreeShippingBar\Model\ResourceModel
 */
class Shippingbar extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_freeshippingbar_rules', 'rule_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws Exception
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        if ($object->isObjectNew()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        return parent::_beforeSave($object);
    }
}
