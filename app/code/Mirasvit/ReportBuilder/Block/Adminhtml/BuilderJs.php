<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-builder
 * @version   1.1.3
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportBuilder\Block\Adminhtml;

use Magento\Backend\Block\Template;

class BuilderJs extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_ReportBuilder::builder_js.phtml';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * BuilderJs constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->serializer = $serializer;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }

    public function jsonEncode($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->urlBuilder->getUrl('reportBuilder/api/duplicate');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->urlBuilder->getUrl('reportBuilder/api/save');
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->urlBuilder->getUrl('reportBuilder/api/delete');
    }
}
