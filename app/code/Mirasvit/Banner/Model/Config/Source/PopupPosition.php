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
 * @package   mirasvit/module-banner
 * @version   1.1.10
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Banner\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;

class PopupPosition implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $items = [];

        foreach ($this->toArray() as $value => $label) {
            $items[] = [
                'value' => $value,
                'label' => __($label)
            ];
        }

        return $items;
    }

    public function toArray(): array
    {
        return [
            'bottom-right' => 'Bottom Right',
            'bottom-left'  => 'Bottom Left',
            'top-right'    => 'Top Right',
            'top-left'     => 'Top Left'
        ];
    }
}
