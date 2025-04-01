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

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($, ko, Component, customerData, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_FreeShippingBar/shippingbar'
        },

        /**
         * init function
         */
        initialize: function (config) {
            this._super();

            this.below_goal = ko.observable();
            var cartData = customerData.get('cart');
            this.below_goal(config.goal - cartData().mpFSBCartTotal);

            cartData.subscribe(function (updatedCart) {
                this.below_goal(config.goal - updatedCart.mpFSBCartTotal);
            }, this);

            this.message = ko.computed(function () {
                if (this.below_goal() <= 0 || this.goal === 0 ) {
                    return config.achieve_message;
                } else if (!this.below_goal() || this.below_goal() === this.goal) {
                    return config.first_message.replace("{{goal}}", "<span class='goal'>" + this.getFormattedPrice(config.goal) + "</span>");
                } else if (this.below_goal() > 0 && this.below_goal() < config.goal) {
                    return config.below_goal_message.replace("{{below_goal}}", "<span class='below_goal'>" + this.getFormattedPrice(this.below_goal()) + "</span>");
                }
            }, this);
        },

        /**
         * FormatPrice
         */
        getFormattedPrice: function (price) {
            if (window.fsbConfig) {
                return priceUtils.formatPrice(price, window.fsbConfig.priceFormat).replace('.00', '');
            } else {
                $('.wrapper-mp-freeshippingbar').css('display','none');

                return true;
            }
        }
    })
});
