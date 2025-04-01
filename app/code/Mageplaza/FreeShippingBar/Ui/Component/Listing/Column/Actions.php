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

namespace Mageplaza\FreeShippingBar\Ui\Component\Listing\Column;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Mageplaza\FreeShippingBar\Ui\Component\Listing\Column
 */
class Actions extends Column
{
    /** Url path */
    const SHIPPINGBAR_URL_PATH_EDIT = 'mpfreeshippingbar/shippingbar/edit';
    const SHIPPINGBAR_URL_PATH_DELETE = 'mpfreeshippingbar/shippingbar/delete';
    const SHIPPINGBAR_URL_PATH_PAUSE = 'mpfreeshippingbar/shippingbar/pause';
    const SHIPPINGBAR_URL_PATH_ENABLE = 'mpfreeshippingbar/shippingbar/enable';
    const SHIPPINGBAR_URL_PATH_DUPLICATE = 'mpfreeshippingbar/shippingbar/duplicate';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Actions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = [],
        $editUrl = self::SHIPPINGBAR_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        $this->escaper = $escaper;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['rule_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['rule_id' => $item['rule_id']]),
                        'label' => __('Edit')
                    ];
                    $title = $this->getEscaper()->escapeHtml($item['name']);
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::SHIPPINGBAR_URL_PATH_DELETE, [
                            'rule_id' => $item['rule_id']
                        ]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a %1 record?', $title)
                        ]
                    ];
                    $item[$name]['pause'] = [
                        'href' => $this->urlBuilder->getUrl(self::SHIPPINGBAR_URL_PATH_PAUSE, [
                            'rule_id' => $item['rule_id']
                        ]),
                        'label' => __('Pause'),
                        'confirm' => [
                            'title' => __('Pause %1', $title),
                            'message' => __('Are you sure you want to pause a %1 record?', $title)
                        ]
                    ];
                    $item[$name]['enable'] = [
                        'href' => $this->urlBuilder->getUrl(self::SHIPPINGBAR_URL_PATH_ENABLE, [
                            'rule_id' => $item['rule_id']
                        ]),
                        'label' => __('Enable'),
                        'confirm' => [
                            'title' => __('Enable %1', $title),
                            'message' => __('Are you sure you want to enable a %1 record?', $title)
                        ]
                    ];
                    $item[$name]['duplicate'] = [
                        'href' => $this->urlBuilder->getUrl(self::SHIPPINGBAR_URL_PATH_DUPLICATE, [
                            'rule_id' => $item['rule_id']
                        ]),
                        'label' => __('Duplicate'),
                        'confirm' => [
                            'title' => __('Duplicate %1', $title),
                            'message' => __('Are you sure you want to duplicate a %1 record?', $title)
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get instance of escaper
     * @return Escaper
     * @deprecated 101.0.7
     */
    private function getEscaper()
    {
        if (!$this->escaper) {
            $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
        }

        return $this->escaper;
    }
}
