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

namespace Mageplaza\FreeShippingBar\Api\Data;

/**
 * Interface ShippingBarSearchResultsInterface
 * @package Mageplaza\FreeShippingBar\Api\Data
 */
interface ShippingBarInterface
{
    const RULE_ID                   = 'rule_id';
    const NAME                      = 'name';
    const STATUS                    = 'status';
    const PRIORITY                  = 'priority';
    const CREATED_AT                = 'created_at';
    const FROM_DATE                 = 'from_date';
    const TO_DATE                   = 'to_date';
    const UPDATED_AT                = 'updated_at';
    const STORE_ID                  = 'store_id';
    const CUSTOMER_GROUP_ID         = 'customer_group_id';
    const GOAL                      = 'goal';
    const FIRST_MESSAGE             = 'first_message';
    const BELOW_GOAL_MESSAGE        = 'below_goal_message';
    const ACHIEVE_MESSAGE           = 'achieve_message';
    const CLICK_ABLE                = 'click_able';
    const URL_SHIPPING_BAR          = 'url_shippingbar';
    const OPEN_NEW_PAGE             = 'open_new_page';
    const TEMPLATE                  = 'template';
    const BAR_BACKGROUND            = 'bar_background';
    const BAR_OPACITY               = 'bar_opacity';
    const BAR_TEXT_COLOR            = 'bar_text_color';
    const BAR_LINK_COLOR            = 'bar_link_color';
    const GOAL_TEXT_COLOR           = 'goal_text_color';
    const IMAGE                     = 'image';
    const STRING_FONT_CONNECT       = 'string_font_connect';
    const FONT_TEXT                 = 'font_text';
    const FONT_SIZE                 = 'font_size';
    const POSITION                  = 'position';
    const ALLOW_SHOW                = 'allowshow';
    const INCLUDE_PAGES             = 'include_pages';
    const INCLUDE_PAGES_URL_CONTAIN = 'include_pages_url_contain';
    const EXCLUDE_PAGES             = 'exclude_pages';
    const EXCLUDE_PAGES_URL_CONTAIN = 'exclude_pages_url_contain';
    const IMAGE_BACKGROUND_URL      = 'image_background_url';

    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setRuleId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPriority($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getFromDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFromDate($value);

    /**
     * @return string
     */
    public function getToDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToDate($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getCustomerGroupId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerGroupId($value);

    /**
     * @return int
     */
    public function getGoal();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setGoal($value);

    /**
     * @return string
     */
    public function getFirstMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFirstMessage($value);

    /**
     * @return string
     */
    public function getBelowGoalMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBelowGoalMessage($value);

    /**
     * @return string
     */
    public function getAchieveMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAchieveMessage($value);

    /**
     * @return int
     */
    public function getClickAble();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setClickAble($value);

    /**
     * @return string
     */
    public function getUrlShippingbar();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlShippingbar($value);

    /**
     * @return int
     */
    public function getOpenNewPage();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setOpenNewPage($value);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTemplate($value);

    /**
     * @return string
     */
    public function getBarBackground();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBarBackground($value);

    /**
     * @return float
     */
    public function getBarOpacity();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setBarOpacity($value);

    /**
     * @return string
     */
    public function getBarTextColor();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBarTextColor($value);

    /**
     * @return string
     */
    public function getBarLinkColor();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBarLinkColor($value);

    /**
     * @return string
     */
    public function getGoalTextColor();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setGoalTextColor($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImage($value);

    /**
     * @return string
     */
    public function getStringFontConnect();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStringFontConnect($value);

    /**
     * @return string
     */
    public function getFontText();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFontText($value);

    /**
     * @return int
     */
    public function getFontSize();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setFontSize($value);

    /**
     * @return string
     */
    public function getPosition();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return string
     */
    public function getAllowshow();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAllowshow($value);

    /**
     * @return string
     */
    public function getIncludePages();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIncludePages($value);

    /**
     * @return string
     */
    public function getIncludePagesUrlContain();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIncludePagesUrlContain($value);

    /**
     * @return string
     */
    public function getExcludePages();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExcludePages($value);

    /**
     * @return string
     */
    public function getExcludePagesUrlContain();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExcludePagesUrlContain($value);

    /**
     * @return string
     */
    public function getImageBackgroundUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImageBackgroundUrl($value);
}
