<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Captcha;

/**
 * Captcha block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class DefaultCaptcha extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Captcha::default.phtml';

    /**
     * @var string
     */
    protected $_captcha;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_captchaData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Captcha\Helper\Data $captchaData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_captchaData = $captchaData;
    }

    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getIsAjax() ? '' : $this->_template;
    }

    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        $store = $this->_storeManager->getStore();
        return $store->getUrl('captcha/refresh', ['_secure' => $store->isCurrentlySecure()]);
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getCaptchaModel()->generate();
		return parent::_toHtml();
    }

    /**
     * Returns captcha model
     *
     * @return \Magento\Captcha\Model\CaptchaInterface
     */
    public function getCaptchaModel()
    {
        return $this->_captchaData->getCaptcha($this->getFormId());
    }
}
