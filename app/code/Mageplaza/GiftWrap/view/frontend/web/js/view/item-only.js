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

define([
    'jquery',
    'underscore',
    'ko',
    'Mageplaza_GiftWrap/js/view/product',
    'Mageplaza_GiftWrap/js/action/update-wrap'
], function ($, _, ko, Component, updateWrapAction) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            this.slider         = '.mpgiftwrap-wrap-slider-item-' + this.itemId;
            this.postcardSlider = '.mpgiftwrap-postcard-slider-item-' + this.itemId;

            return this;
        },

        applyWrap: function (wrap, postcard) {
            this._super(wrap, postcard);

            updateWrapAction(this.itemId, wrap, postcard);
        }
    });
});

