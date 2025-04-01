<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Category
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $items = $this->getRequest()->getParam('items', []);

        if (empty($items) && !$this->getRequest()->getParam('isAjax')) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($items as $objId => $objData) {
            if ($objData['name'] === '') {
                return $resultJson->setData(['messages' => [__('name value must not be empty.')], 'error' => true]);
            }

            /** @var Category $object */
            $object = $this->categoryFactory->create()->load($objId);

            try {
                $object->addData($objData)->save();
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithRuleId($object, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithRuleId($object, __('Something went wrong while saving the category.'));
                $error = true;
            }
        }

        return $resultJson->setData(compact('messages', 'error'));
    }

    /**
     * Add object id to error message
     *
     * @param Category $object
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithRuleId(Category $object, $errorText)
    {
        return '[Category ID: ' . $object->getId() . '] ' . $errorText;
    }
}
