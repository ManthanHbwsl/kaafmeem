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

define(['ko', 'Magento_Checkout/js/model/quote'], function (ko, quote) {
    'use strict';

    var config = ko.computed(function () {
        var extensionAttributes = quote.getTotals()().extension_attributes,
            result              = {
                wrap: {},
                postcard: {}
            };

        if (extensionAttributes) {
            if (extensionAttributes.hasOwnProperty('mp_gift_wrap')) {
                result.wrap = JSON.parse(extensionAttributes.mp_gift_wrap);
            }
            if (extensionAttributes.hasOwnProperty('mp_gift_wrap_postcard')) {
                result.postcard = JSON.parse(extensionAttributes.mp_gift_wrap_postcard);
            }
        }

        return result;
    });

    return {config: config};
});
