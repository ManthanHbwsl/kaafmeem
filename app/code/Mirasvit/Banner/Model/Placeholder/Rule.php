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



namespace Mirasvit\Banner\Model\Placeholder;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mirasvit\Banner\Model\Banner\Rule\DataObject;
use Mirasvit\Banner\Model\Banner\Rule\DataObjectFactory;

class Rule extends AbstractModel
{
    const FORM_NAME = 'mstBanner_placeholder_form';

    /**
     * @var Rule\Condition\CombineFactory
     */
    private $conditionCombineFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        Rule\Condition\CombineFactory $conditionCombineFactory,
        DataObjectFactory $dataObjectFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate
    ) {
        $this->conditionCombineFactory = $conditionCombineFactory;
        $this->dataObjectFactory       = $dataObjectFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    /**
     * @return \Magento\Rule\Model\Action\Collection|void
     */
    public function getActionsInstance()
    {
    }

    /**
     * @return \Magento\Rule\Model\Condition\Combine|Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->conditionCombineFactory->create();
    }

    /**
     * @param array $data
     *
     * @return DataObject
     */
    public function getDataObject(array $data)
    {
        return $this->dataObjectFactory->create(['data' => $data]);
    }
}
