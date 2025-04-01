<?php
/**
 * @category   Webiators
 * @package    Webiators_AutoInvoiceAndShipment
 * @author     Webiators Team
 * @copyright  Copyright (c) Webiators Technologies Private Limited. ( https://webiators.com ).
 */
namespace Webiators\AutoInvoiceAndShipment\Model\Config\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

class Adminpayments extends \Magento\Framework\DataObject 
implements \Magento\Framework\Option\ArrayInterface
{
  protected $_appConfigScopeConfigInterface;
  protected $_paymentModelConfig;
  public function __construct(
    ScopeConfigInterface $appConfigScopeConfigInterface,
    Config $paymentModelConfig
  ) {
    $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
    $this->_paymentModelConfig = $paymentModelConfig;
  }
  public function toOptionArray()
  {
    $payments = $this->_paymentModelConfig->getActiveMethods();
    $methods = array();
    foreach ($payments as $paymentCode => $paymentModel) {
      $paymentTitle = $this->_appConfigScopeConfigInterface
      ->getValue('payment/'.$paymentCode.'/title');
      $methods[$paymentCode] = array(
        'label' => $paymentTitle,
        'value' => $paymentCode
      );
    }
    return $methods;
  }
}