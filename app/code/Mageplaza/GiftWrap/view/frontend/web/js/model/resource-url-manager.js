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
    'Magento_Checkout/js/model/resource-url-manager'
], function ($, resourceUrlManager) {
    'use strict';

    return $.extend(resourceUrlManager, {
        /**
         * Get update wrap url
         * @return {*|string}
         */
        getUrlForUpdateWrap: function (quote) {
            var params = this.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {},
                urls = {
                'guest': '/guest-carts/:cartId/mpgiftwrap-update-wrap',
                'customer': '/carts/mine/mpgiftwrap-update-wrap'
            };

            return this.getUrl(urls, params);
        }
    });
});
