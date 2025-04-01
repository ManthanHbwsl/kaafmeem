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
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Helper;

use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 *
 * @package Mageplaza\OrderArchive\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mporderarchive';
    const ALL_STORE_ID       = '0';

    /**
     * @param  $days
     *
     * @return false|string
     */
    public function setDate($days)
    {
        return date('Y-m-d H:i:s', strtotime('-' . $days . ' days'));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowArchive($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getConfigGeneral('show_frontend', $storeId);
    }

    /**
     * @param  $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getScheduleConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('schedule/' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderStatusConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('order_status', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderCustomerGroupConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('customer_groups', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getStoreViewConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('store_views', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getShippingCountryType($storeId = null)
    {
        return $this->getScheduleConfig('country', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getCountriesConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('specific_country', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrderTotalConfig($storeId = null)
    {
        return $this->getScheduleConfig('order_under', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPeriodConfig($storeId = null)
    {
        return $this->getScheduleConfig('day_before', $storeId);
    }

    /**
     * @return bool
     */
    public function isBetterOrderGridEnable()
    {
        if ($this->_moduleManager->isEnabled('Mageplaza_BetterOrderGrid')) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function isBetterOrderGridHideOrder()
    {
        if ($this->isBetterOrderGridEnable()) {
            $gridHelper = $this->objectManager->create(\Mageplaza\BetterOrderGrid\Helper\Data::class);

            return $gridHelper->getOrderStatus();
        }

        return null;
    }
}
