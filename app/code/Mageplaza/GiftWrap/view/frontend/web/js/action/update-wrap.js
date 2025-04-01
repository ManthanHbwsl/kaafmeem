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
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/action/get-payment-information',
    'Mageplaza_GiftWrap/js/model/resource-url-manager',
    'Magento_Ui/js/modal/alert'
], function (
    $,
    _,
    storage,
    quote,
    totals,
    errorProcessor,
    getPaymentInformationAction,
    resourceUrlManager,
    alert
) {
    'use strict';

    return function (itemId, wrap, postcard) {
        totals.isLoading(true);
        return storage.post(
            resourceUrlManager.getUrlForUpdateWrap(quote),
            JSON.stringify({'itemId': itemId, 'wrap': wrap, 'postcard': postcard})
        ).done(function (response) {
            var deferred = $.Deferred();

            quote.setTotals(response);

            getPaymentInformationAction(deferred);
            $.when(deferred).done(function () {
                totals.isLoading(false);
            });
        }).fail(function (response) {
            if ($('body').hasClass('checkout-cart-index')) {
                alert({
                    content: $.mage.__(response.responseJSON.message)
                });
            } else {
                errorProcessor.process(response);
            }

            totals.isLoading(false);
        });
    };
});
