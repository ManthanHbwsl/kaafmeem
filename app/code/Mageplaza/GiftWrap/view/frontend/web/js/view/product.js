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
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mageplaza/core/owl.carousel'
], function ($, _, ko, Component, modal, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_GiftWrap/product'
        },
        modalWindow: null,
        category: ko.observable(),
        slider: '.mpgiftwrap-wrap-slider-item-0',
        postcardSlider: '.mpgiftwrap-postcard-slider-item-0',

        initObservable: function () {
            var self = this;

            this._super().observe({
                wrapOptions: self.wraps,
                postcardOptions: self.postcards,
                selectedWrap: _.isEmpty(self.savedWrap) ? false : self.savedWrap,
                selectedPostcard: _.isEmpty(self.savedPostcard) ? false : self.savedPostcard,
                wrap: _.isEmpty(self.savedWrap) ? false : self.savedWrap.wrap_id + '',
                postcard: _.isEmpty(self.savedPostcard) ? false : self.savedPostcard.wrap_id + '',
                useGiftMessage: _.isEmpty(self.savedWrap) ? false : self.savedWrap.use_gift_message,
                message: _.isEmpty(self.savedWrap) ? '' : self.savedWrap.gift_message,
                giftMessage: _.isEmpty(self.savedWrap) ? '' : self.savedWrap.gift_message,
                postcardMessage: _.isEmpty(self.savedPostcard) ? '' : self.savedPostcard.gift_message,
                giftPostcardMessage: _.isEmpty(self.savedPostcard) ? '' : self.savedPostcard.gift_message
            });

            this.category.subscribe(function (value) {
                $(self.slider).children().remove();

                self.wrapOptions([]);
                self.postcardOptions([]);

                if (!value) {
                    self.wrapOptions(self.wraps);
                    self.postcardOptions(self.postcards);
                } else {
                    _.each(self.wraps, function (wrap) {
                        if (wrap.category && _.contains(wrap.category.split(','), value)) {
                            self.wrapOptions.push(wrap);
                        }
                    });
                    _.each(self.postcards, function (postcard) {
                        if (postcard.category && _.contains(postcard.category.split(','), value)) {
                            self.postcardOptions.push(postcard);
                        }
                    });
                }

                self.initSlider(self.slider);
                self.initSlider(self.postcardSlider);
            });

            if (this.selectedWrap()) {
                $('[name="mp_gift_wrap_data"]').val(JSON.stringify(this.selectedWrap()));
            }
            if (this.selectedPostcard()) {
                $('[name="mp_gift_wrap_data"]').val(JSON.stringify(this.selectedPostcard()));
            }

            this.selectedWrap.subscribe(function (value) {
                if (value) {
                    _.extend(value, {'use_gift_message': self.useGiftMessage(), 'gift_message': self.message()});
                    self.giftMessage(value.gift_message);
                }
                $('[name="mp_gift_wrap_data"]').val(JSON.stringify(value));
            });

            this.selectedPostcard.subscribe(function (value) {
                if (value) {
                    _.extend(value, {'gift_message': self.postcardMessage()});
                    self.giftPostcardMessage(value.gift_message);
                }
                $('[name="mp_gift_wrap_postcard_data"]').val(JSON.stringify(value));
            });

            return this;
        },

        initSlider: function (element) {
            var container = $(element);

            container.trigger('destroy.owl.carousel');

            container.owlCarousel({
                center: false,
                loop: false,
                margin: 5,
                dots: false,
                nav: true,
                responsive: {
                    360: {
                        items: 2
                    },
                    568: {
                        items: 4
                    },
                    768: {
                        items: 8
                    }
                }
            });
        },

        setModalElement: function (element) {
            var options = {
                'type': 'popup',
                'title': $t('Add Gift Wrap'),
                'responsive': true,
                'innerScroll': true,
                'buttons': []
            };

            this.modalWindow = element;

            modal(options, $(this.modalWindow));
        },

        selectWrap: function () {
            var wrap     = _.findWhere(this.wrapOptions(), {'wrap_id': this.wrap()}),
                postcard = _.findWhere(this.postcardOptions(), {'wrap_id': this.postcard()});

            wrap     = _.isEmpty(wrap) ? false : wrap;
            postcard = _.isEmpty(postcard) ? false : postcard;

            this.applyWrap(wrap, postcard);

            this.closeModal();
        },

        removeWrap: function () {
            this.applyWrap(false, false);
            this.wrap(false);
            this.postcard(false);
            this.message('');
            this.postcardMessage('');

            this.closeModal();
        },

        showModal: function () {
            $(this.modalWindow).modal('openModal');
        },

        closeModal: function () {
            $(this.modalWindow).modal('closeModal');
        },

        applyWrap: function (wrap, postcard) {
            this.selectedWrap(wrap);
            this.selectedPostcard(postcard);
        },

        getItem: function (item) {
            return item;
        },

        getItemId: function () {
            return '';
        },

        isVisibleRemoveButton: function () {
            return this.selectedWrap() || this.selectedPostcard();
        }
    });
});
