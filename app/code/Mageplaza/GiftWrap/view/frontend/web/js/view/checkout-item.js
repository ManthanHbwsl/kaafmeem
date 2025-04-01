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
    'ko',
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Mageplaza_GiftWrap/js/action/update-wrap',
    'Mageplaza_GiftWrap/js/model/giftwrap',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mageplaza/core/owl.carousel'
], function (ko, $, _, Component, updateWrapAction, giftwrap, modal, $t) {
    'use strict';

    var giftwrapData = {};

    return Component.extend({
        category: ko.observable(),
        slider: '.mpgiftwrap-wrap',
        postcardSlider: '.mpgiftwrap-postcard',

        initObservable: function () {
            var self = this;

            this._super().observe({
                wrapOptions: self.wraps,
                postcardOptions: self.postcards
            });

            this.category.subscribe(function (value) {
                $(self.slider).children().remove();
                $(self.postcardSlider).children().remove();

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

            return this;
        },

        getValue: function (item, wrapDataKey) {
            var product,
                config = wrapDataKey === 'mp_gift_wrap_data' ? giftwrap.config().wrap : giftwrap.config().postcard;

            if (_.isEmpty(item) || !item.item_id) {
                return false;
            }

            product = _.findWhere(config, {item_id: item.item_id + ''}) || {};

            if (!_.isEmpty(product)) {
                giftwrapData = JSON.parse(product[wrapDataKey]);

                if (_.isEmpty(giftwrapData)
                    || !_.isEmpty(giftwrapData)
                    && giftwrapData.hasOwnProperty('all_cart') && giftwrapData.all_cart && !this.isShowAllCart
                    || !_.isEmpty(giftwrapData)
                    && !giftwrapData.hasOwnProperty('all_cart') && !giftwrapData.all_cart && !this.isShowOnProduct
                ) {
                    return false;
                }

                return giftwrapData;
            }

            return false;
        },

        getWrapValue: function (item) {
            return this.getValue(item, 'mp_gift_wrap_data');
        },

        getPostcardValue: function (item) {
            return this.getValue(item, 'mp_gift_wrap_postcard_data');
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

        setModalElement: function (item) {
            var modalWindow = $('#mpgiftwrap-modal-' + item.item_id);

            var options = {
                'type': 'popup',
                'title': $t('Add Gift Wrap'),
                'responsive': true,
                'innerScroll': true,
                'buttons': []
            };

            modal(options, $(modalWindow));
        },

        selectWrap: function (item, data, event) {
            var modalWindow, wrap_id, wrap, postcard_id, postcard;

            if (event.type !== 'click') {
                return;
            }

            modalWindow = $('#mpgiftwrap-modal-' + item.item_id);
            wrap_id     = modalWindow.find('.mpgiftwrap-wrap-item input:checked').val();
            postcard_id = modalWindow.find('.mpgiftwrap-postcard-item input:checked').val();
            wrap        = _.findWhere(this.wrapOptions(), {'wrap_id': wrap_id});
            postcard    = _.findWhere(this.postcardOptions(), {'wrap_id': postcard_id});
            wrap        = _.isEmpty(wrap) ? false : wrap;
            postcard    = _.isEmpty(postcard) ? false : postcard;

            this.applyWrap(wrap, postcard, item);

            this.closeModal(item, data, event);
        },

        applyWrap: function (wrap, postcard, item) {
            if (wrap) {
                _.extend(wrap, {
                    'use_gift_message': $('#use-gift-message-' + item.item_id).is(':checked'),
                    'gift_message': $('#mpgiftwrap-message-input-' + item.item_id).val()
                });
            }

            if (postcard) {
                _.extend(postcard, {
                    'gift_message': $('#mpgiftwrap-postcard-message-input-' + item.item_id).val()
                });
            }

            updateWrapAction(item.item_id, wrap, postcard);
        },

        removeWrap: function (item, data, event) {
            if (event.type !== 'click') {
                return;
            }

            updateWrapAction(item.item_id, false, false);

            this.closeModal(item, data, event);
        },

        showModal: function (item, data, event) {
            if (event.type !== 'click') {
                return;
            }

            $('#mpgiftwrap-modal-' + item.item_id).modal('openModal');
        },

        closeModal: function (item, data, event) {
            if (event.type !== 'click') {
                return;
            }

            $('#mpgiftwrap-modal-' + item.item_id).modal('closeModal');
        },

        getItemId: function (item) {
            if (item) {
                return item.item_id;
            }

            return '';
        },

        useGiftMessage: function (item) {
            if ($('#use-gift-message-' + item.item_id).is(':checked')) {
                $('#mpgiftwrap-message-' + item.item_id).show();
            } else {
                $('#mpgiftwrap-message-' + item.item_id).hide();
            }
        },

        canRender: function (item) {
            return !$('.modal-popup #mpgiftwrap-modal-' + item.item_id).length;
        },

        isEnabled: function (item) {
            var product;

            if (_.isEmpty(item) || !item.item_id) {
                return false;
            }

            product = _.findWhere(giftwrap.config().wrap, {item_id: item.item_id + ''}) || {};

            return !_.isEmpty(product);
        },

        isVisibleRemoveButton: function (item) {
            return !!(this.getWrapValue(item) || this.getPostcardValue(item));
        }
    });
});
