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

namespace Mageplaza\OrderArchive\Plugin\Order;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction;
use Mageplaza\OrderArchive\Helper\Data as HelperData;

/**
 * Class AddArchiveAction
 *
 * @package Mageplaza\OrderArchive\Plugin\Order
 */
class AddArchiveAction
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * AddDeleteAction constructor.
     *
     * @param HelperData $helper
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        HelperData $helper,
        AuthorizationInterface $authorization
    ) {
        $this->helper         = $helper;
        $this->_authorization = $authorization;
    }

    /**
     * @param MassAction $object
     * @param UiComponentInterface[] $result
     *
     * @return UiComponentInterface[]
     * @SuppressWarnings(Unused)
     */
    public function afterGetChildComponents(MassAction $object, $result)
    {
        if (isset($result['mp_archive']) && (!$this->helper->isEnabled() || !$this->_authorization->isAllowed('Magento_Sales::archive'))) {
            unset($result['mp_archive']);
        }

        if (isset($result['mp_unarchive']) && (!$this->helper->isEnabled() || !$this->_authorization->isAllowed('Magento_Sales::unarchive'))) {
            unset($result['mp_unarchive']);
        }

        return $result;
    }
}
