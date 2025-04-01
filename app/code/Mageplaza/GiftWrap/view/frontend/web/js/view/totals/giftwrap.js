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
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_GiftWrap/totals/giftwrap'
        },

        getWrapTotal: function () {
            return totals.getSegment('mp_gift_wrap_amount');
        },

        getWrapValue: function () {
            return this.getFormattedPrice(this.getWrapTotal().value);
        },

        getPostcardTotal: function () {
            return totals.getSegment('mp_gift_wrap_postcard_amount');
        },

        getPostcardValue: function () {
            return this.getFormattedPrice(this.getPostcardTotal().value);
        }
    });
});
