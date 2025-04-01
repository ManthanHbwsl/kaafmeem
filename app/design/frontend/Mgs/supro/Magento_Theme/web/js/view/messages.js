define([
'jquery',
'uiComponent',
'underscore',
'escaper',
'Magento_Customer/js/customer-data',
'jquery/jquery-storageapi'
], function ($, Component, _, escaper, customerData, ko) {
'use strict';

	return Component.extend({
		defaults: {
			cookieMessages: [],
			messages: [],
            allowedTags: ['div', 'span', 'b', 'strong', 'i', 'em', 'u', 'a'],
            selector: '.page.messages .message',
			listens: {
				isHidden: 'onHiddenChange'
			}
		},
		/** @inheritdoc */
		initialize: function () {
			this._super();

			this.cookieMessages = $.cookieStorage.get('mage-messages');
			this.messages = customerData.get('messages').extend({
				disposableCustomerData: 'messages'
			});

			if (!_.isEmpty(this.messages().messages)) {
				customerData.set('messages', {});
			}

            $.mage.cookies.set('mage-messages', '', {
                samesite: 'strict',
                domain: ''
            });
        },

        /**
         * Prepare the given message to be rendered as HTML
         *
         * @param {String} message
         * @return {String}
         */
        prepareMessageForHtml: function (message) {
        return escaper.escapeHtml(message, this.allowedTags);

    },


    initObservable: function () {
			this._super()
				.observe('isHidden');

			return this;
		},

		RemoveMessage: function () {
			var el = $(this.selector);
			el.toggleClass('bounceInRight bounceOutRight');
			setTimeout(function () {
				el.hide()
			}, 2000);
		},

		isVisible: function () {
			return this.isHidden(!_.isEmpty(this.messages().messages) || !_.isEmpty(this.cookieMessages));
		},

		onHiddenChange: function (isHidden) {
			var self = this;
			if (isHidden) {
				setTimeout(function () {
					self.RemoveMessage();
				}, 5000);
			}
			this.isHidden(false);
		}

	});
});
