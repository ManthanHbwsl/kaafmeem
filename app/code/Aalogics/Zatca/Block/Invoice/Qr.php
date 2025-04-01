<?php

namespace Aalogics\Zatca\Block\Invoice;

use Aalogics\Zatca\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
/**
 * Class Qr
 * @package Aalogics\Zatca\Block\Invoice
 */
class Qr  extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var Data
     */
    protected $helper;


    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
        ) {

        $this->helper = $helper;
        parent::__construct($context,$data);
    }


    public function getHelper() {
        return $this->helper;
    }
}
