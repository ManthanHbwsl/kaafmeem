<?php


namespace Srp\CustomPrice\Plugin\Magento\Tax\Model\Sales\Total\Quote;
use Magento\Framework\App\ObjectManager;
use Magento\Tax\Helper\Data as TaxHelper;

class CommonTaxCollector
{
	private $taxHelper;

	public function __construct(
        TaxHelper $taxHelper = null
    ) {
        $this->taxHelper = $taxHelper ?: ObjectManager::getInstance()->get(TaxHelper::class);
    }

    public function afterUpdateItemTaxInfo(
        \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector $subject,
        $result,
        $quoteItem,
        $itemTaxDetails
    ) {
        if ($quoteItem->getCustomPrice() && $this->taxHelper->applyTaxOnCustomPrice()) {
            $quoteItem->setCustomPrice($itemTaxDetails->getPrice());
        }

        return $result;
    }
}

