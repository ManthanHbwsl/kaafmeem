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
        defaults: {
            template: 'Mageplaza_GiftWrap/all-cart'
        },
        slider: '.mpgiftwrap-wrap-all-cart',
        postcardSlider: '.mpgiftwrap-postcard-all-cart',

        applyWrap: function (wrap, postcard) {
            if (wrap) {
                _.extend(wrap, {'all_cart': true});
            }

            if (postcard) {
                _.extend(postcard, {'all_cart': true});
            }

            this._super(wrap, postcard);

            updateWrapAction('all_cart', wrap, postcard);
        }
    });
});

