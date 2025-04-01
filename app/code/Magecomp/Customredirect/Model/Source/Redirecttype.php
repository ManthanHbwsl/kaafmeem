<?php
namespace Magecomp\Customredirect\Model\Source;

class Redirecttype
{
    public function toOptionArray()
    {
        return [
			['value' => 0, 'label'=>__('CMS Pages')],
            ['value' => 1, 'label'=>__('Custom Path')],
        ];
    }
}