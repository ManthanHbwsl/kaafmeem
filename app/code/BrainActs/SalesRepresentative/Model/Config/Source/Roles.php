<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Roles
 *
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Roles implements ArrayInterface
{
    /**
     * @var \Magento\User\Model\ResourceModel\Role\User\CollectionFactory
     */
    private $userRolesFactory;

    public function __construct(
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
    ) {
        $this->userRolesFactory = $userRolesFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $collection = $this->userRolesFactory->create();
        $collection->setRolesFilter();

        $options = [];
        $options [] = [
            'value' => '',
            'label' => __(' -- None  -- ')
        ];

        foreach ($collection as $role) {
            $options [] = [
                'value' => $role->getId(),
                'label' => $role->getRoleName()
            ];
        }

        return $options;
    }
}
