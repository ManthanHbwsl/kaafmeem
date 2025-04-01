require(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'mage/translate'
    ],
    function ($, mainQuote, rateReg, $t) {
        var inp = '';
        $(document).ready(function () {
            /** Check if extension is enabled or not */
            if (window.is_city_enabled === '1') {
                $(document).on("change", "[name='region_id']", function (e) {
                    var region_id = $(this).val();
                    var main_id = $(this).parent().parent().parent().attr('id');
                    if (typeof (main_id) === 'undefined') {
                        $(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
                        main_id = 'billingAddress-checkout-form';
                    }
                    if (typeof (region_id) !== 'undefined' && region_id !== null && region_id !== '' && main_id !== "") {
                        var city_id = $('#' + main_id + ' [name="city"]').attr('id');
                        inp = document.getElementById(city_id);
                        getRegionCities(region_id, main_id);
                    }
                });
                setTimeout(function () { reloadCities() }, 10000);
                $(document).on("change", "[name='country_id']", function (e) {
                    var main_id = $(this).parent().parent().parent().attr('id');
                    if (typeof (main_id) === 'undefined') {
                        $(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
                        main_id = 'billingAddress-checkout-form';
                    }
                    if (window.is_state_available === '1') {
                        $('#region_id').removeAttr('disabled');
                        var region_id = $('#' + main_id).find("[name='region_id']").val();
                        if (typeof (region_id) !== 'undefined' && region_id != null && region_id !== '' && main_id !== "") {
                            var city_id = $('#' + main_id + ' [name="city"]').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCities(region_id, main_id);
                        } else {
                            resetForms(main_id);
                        }
                    } else {
                        resetForms(main_id);
                        if ($(this).val() !== "") {
                            getRegionCities('', main_id);
                        }
                    }
                });
            }
        });

        var reloadCities = function () {
            var country_id = $("[name='country_id']").val();
            if (typeof (country_id) !== 'undefined' && country_id !== null && country_id !== "") {
                var main_id = $("[name='country_id']").parent().parent().parent().attr('id');
                if (typeof (main_id) === 'undefined') {
                    $(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
                    main_id = 'billingAddress-checkout-form';
                }
                if ($('#' + main_id).closest('li').css('display') === 'none') {
                    main_id = 'checkout-step-payment';
                }
                var city_id = $('#' + main_id).find("[name='city']").attr('id');
                inp = document.getElementById(city_id);
                if (window.is_state_available === '1') {
                    if ($('#' + main_id).find("[name='region_id']").length > 0 && $('#' + main_id).find("[name='region_id']").val() !== "") {
                        getRegionCities($('#' + main_id).find("[name='region_id']").val(), main_id);
                    }
                } else {
                    getRegionCities('', main_id);
                }
            } else {
                setTimeout( function () {  reloadCities(); }, 2000);
            }
        };
        var resetForms = function (main_id) {
            var city_id = $('#' + main_id + ' [name="city"]').attr('id');
            $('#' + city_id + '-select').remove();
            $('#' + main_id + ' [name="city"]').show();
            $('#' + main_id + ' [name="city"]').val('');
            var postcode_id = $('#' + main_id + ' [name="postcode"]').attr('id');
            $('#' + postcode_id + '-select').remove();
            $('#' + main_id + ' .postcode_billing_notinlist').remove();
            $('#' + main_id + ' .postcode_br_billing_notinlist').remove();
            $('#' + main_id + ' [name="postcode"]').show();
            $('#' + main_id + ' [name="postcode"]').val('');
            $('#' + main_id + ' .billing_notinlist').remove();
            $('#zip-error').remove();
            $('#city-select-error').remove();
        };

        /** Get cities dropdown */
        var ajaxLoading = false;
        var getRegionCities = function (region_value, main_id) {
            var country = $('#' + main_id).find('[name="country_id"]').val();
            if (!ajaxLoading && typeof (country) !== 'undefined' && country !== '') {
                ajaxLoading = true;
                var city_id = $('#' + main_id + ' [name="city"]').attr('id');
                var url = window.data_city_url;
                var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $t('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $t('Loading') + '...</div>';
                if ($('#' + main_id + ' .city_loading_mask').length === 0) {
                    $('#' + main_id + ' [name="city"]').after(loader);
                }
                var city = $('#' + main_id + ' [name="city"]').val();
                emptyInput('', main_id);
                $('#error-' + city_id).hide();
                $('.mage-error').hide();
                $('#' + main_id + ' [name="city"]').hide();
                $('#' + city_id + '-select').remove();
                $('#' + main_id + ' .billing_notinlist').remove();
                $('#' + main_id + ' .br_billing_notinlist').remove();
                $('#' + main_id + ' .postcode_billing_notinlist').remove();
                $('#' + main_id + ' .postcode_br_billing_notinlist').remove();
                $('#' + main_id + ' [name="zip-select"]').remove();
                $('#' + main_id + ' [name="postcode"]').show();
                $.ajax({
                    url: url,
                    type: "get",
                    data: "state=" + region_value + '&country_id=' + country,
                    dataType: 'json',
                    timeout: 15000
                }).done(function (response) {
                    ajaxLoading = false;
                    $('#error-' + city_id).show();
                    $('.mage-error').show();
                    $('#' + main_id + ' .city_loading_mask').remove();
                    $('#' + main_id + ' [name="city"]').show();
                    var options = '<select onchange="window.getCityState(this.value,\'' + main_id + '\'),window.getZipcodes(this.value,\'' + main_id + '\')" id="' + city_id + '-select" class="select" title="' + $t('City') + '" name="city-select" ><option value="">' + $t(window.city_option_title) + '</option>';
                    var cities = response.cities;
                    var cities_indexes = response.cities_indexes;
                    if (cities.length > 0) {
                        for (var i = 0; i < cities.length; i++) {
                            var selected = '';
                            if (city.toLowerCase() === cities[i].toLowerCase()) {
                                selected = "selected='selected'";
                            }
                            options += '<option ' + selected + ' value="' + cities_indexes[i] + '">' + cities[i] + '</option>';
                        }
                        options += "</select>";
                        if (window.data_city_link !== 0) {
                            var title = $t(window.data_city_title);
                            options += "<br class='br_billing_notinlist' /><a onclick='window.cityNotInList(\"" + main_id + "\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>" + title + "</a>";
                        }
                        $('#' + main_id + ' [name="city"]').hide();
                        if ($('#' + city_id + '-select').length === 0) {
                            $('#' + main_id + ' [name="city"]').after(options);
                        }
                    } else {
                        $('#' + main_id + ' [name="city"]').html(inp);
                        $('#' + main_id + ' .billing_notinlist').remove();
                    }
                }).fail(function (error) {
                    ajaxLoading = false;
                    $('#error-' + city_id).show();
                    $('#' + main_id + ' .city_loading_mask').remove();
                    $('#' + main_id + ' [name="city"]').show();
                });
            }
        };
        /* Get Zip codes */
        var getPostcodes = function (value, main_id) {
            var postcode_id = $('#' + main_id + ' [name="postcode"]').attr('id');
            inp = document.getElementById(postcode_id);
            var url = window.data_zip_url;
            var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $t('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $t('Loading') + '...</div>';
            if ($('#' + main_id + ' .postcode_loading_mask').length === 0) {
                $('#' + main_id + ' [name="postcode"]').after(loader);
            }
            emptyInputZip('', main_id);
            $('#error-' + postcode_id).hide();
            $('.mage-error').hide();
            $('#' + main_id + ' [name="postcode"]').hide();
            $('#' + postcode_id + '-select').remove();
            $('#' + main_id + ' .postcode_billing_notinlist').remove();
            $('#' + main_id + ' .postcode_br_billing_notinlist').remove();
            $.ajax({
                url: url,
                type: "get",
                data: "city=" + value + '&state=' + $('#' + main_id + ' [name="region_id"]').val() + '&country_id=' + $('#' + main_id + ' [name="country_id"]').val(),
                dataType: 'json',
                timeout: 15000
            }).done(function (response) {
                $('#error-' + postcode_id).show();
                $('.mage-error').show();
                $('#' + main_id + ' .postcode_loading_mask').remove();
                $('#' + main_id + ' [name="postcode"]').show();
                var options = '<select onchange="window.getZipState(this.value,\'' + main_id + '\')" id="' + postcode_id + '-select" class="validate-select select" title="' + $t('Postcode') + '" name="zip-select" ><option value="">' + $t(window.zip_option_title) + '</option>';
                if (response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
                        options += '<option value="' + response[i] + '">' + response[i] + '</option>';
                    }
                    options += "</select>";
                    if (window.data_zip_link !== 0) {
                        var title = $t(window.data_zip_title);
                        options += "<br class='postcode_br_billing_notinlist' /><a onclick='window.notInListZip(\"" + main_id + "\")' class='postcode_billing_notinlist' href='javascript:void(0)' class='postcode_notinlist'>" + title + "</a>";
                    }
                    $('#' + main_id + ' [name="postcode"]').hide();
                    if ($('#' + postcode_id + '-select').length === 0) {
                        $('#' + main_id + ' [name="postcode"]').after(options);
                    }
                } else {
                    $('#' + main_id + ' [name="postcode"]').html(inp);
                    $('#' + main_id + ' .postcode_billing_notinlist').remove();
                }
            }).fail(function (error) {
                $('#error-' + postcode_id).show();
                $('#' + main_id + ' .postcode_loading_mask').remove();
                $('#' + main_id + ' [name="postcode"]').show();
            });
        };

        window.getZipcodes = function (value, type) {
            if (value !== '' && $('#' + type + ' [name="city-select"]').length > 0 && $('#' + type + ' [name="city-select"]').is('select')) {
                getPostcodes(value, type);
            }
        };

        /* Zip not in list */
        window.notInListZip = function (main_id) {
            var postcode_id = $('#' + main_id + ' [name="postcode"]').attr('id');
            $('#' + postcode_id + '-select').remove();
            $('#' + main_id + ' .postcode_billing_notinlist').remove();
            $('#' + main_id + ' .postcode_br_billing_notinlist').remove();
            $('#' + main_id + ' [name="postcode"]').show();
        };

        window.cityNotInList = function (main_id) {
            var city_id = $('#' + main_id + ' [name="city"]').attr('id');
            $('#' + city_id + '-select').remove();
            $('#' + main_id + ' .billing_notinlist').remove();
            $('#' + main_id + ' .br_billing_notinlist').remove();
            $('#' + main_id + ' [name="city"]').show();
        };

        var emptyInput = function (val, main_id) {
            $('#' + main_id).find('[name="city"]').focus();
            $('#' + main_id).find('[name="city"]').val(val).change();
            $('#' + main_id).find('[name="city-select"]').show();
            if (val !== "") {
                $('#city-select-error').remove();
                $('#' + main_id).find('[name="city"]').removeClass('mage-error');
                calculateShppingRates();
            }
        };

        var calculateShppingRates = function () {
            if (window.isShippingRateEnabled === '1') {
                var address = mainQuote.shippingAddress();
                rateReg.set(address.getKey(), null);
                rateReg.set(address.getCacheKey(), null);
                mainQuote.shippingAddress(address);
            }
        };

        var emptyInputZip = function (val, main_id) {
            $('#' + main_id + ' [name="postcode"]').focus();
            $('#' + main_id + ' [name="postcode"]').val(val).change();
            $('#' + main_id + ' [name="zip-select"]').show();
            if (val !== "") {
                $('#zip-select-error').remove();
                $('#' + main_id + ' [name="postcode"]').removeClass('mage-error');
                $('#zip-select').removeClass('mage-error');
            }
        };

        window.getCityState = function (val, main_id) {
            emptyInput(val, main_id);
        };

        window.getZipState = function (val, main_id) {
            emptyInputZip(val, main_id);
        };

    });
