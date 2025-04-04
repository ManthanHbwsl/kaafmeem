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
 * @package   mirasvit/module-reports
 * @version   1.5.3
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Config\Type;

use Magento\Quote\Model\QuoteRepository;
use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;

class QuoteProducts implements TypeInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE_STR;
    }

    /**
     * @return array|string[]
     */
    public function getAggregators()
    {
        return [AggregatorInterface::TYPE_NONE];
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return self::VALUE_TYPE_STRING;
    }

    /**
     * @return string
     */
    public function getJsType()
    {
        return self::JS_TYPE_HTML;
    }

    /**
     * @return bool|string
     */
    public function getJsFilterType()
    {
        return false;
    }

    /**
     * @param number|string $actualValue
     * @param AggregatorInterface $aggregator
     * @return number|string
     */
    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        $html = [];

        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($actualValue);

            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllVisibleItems() as $item) {
                $title = __('%1 [%2] × %3', $item->getName(), $item->getSku(), intval($item->getQty()));

                $html[] = $title;
            }
        } catch (\Exception $e) {
            return self::NA;
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param number|string $actualValue
     * @param AggregatorInterface $aggregator
     * @return number|string
     */
    public function getPk($actualValue, AggregatorInterface $aggregator)
    {
        return $actualValue;
    }
}
