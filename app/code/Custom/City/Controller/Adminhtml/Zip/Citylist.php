<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\Zip;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\City\Model\CityFactory;
use Custom\City\Model\Resource\City\Collection as CityCollection;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;

class Citylist extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var CityCollection */
    protected $_cityCollection;

    /** @var CityFactory */
    protected $_cityFactory;

    /** @var Data */
    protected $jsonHelper;

    public function __construct(
        Context $context,
        CityFactory $cityFactory,
        CityCollection $cityCollection,
        PageFactory $resultPageFactory,
        Data $jsonHelper
    ) {
        $this->_cityFactory = $cityFactory;
        $this->_cityCollection = $cityCollection;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     *
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $city = "<option value=''>" . __('--Please Select--') . "</option>";
        if (isset($params['state']) && !empty($params['state'])) {
            $citiesArray = $this->_cityCollection->addFieldToFilter('state_id', $params['state']);
            foreach ($citiesArray as $_city) {
                if ($_city['id']) {
                    $value = $_city['id'];
                    $city .= "<option value='" . $value . "'>" . __($_city['city']) . "</option>";
                }
            }
        } elseif (!isset($params['state']) && (isset($params['country']) && !empty($params['country']))) {
            $citiesArray = $this->_cityCollection
                ->addFieldToFilter('country_id', $params['country']);
            foreach ($citiesArray as $_city) {
                if ($_city['id']) {
                    $value = $_city['id'];
                    $city .= "<option value='" . $value . "'>" . __($_city['city']) . "</option>";
                }
            }
        }
        $result['htmlconent'] = $city;
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

}
