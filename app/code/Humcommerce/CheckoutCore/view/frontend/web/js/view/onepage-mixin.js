define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'uiRegistry',
    'consoleLogger',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Amasty_CheckoutCore/js/action/is-equal-ignore-functions',
    'Amasty_CheckoutCore/js/model/one-step-layout',
    'Amasty_CheckoutCore/js/model/payment-validators/shipping-validator',
    'Amasty_CheckoutCore/js/model/address-form-state',
    'Amasty_CheckoutCore/js/model/statistic',
    'Amasty_CheckoutCore/js/model/shipping-registry',
    'Amasty_CheckoutCore/js/action/recollect-shipping-rates',
    'Amasty_CheckoutCore/js/model/payment/salesrule-observer',
], function(        
    $,
    _,
    Component,
    ko,
    registry,
    consoleLogger,
    customer,
    selectBillingAddress,
    quote,
    paymentValidatorRegistry,
    paymentMethodConverter,
    paymentService,
    checkoutDataResolver,
    isEqualIgnoreFunctions,
    oneStepLayout,
    shippingValidator,
    addressFormState,
    statistic,
    shippingRegistry,
    recollectRates,
    salesRuleObserver) {

    var mixin = {

        initChangePlaceButtonText: function () {
            var placeOrderButtonSelector = '.payment-method .action.primary.checkout';

            if (!this.customPlaceButtonText) {
                return;
            }

            $.async(placeOrderButtonSelector, function (element) {
                if (element.id == 'ckoApplePayButton'){
                    $(element).text('');
                }
                else{
                    $(element).attr('title', this.customPlaceButtonText);
                    $(element).attr('aria-label', this.customPlaceButtonText);
                    $(element).text(this.customPlaceButtonText);   
                }

            }.bind(this));
        }
        
    };
    
    return function(target){
        return target.extend(mixin);
    }
    
});