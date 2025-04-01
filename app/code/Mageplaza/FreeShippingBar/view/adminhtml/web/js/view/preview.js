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
    'uiComponent'
], function ($, ko, Component) {
    'use strict';
    var self = this;

    return Component.extend({
        defaults: {
            template: 'Mageplaza_FreeShippingBar/preview'
        },

        /**
         * HTML Attributes
         */
        data: ko.observableArray(),

        /**
         *
         * @returns {exports}
         */
        initialize: function () {
            self = this;
            this._super();

            return self;
        },

        /**
         * After render
         */
        renderHTML: function () {
            this.setValue();
            this.setDefault();
            this.initListener();
        },

        /**
         * Set HTML Attributes
         * @returns {*}
         */
        setValue: function () {
            var freeshippingbar = $('.freeshippingbar'),
                firstMsgContainer = $('#first-message'),
                belowMsgContainer = $('#bellow-goal-message'),
                successMsgContainer = $('#success-message'),
                firstMsg = $('#shippingbar_first_message'),
                bellowGoalMessage = $('#shippingbar_below_goal_message'),
                achieveMessage = $('#shippingbar_achieve_message'),
                res_firstMessage = firstMsg.val().replace("{{goal}}", "<span id='goal'>{{goal}}</span>"),
                res_bellowGoalMessage = bellowGoalMessage.val().replace("{{below_goal}}", "<span id='below_goal'>{{below_goal}}</span>"),
                freeshipping_a = $(".freeshippingbar a"),
                hasUrl = $('#shippingbar_url_shippingbar'),
                spBarLinkColor = $('#shippingbar_bar_link_color'),
                spBarBackground = $('#shippingbar_bar_background'),
                spBarTextColor = $('#shippingbar_bar_text_color'),
                spBarOpacity = $('#shippingbar_bar_opacity'),
                spBarGoalTextColor = $('#shippingbar_goal_text_color'),
                spBarFontText = $('#shippingbar_font_text'),
                spBarFontSize = $('#shippingbar_font_size'),
                spBarOpenNewPage = $("#shippingbar_open_new_page"),
                templateSelected = $('#shippingbar_template'),
                spBarImageBg = document.querySelector('#shippingbar_image').getAttribute('value'),
                clickAble = $('#shippingbar_click_able'),

                html = {
                    'freeshippingbar': freeshippingbar,
                    'firstMsgContainer': firstMsgContainer,
                    'belowMsgContainer': belowMsgContainer,
                    'successMsgContainer': successMsgContainer,
                    'firstMsg': firstMsg,
                    'bellowGoalMessage': bellowGoalMessage,
                    'achieveMessage': achieveMessage,
                    'res_firstMessage': res_firstMessage,
                    'res_bellowGoalMessage': res_bellowGoalMessage,
                    'clickAble': clickAble,
                    'freeshipping_a': freeshipping_a,
                    'hasUrl': hasUrl,
                    'spBarLinkColor': spBarLinkColor,
                    'spBarBackground': spBarBackground,
                    'spBarTextColor': spBarTextColor,
                    'spBarOpacity': spBarOpacity,
                    'spBarGoalTextColor': spBarGoalTextColor,
                    'spBarFontText': spBarFontText,
                    'spBarFontSize': spBarFontSize,
                    'spBarOpenNewPage': spBarOpenNewPage,
                    'templateSelected': templateSelected,
                    'spBarImageBg': spBarImageBg,
                    'mediaMageplazaPath': this.imageHelper,
                    'imageFSBMageplazaUrl': this.imageURL,
                    'templatesModel': this.templatesModel
                };

            return this.data(html);
        },

        /**
         * Get HTML Attributes
         * @returns {*}
         */
        getValue: function () {
            return this.data();
        },

        /**
         * Set default value
         */
        setDefault: function () {
            var html = this.getValue();
            html.firstMsgContainer.append(html.res_firstMessage);
            html.belowMsgContainer.append(html.res_bellowGoalMessage);
            html.successMsgContainer.append(html.achieveMessage.val());

            html.freeshippingbar.css({
                'background-color': html.spBarBackground.val(),
                'color': html.spBarTextColor.val(),
                'font-family': "'" + html.spBarFontText.val() + "'",
                'font-size': html.spBarFontSize.val() + 'px',
                'opacity': html.spBarOpacity.val()
            });

            $('#goal').css('color', html.spBarGoalTextColor.val());
            $('#below_goal').css('color', html.spBarGoalTextColor.val());
            var imageUrl;
            imageUrl = "url(" + html.mediaMageplazaPath + '/' + html.spBarImageBg + ")";

            if (!html.spBarImageBg) {
                var templateIdSelected = html.templateSelected.val();
                imageUrl = "url(" + html.imageFSBMageplazaUrl + html.templatesModel[templateIdSelected]['bg_url_image'] + ")";
            }
            if (html.templateSelected.val() === '0' && !html.spBarImageBg) {
                imageUrl = html.spBarBackground.val();
            }
            html.freeshippingbar.css('background-image', imageUrl);

            self.changeAble(html);
            html.freeshipping_a.attr("target", '_blank');
        },

        /**
         * Listen when change value
         */
        initListener: function () {
            var html = this.data(),
                reg = /<(\/)?\w+/,
                el_test = $('#shippingbar_el_message');

            el_test.html('HTML tags are not allowed.');
            html.firstMsg.on("change", function () {
                if (reg.test(html.firstMsg.val()) == true) {
                    self.showMessage(el_test, 5000);
                } else {
                    var msg = html.firstMsg.val().replace("{{goal}}", "<span id='goal'>{{goal}}</span>");
                    html.firstMsgContainer.html(msg);
                }
            });
            html.bellowGoalMessage.on("change", function () {
                if (reg.test(html.bellowGoalMessage.val()) == true) {
                    self.showMessage(el_test, 5000);
                } else {
                    var msg = html.bellowGoalMessage.val().replace("{{below_goal}}", "<span id='below_goal'>{{below_goal}}</span>");
                    html.belowMsgContainer.html(msg);
                }
            });
            html.achieveMessage.on("change", function () {
                if (reg.test(html.achieveMessage.val()) == true) {
                    self.showMessage(el_test, 5000);
                } else {
                    html.successMsgContainer.text(html.achieveMessage.val());
                }
            });
            html.clickAble.on("change", function () {
                if (html.spBarOpenNewPage.val() == 1) {
                    html.freeshipping_a.attr("target", '_blank');
                } else {
                    html.freeshipping_a.attr("target", '_self');
                }
                self.changeAble(html);
            });
            html.hasUrl.on("change", function () {
                if (html.hasUrl.val()) {
                    html.freeshipping_a.removeAttr("href");
                    html.freeshipping_a.attr("href", html.hasUrl.val());
                }
            });
            html.spBarOpenNewPage.on("change", function () {
                var open_new_page = html.spBarOpenNewPage.val();
                if (open_new_page == 1) {
                    html.freeshipping_a.attr("target", '_blank');
                } else {
                    html.freeshipping_a.attr("target", '_self');
                }
            });
            // add attribute and var when fields triger changed
            html.templateSelected.on("change", function () {
                var templateIdSelected = html.templateSelected.val();
                html.spBarOpacity.val(html.templatesModel[templateIdSelected]['bar_opacity']).trigger('change');
                html.spBarBackground.css('background-color', html.templatesModel[templateIdSelected]['bar_background']);
                html.spBarBackground.val(html.templatesModel[templateIdSelected]['bar_background']).trigger('change');
                html.spBarTextColor.css('background-color', html.templatesModel[templateIdSelected]['bar_text_color']);
                html.spBarTextColor.val(html.templatesModel[templateIdSelected]['bar_text_color']).trigger('change');
                html.spBarLinkColor.css('background-color', html.templatesModel[templateIdSelected]['bar_link_color']);
                html.spBarLinkColor.css('color', 'black');
                html.spBarLinkColor.val(html.templatesModel[templateIdSelected]['bar_link_color']).trigger('change');
                html.spBarGoalTextColor.css('background-color', html.templatesModel[templateIdSelected]['goal_text_color']);
                html.spBarGoalTextColor.val(html.templatesModel[templateIdSelected]['goal_text_color']).trigger('change');
                html.spBarFontText.val(html.templatesModel[templateIdSelected]['font_text']).trigger('change');
                html.spBarFontSize.val(html.templatesModel[templateIdSelected]['font_size']).trigger('change');
                var templateImg = html.templatesModel[templateIdSelected]['bg_url_image'];
                var imageUrl = 'none';
                if (templateImg) {
                    imageUrl = "url(" + html.imageFSBMageplazaUrl + templateImg + ")";
                }
                html.freeshippingbar.css('background-image', imageUrl);
            });

            // control spBarOpacity
            html.spBarOpacity.on("change", function () {
                var opacity = html.spBarOpacity.val();
                html.freeshippingbar.css('opacity', opacity);
            });

            // control spBarBackground
            html.spBarBackground.on("change", function () {
                var bgColor = html.spBarBackground.val();
                html.freeshippingbar.css('background-color', bgColor);
            });

            // control spBarTextColor
            html.spBarTextColor.on("change", function () {
                var bgColor = html.spBarTextColor.val();
                if (html.clickAble.val() !== '1') {
                    html.freeshipping_a.css('color', bgColor);
                }
            });

            // control spBarLinkColor
            html.spBarLinkColor.on("change", function () {
                var barLinkColor = html.spBarLinkColor.val();
                html.freeshipping_a.css('color', barLinkColor);
            });

            // control spBarGoalTextColor
            html.spBarGoalTextColor.on("change", function () {
                var goalTextColor = html.spBarGoalTextColor.val();
                $('#goal').css('color', goalTextColor);
                $('#below_goal').css('color', goalTextColor);
            });

            // control spBarFontText
            html.spBarFontText.on("change", function () {
                var font_text = "'" + html.spBarFontText.val() + "'";
                html.freeshippingbar.css('font-family', font_text);
            });

            // control spBarFontSize
            html.spBarFontSize.on("change", function () {
                var font_size = html.spBarFontSize.val() + 'px';
                html.freeshipping_a.css('font-size', font_size);
            });
        },

        /**
         * ClickAble
         * @param html
         * @returns {exports}
         */
        changeAble: function (html) {
            html.freeshipping_a
                .css({
                    'color': html.spBarTextColor.val(),
                    "cursor": "unset"
                })
                .attr("href", 'javascript:void(0)');
            if (html.clickAble.val() === '1' && html.hasUrl.val()) {
                html.freeshipping_a
                    .css({
                        'color': html.spBarLinkColor.val(),
                        'font-size': html.spBarFontSize.val() + 'px',
                        "cursor": "pointer"
                    })
                    .attr("href", html.hasUrl.val());
            }

            return this;
        },

        /**
         * show message
         * @param el
         * @param timedelay
         */
        showMessage: function (el, timedelay) {
            el.show();
            if (timedelay <= 0) timedelay = 5000;
            setTimeout(function () {
                el.hide();
            }, timedelay);
        }
    });
});
