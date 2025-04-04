/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
     'mage/apply/main', 
    'MGS_Ajaxlayernavigation/js/ion.rangeSlider.min',
	"mlazyload",
	'mage/translate'
], function ($,mage,ionRangeSlider) {
    'use strict';
    $.widget('mage.ajaxnavigation', {
        options: {
             
        }, 
        _create: function () {
            this.url = $(location).attr('href'); 
            this.useAjax = this.options.useAjax;
            this.usePrice_slide = this.options.use_range_price;
            this.minPrice = $("#price-range-slider").data("from");
            this.maxPrice = $("#price-range-slider").data("to");
            this.fromPrice = $("#price-range-slider").data("from");
            this.toPrice = $("#price-range-slider").data("to");
            this.pricePrefix = this.options.pricePrefix;
            this.pricePostfix = this.options.pricePostfix;
            this.initNavigation();
        },
        initNavigation: function() { 
            if(this.usePrice_slide){
               this.addPriceSlider(); 
            } 
            var self = this;
                self.addToolbarObservers();
            this.addFilterObservers();
        },
        addPriceSlider: function() {
            var self = this,
                priceActive = location.search.split('price=')[1];
            if (priceActive) {
                $("#price-range-slider").data('from', this.fromPrice);
                $("#price-range-slider").data('to', this.toPrice);
            }
            $("#price-range-slider").ionRangeSlider({
                type: "double",
                min: self.minPrice,
                max: self.maxPrice,
                from: self.fromPrice,
                to: self.toPrice,
                prettify_enabled: true,
                prefix: self.pricePrefix,
                postfix: self.pricePostfix,
                grid: true,
                onFinish: function(obj) {
                    self.applyToolbarElement('price', obj.from + '-' + obj.to);
                    self.fromPrice = obj.from;
                    self.toPrice = obj.to;
                }
            });
        },
        addFilterObservers: function() {
            var selectedIds, checkbox, filterItem,
                self = this;

            $(".mgs-layered-checkbox").on('change',function(){
                self.applyFilter($(this).parent().next());
                return false;
            });

            $(".mgs-ajax-layer-item" ).off();
            $(".mgs-ajax-layer-item").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var checkboxWrapper = $(this).prev(),
                    checkbox = checkboxWrapper.find('input');

                self.toggleCheckbox(checkbox);
                self.applyFilter($(this));
                return false;
            });

            $(".filter-active-item-link").off();
            $(".state-item-remove").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.applyFilter($(this).next());
                return false;
            });


            $( ".swatch-attribute-options a" ).off();
            $(".swatch-attribute-options a").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.applyFilter($(this));
                return false;
            });

            $(".filter-active-item-clear-all").off();
            $(".filter-active-item-clear-all").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.applyFilter($(this));
                return false;
            });
            // show hide filter
            $( ".filter-content dt" ).off();
            $(".filter-content dt").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggleFilter($(this));
                return false;
            });
        },
        toggleCheckbox: function(el) {
            if (el.prop('checked')) {
                el.prop("checked", false);
            } else {
                el.addClass('loading');
                el.prop("checked", true);
            }
        },

        addToolbarObservers: function() {
            var self = this;
            $("#mode-list").off();
            $("#mode-list").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation(); 
                self.applyToolbarElement('product_list_mode', 'list');
                return false;
            });

            $("#mode-grid").off();
            $("#mode-grid").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation(); 
                self.applyToolbarElement('product_list_mode', 'grid');
                return false;
            });

            $("#sorter").off();
            $("#sorter").on("change", function(e) {
                e.preventDefault();
                e.stopPropagation(); 
                self.applyToolbarElement('product_list_order', $(this).val());
                return false;
            });

            $(".sorter-action").off();
            $(".sorter-action").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation(); 
                self.applyToolbarElement('product_list_dir', $(this).attr("data-value"));
                return false;
            });


            $(".limiter-options").off();
            $(".limiter-options").on("change", function(e) {
                e.preventDefault();
                e.stopPropagation();

                self.applyToolbarElement('product_list_limit', $(this).val());
                return false;
            });

            $(".pages-items a").off();
            $(".pages-items a").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.applyFilter($(this));
                $('html, body').animate({
                    scrollTop: $("#maincontent").offset().top
                }, 500);
                return false;
            });
        },

        applyToolbarElement: function(param, value) {
            var self = this,
                urlParams = self.urlParams(self.url),
                url = self.url.split("?")[0];

            urlParams[param] = value;
            self.ajax_init(url + '?' + $.param(urlParams));
        },

        applyFilter: function(el) {
            this.ajax_init($(el).attr('href'));
        },

        ajax_init: function(url) {
            var self = this;
            if (!this.useAjax) {
                window.location = decodeURIComponent(url);
                return false;
            }
            window.history.pushState("", "", decodeURIComponent(url));
            $.ajax({
                method: "GET",
                url: decodeURIComponent(url),
                dataType: "json",
                data: { is_ajax: 1 },
                showLoader: true
            }) .done(function(data) {
                if (data.list) {
					$(".main .toolbar-products").remove();
					$(".main .products").remove();
					$(".main .filter-active").remove();
					$(".product-container.category-product-container").replaceWith(data.list);
					var $_filterAction = '<div class="filter-placeholder"><a class="action-filter" href="javascript:void(0)">'+$.mage.__('Filter')+'</a></div>';
					$('.top-toolbar .toolbar-products .toolbar-sorter').before($_filterAction);
                }
                if (data.filters) {
                    $(".block.filter").remove();
                    $(".column.main").prepend(data.filters);
                }
                self.url = url;
                self.initNavigation();
                $(mage.apply);
				
				$('.block.filter').removeClass('active');
				
				$("img.lazy").unveil(25, function(){
					var self = $(this);
					setTimeout(function(){
						self.removeClass('lazy');
						self.parents('.parent_lazy').addClass('lazy_loaded');
					}, 0);
				});
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            });
        },

        urlParams: function(url) {
            var result = {};
            var searchIndex = url.indexOf("?");
            if (searchIndex == -1 ) return result;
            var sPageURL = url.substring(searchIndex +1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                result[sParameterName[0]] = sParameterName[1];
            }

            return result;
        },

        toggleFilter: function(el) {
            el.toggleClass('inactive');
            el.toggleClass('active');
            el.next().slideToggle(); 
        }
    });

    return $.mage.ajaxnavigation;
});
