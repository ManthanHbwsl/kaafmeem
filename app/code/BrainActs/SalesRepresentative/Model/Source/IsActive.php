<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\Source;

use BrainActs\SalesRepresentative\Model\Member;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\Member
     */
    private $salesrepMember;

    /**
     * @param \BrainActs\SalesRepresentative\Model\Member $salesrepMember
     */
    public function __construct(Member $salesrepMember)
    {
        $this->salesrepMember = $salesrepMember;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->salesrepMember->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
