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
 * @package   mirasvit/module-banner
 * @version   1.1.10
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Banner\Model\ConfigProvider;

class Debug extends Template
{
    private $configProvider;
    private $serializer;

    public function __construct(
        ConfigProvider $configProvider,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        Context $context
    ) {
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;

        parent::__construct($context);
    }

    public function toHtml()
    {
        if (!$this->configProvider->isDebug()) {
            return '';
        }

        return '<script type="text/x-magento-init">' . $this->serializer->serialize($this->getJsConfig()) . '</script>';
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return [
            '*' => [
                'Mirasvit_Banner/js/debug' => [],
            ],
        ];
    }

}
