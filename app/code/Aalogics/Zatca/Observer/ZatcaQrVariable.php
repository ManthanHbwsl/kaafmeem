<?php
namespace Aalogics\Zatca\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Aalogics\Zatca\Helper\Data;




class ZatcaQrVariable implements ObserverInterface
{
    /*
     * @var \Aalogics\Zatca\Helper\Data;
     */
    protected $helper;
    
    
    
    public function __construct(
        \Aalogics\Zatca\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transport = $observer->getEvent()->getData('transportObject');
        $invoice = $transport->getInvoice();
        if ($invoice != null)
        {
            $zatcaQr = $this->helper->getZatcaQR($invoice, true);
            $zatcaDescription = $this->helper->getZatcaQRDescription();
            $transport->setData('zatca_qr',$zatcaQr) ;
            $transport->setData('zatca_qr_description', $zatcaDescription) ;
        }
    }
}