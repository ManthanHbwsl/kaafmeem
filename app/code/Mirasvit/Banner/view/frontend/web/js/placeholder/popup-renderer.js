define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('mst.popupRenderer', {
        options: {
            'placeholder_id': null,
            'cookie_lifetime': null,
            'delay': null
        },

        _create: function () {
            const $el = this.element;

            if (!this.canShow()) {
                return;
            }

            const cookieData = {path: '/'}

            let cookieLifetime = this.options.cookie_lifetime !== null ? this.options.cookie_lifetime : 24;

            if (cookieLifetime != -1) {
                cookieData.expires = new Date(Date.now() + (cookieLifetime * 60 * 60 * 1000));
            } else {
                cookieData.lifetime = cookieData
            }

            const delay = this.options.delay === null ? 3 : this.options.delay;

            setTimeout(function () {
                $el.addClass('_active');

                if (cookieLifetime != 0) {
                    $.mage.cookies.set(
                        this.getCookieName(),
                        "+",
                        cookieData
                    );
                }

            }.bind(this), delay * 1000);

            $('[data-element=close]', $el).on('click', function () {
                $el.remove();
            }.bind(this))
        },

        canShow: function () {
            return $.mage.cookies.get(this.getCookieName()) !== "+";
        },

        getCookieName: function () {
            return 'mstBanner_placeholder_' + this.options.placeholder_id;
        }
    });

    return $.mst.popupRenderer;
});
