<?php
namespace Aalogics\Zatca\Model\Adminhtml\System\Config\Source;


class Position implements \Magento\Framework\Option\ArrayInterface
{
    protected $_userFactory;
    
    
    
    public function getOptions()
    {
        //@todo move all labels in translate function
        $res = [];
        $res[] = ['value' => 'top', 'label' => 'TOP'];
        $res[] = ['value' => 'middle', 'label' => 'After Address'];
        $res[] = ['value' => 'footer', 'label' => 'Footer'];
        return $res;
    }
    
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}