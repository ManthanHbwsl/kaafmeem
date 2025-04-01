<?php

namespace Web\CustomerDob\Block\Widget;

use Magento\Framework\View\Element\Template;

class Dob extends \Magento\Customer\Block\Widget\Dob {

	public function getHtmlExtraParams()
    {
        $validators = [];
        if ($this->isRequired()) {
            $validators['required'] = true;
        }
        $validators['validate-date'] = [
            'dateFormat' => $this->getDateFormat()
        ];
        $validators['validate-dob'] = true;
		return '';
        return 'data-validate="' . $this->_escaper->escapeHtml(json_encode($validators)) . '"';
    }
}