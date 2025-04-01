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
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Model;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface;

/**
 * Class Shippingbar
 * @package Mageplaza\FreeShippingBar\Model
 */
class Shippingbar extends AbstractModel implements ShippingBarInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_shippingbar_rules';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Shippingbar::class);
    }

    /**
     * @inheritDoc
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRuleId($value)
    {
        $this->setData(self::RULE_ID, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        $this->setData(self::NAME, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        $this->setData(self::STATUS, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @inheritDoc
     */
    public function setPriority($value)
    {
        $this->setData(self::PRIORITY, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        $this->setData(self::CREATED_AT, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setFromDate($value)
    {
        $this->setData(self::FROM_DATE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setToDate($value)
    {
        $this->setData(self::TO_DATE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($value)
    {
        $this->setData(self::UPDATED_AT, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($value)
    {
        $this->setData(self::STORE_ID, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerGroupId($value)
    {
        $this->setData(self::CUSTOMER_GROUP_ID, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGoal()
    {
        return $this->getData(self::GOAL);
    }

    /**
     * @inheritDoc
     */
    public function setGoal($value)
    {
        $this->setData(self::GOAL, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFirstMessage()
    {
        return $this->getData(self::FIRST_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setFirstMessage($value)
    {
        $this->setData(self::FIRST_MESSAGE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBelowGoalMessage()
    {
        return $this->getData(self::BELOW_GOAL_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setBelowGoalMessage($value)
    {
        $this->setData(self::BELOW_GOAL_MESSAGE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAchieveMessage()
    {
        return $this->getData(self::ACHIEVE_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setAchieveMessage($value)
    {
        $this->setData(self::ACHIEVE_MESSAGE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClickAble()
    {
        return $this->getData(self::CLICK_ABLE);
    }

    /**
     * @inheritDoc
     */
    public function setClickAble($value)
    {
        $this->setData(self::CLICK_ABLE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrlShippingbar()
    {
        return $this->getData(self::URL_SHIPPING_BAR);
    }

    /**
     * @inheritDoc
     */
    public function setUrlShippingbar($value)
    {
        $this->setData(self::URL_SHIPPING_BAR, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOpenNewPage()
    {
        return $this->getData(self::OPEN_NEW_PAGE);
    }

    /**
     * @inheritDoc
     */
    public function setOpenNewPage($value)
    {
        $this->setData(self::OPEN_NEW_PAGE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate()
    {
        return $this->getData(self::TEMPLATE);
    }

    /**
     * @inheritDoc
     */
    public function setTemplate($value)
    {
        $this->setData(self::TEMPLATE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBarBackground()
    {
        return $this->getData(self::BAR_BACKGROUND);
    }

    /**
     * @inheritDoc
     */
    public function setBarBackground($value)
    {
        $this->setData(self::BAR_BACKGROUND, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBarOpacity()
    {
        return $this->getData(self::BAR_OPACITY);
    }

    /**
     * @inheritDoc
     */
    public function setBarOpacity($value)
    {
        $this->setData(self::BAR_OPACITY, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBarTextColor()
    {
        return $this->getData(self::BAR_TEXT_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function setBarTextColor($value)
    {
        $this->setData(self::BAR_TEXT_COLOR, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBarLinkColor()
    {
        return $this->getData(self::BAR_LINK_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function setBarLinkColor($value)
    {
        $this->setData(self::BAR_LINK_COLOR, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getGoalTextColor()
    {
        return $this->getData(self::GOAL_TEXT_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function setGoalTextColor($value)
    {
        $this->setData(self::GOAL_TEXT_COLOR, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($value)
    {
        $this->setData(self::IMAGE, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStringFontConnect()
    {
        return $this->getData(self::STRING_FONT_CONNECT);
    }

    /**
     * @inheritDoc
     */
    public function setStringFontConnect($value)
    {
        $this->setData(self::STRING_FONT_CONNECT, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFontText()
    {
        return $this->getData(self::FONT_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setFontText($value)
    {
        $this->setData(self::FONT_TEXT, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFontSize()
    {
        return $this->getData(self::FONT_SIZE);
    }

    /**
     * @inheritDoc
     */
    public function setFontSize($value)
    {
        $this->setData(self::FONT_SIZE, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($value)
    {
        $this->setData(self::POSITION, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAllowshow()
    {
        return $this->getData(self::ALLOW_SHOW);
    }

    /**
     * @inheritDoc
     */
    public function setAllowshow($value)
    {
        $this->setData(self::ALLOW_SHOW, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIncludePages()
    {
        return $this->getData(self::INCLUDE_PAGES);
    }

    /**
     * @inheritDoc
     */
    public function setIncludePages($value)
    {
        $this->setData(self::INCLUDE_PAGES, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIncludePagesUrlContain()
    {
        return $this->getData(self::INCLUDE_PAGES_URL_CONTAIN);
    }

    /**
     * @inheritDoc
     */
    public function setIncludePagesUrlContain($value)
    {
        $this->setData(self::INCLUDE_PAGES_URL_CONTAIN, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludePages()
    {
        return $this->getData(self::EXCLUDE_PAGES);
    }

    /**
     * @inheritDoc
     */
    public function setExcludePages($value)
    {
        $this->setData(self::EXCLUDE_PAGES, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExcludePagesUrlContain()
    {
        return $this->getData(self::EXCLUDE_PAGES_URL_CONTAIN);
    }

    /**
     * @inheritDoc
     */
    public function setExcludePagesUrlContain($value)
    {
        $this->setData(self::EXCLUDE_PAGES_URL_CONTAIN, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getImageBackgroundUrl()
    {
        return $this->getData(self::IMAGE_BACKGROUND_URL);
    }

    /**
     * @inheritDoc
     */
    public function setImageBackgroundUrl($value)
    {
        $this->setData(self::IMAGE_BACKGROUND_URL, $value);

        return $this;
    }

    /**
     * @param string $currentPath
     * @param string $type
     *
     * @return bool
     */
    public function checkPaths($currentPath, $type = 'include')
    {
        $pagesUrlData = $type === 'include'
            ? $this->getIncludePagesUrlContain()
            : $this->getExcludePagesUrlContain();

        if ($pagesUrlData) {
            $arrayPaths = explode("\n", $pagesUrlData);
            $pathsUrl = array_filter(array_map('trim', $arrayPaths));
            foreach ($pathsUrl as $path) {
                if (strpos($currentPath, $path) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $fullActionName
     * @param string $currentPath
     *
     * @return bool
     */
    public function checkInclude($fullActionName, $currentPath)
    {
        return ($this->checkPages($fullActionName) || $this->checkPaths($currentPath));
    }

    /**
     * @param string $fullActionName
     * @param string $type
     *
     * @return bool
     */
    public function checkPages($fullActionName, $type = 'include')
    {
        $pages = array_filter(array_map(
            'trim',
            explode("\n", $type === 'include' ? $this->getIncludePages() : $this->getExcludePages())
        ));

        return in_array($fullActionName, $pages, true);
    }

    /**
     * @param string $fullActionName
     * @param string $currentPath
     *
     * @return bool
     */
    public function checkExclude($fullActionName, $currentPath)
    {
        return ($this->checkPages($fullActionName, 'exclude') || $this->checkPaths($currentPath, 'exclude'));
    }
}
