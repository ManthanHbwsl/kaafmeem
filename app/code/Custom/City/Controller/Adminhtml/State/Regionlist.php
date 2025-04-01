<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;

class Regionlist extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var CountryFactory */
    protected $_countryFactory;

    /** @var Data */
    protected $jsonHelper;

    public function __construct(
        Context $context,
        CountryFactory $countryFactory,
        PageFactory $resultPageFactory,
        Data $jsonHelper
    ) {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $countryCode = $this->getRequest()->getParam('country');
        $state = "<option value=''>" . __('--Please Select--') . "</option>";
        if ($countryCode != '') {
            $stateArray = $this->_countryFactory->create()->setId(
                $countryCode
            )->getLoadedRegionCollection()->toOptionArray();
            foreach ($stateArray as $_state) {
                if ($_state['value']) {
                    $value = $_state['value'];
                    $state .= "<option value='" . $value . "'>" . $_state['label'] . "</option>";
                }
            }
        }
        $result['htmlconent'] = $state;
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

}