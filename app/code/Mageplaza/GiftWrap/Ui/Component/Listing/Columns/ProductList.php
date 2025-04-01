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
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class ProductList
 * @package Mageplaza\GiftWrap\Ui\Component\Listing\Columns
 */
class ProductList extends Column
{
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
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $name = $this->getData('name');
        $data = Data::jsonDecode($item[$name]);
        $result = '';

        foreach ($data as $datum) {
            $result .= $datum['name'] . ' x ' . $datum['qty'] . '<br/>';
        }

        return $result ?: $item[$name];
    }
}
