var inp = '';
require(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, $t) {
        window.reloadRegionsOnEditPage = function () {
            setTimeout(function () {
                if ($('#region_id').length > 0 && $('#region_id').val() !== "") {
                    $('#region_id').change();
                } else {
                    window.reloadRegionsOnEditPage();
                }
            }, 500);
        };

        var reloadCities = function () {
            var country_id = $("#country_id").val();
            if (typeof (country_id) !== 'undefined' && country_id !== null && country_id !== "") {
                var city_id = $('#city').attr('id');
                inp = document.getElementById(city_id);
                if (window.is_state_available === '1') {
                    if ($("#region_id").length > 0 && $("#region_id").val() !== "") {
                        getRegionCitiesAddress($("#region_id").val());
                    }
                } else {
                    getRegionCitiesAddress('');
                }
            } else {
                setTimeout(reloadCities, 2000);
            }
        };

        var resetForms = function () {
            $('#city-select').remove();
            $('#city').show();
            $('#city').val('');
            $('#zip-select').remove();
            $('.postcode_billing_notinlist').remove();
            $('.postcode_br_billing_notinlist').remove();
            $('#zip').show();
            $('#zip').val('');
            $('.billing_notinlist').remove();
            $('#city-select-error').remove();
        };

        $(document).ready(function () {
            /** Check if extension is enabled or not */
            if (window.is_city_enabled === '1') {
                /** Check if state is enabled or exists on website then load regions */
                /** If load cities is based on region **/
                window.reloadRegionsOnEditPage();
                $(document).on("change", "#region_id", function (e) {
                    var region_id = $(this).val();
                    if (typeof (region_id) != 'undefined' && region_id != null && region_id !== '') {
                        var city_id = $('#city').attr('id');
                        inp = document.getElementById(city_id);
                        getRegionCitiesAddress(region_id);
                    }
                });
                setTimeout(reloadCities(), 10000);
                $(document).on("change", "#country_id", function (e) {
                    if (window.is_state_available === '1') {
                        $('#region_id').removeAttr('disabled');
                        var region_id = $('#region_id').val();
                        if (typeof (region_id) != 'undefined' && region_id != null && region_id !== '') {
                            var city_id = $('#city').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCitiesAddress(region_id);
                        } else {
                            resetForms();
                        }
                    } else {
                        resetForms();
                        if ($(this).val() !== "") {
                            getRegionCitiesAddress('');
                        }
                    }
                });
            }
        });

        /** Get cities dropdown */
        var ajaxLoading = false;
        /** Get cities on address page */
        var getRegionCitiesAddress = function (value) {
            if (!ajaxLoading) {
                ajaxLoading = true;
                var city_id = "city";
                var url = window.data_city_url;
                var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $t('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $t('Loading') + '...</div>';
                if ($('.city_loading_mask').length === 0) {
                    $('#city').after(loader);
                }
                var city = $('#city').val();
                emptyInput('');
                $('#error-' + city_id).hide();
                $('#city-select-error').remove();
                $('.mage-error').hide();
                $('#city').hide();
                $('#' + city_id + '-select').remove();
                $('.billing_notinlist').remove();
                $('.br_billing_notinlist').remove();
                $('.postcode_billing_notinlist').remove();
                $('.postcode_br_billing_notinlist').remove();
                $('#zip-select,#zip-select-error,#zip-error').remove();
                $('#zip').removeClass('mage-error');
                $('#zip').show();
                $.ajax({
                    url: url,
                    type: "get",
                    data: "state=" + value + '&country_id=' + $('#country').val(),
                    dataType: 'json',
                    timeout: 15000
                }).done(function (response) {
                    ajaxLoading = false;
                    $('#error-' + city_id).show();
                    $('.mage-error').show();
                    $('.city_loading_mask').remove();
                    $('#city').show();
                    var cities = response.cities;
                    var cities_indexes = response.cities_indexes;
                    var options = '<select onchange="window.getCityState(this.value),window.getZipcodes(this.value)" id="' + city_id + '-select" class="validate-select select" title="' + $t('City') + '" name="city-select" ><option value="">' + $t(window.city_option_title) + '</option>';
                    var loadZipCodes = false;
                    if (cities.length > 0) {
                        for (var i = 0; i < cities.length; i++) {
                            var selected = '';
                            if (city.toLowerCase() === cities[i].toLowerCase()) {
                                selected = "selected='selected'";
                                loadZipCodes = true;
                                $('#city').val(city);
                            }
                            options += '<option ' + selected + ' value="' + cities_indexes[i] + '">' + cities[i] + '</option>';
                        }
                        options += "</select>";
                        if (window.data_city_link !== 0) {
                            var title = window.data_city_title;
                            options += "<br class='br_billing_notinlist' /><a onclick='window.cityNotInList()' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>" + title + "</a>";
                        }
                        $('#city').hide();
                        if ($('#' + city_id + '-select').length === 0) {
                            $('#city').after(options);
                        }
                        if (loadZipCodes) {
                            window.getZipcodes(city);
                        }
                    } else {
                        $('#city').html(inp);
                        $('.billing_notinlist').remove();
                    }
                }).fail(function (error) {
                    ajaxLoading = false;
                    $('#error-' + city_id).show();
                    $('.city_loading_mask').remove();
                    $('#city').show();
                });
            }
        };

        var getPostcodesForAddress = function (value) {
            var postcode_id = $('#zip').attr('id');
            var zipCode = $('#zip').val();
            inp = document.getElementById(postcode_id);
            var url = window.data_zip_url;
            var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $t('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $t('Loading') + '...</div>';
            if ($('.postcode_loading_mask').length === 0) {
                $('#zip').after(loader);
            }
            emptyInputZip('');
            $('#error-' + postcode_id).hide();
            $('.mage-error').hide();
            $('#zip').hide();
            $('#zip-select-error').remove();
            $('#zip-select').remove();
            $('.postcode_billing_notinlist').remove();
            $('.postcode_br_billing_notinlist').remove();
            var state = '';
            if ($("[name='region_id']").css('display') !== "none") {
                state = $('#region_id').val();
            }
            $.ajax({
                url: url,
                type: "get",
                data: "city=" + value + '&state=' + state + '&country_id=' + $('#country').val(),
                dataType: 'json',
                timeout: 15000
            }).done(function (response) {
                $('#error-' + postcode_id).show();
                $('.mage-error').show();
                $('.postcode_loading_mask').remove();
                $('#zip').show();
                var options = '<select onchange="window.getZipState(this.value)" id="' + postcode_id + '-select" class="validate-select select" title="' + $t('Postcode') + '" name="zip-select" ><option value="">' + $t(window.zip_option_title) + '</option>';
                if (response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
                        var selected = '';
                        if (zipCode === response[i]) {
                            selected = 'selected="selected"';
                            $('#zip').val(zipCode);
                        }
                        options += '<option ' + selected + ' value="' + response[i] + '">' + response[i] + '</option>';
                    }
                    options += "</select>";
                    if (window.data_zip_link !== 0) {
                        var title = window.data_zip_title;
                        options += "<br class='postcode_br_billing_notinlist' /><a onclick='window.notInListZip()' class='postcode_billing_notinlist' href='javascript:void(0)' class='postcode_notinlist'>" + title + "</a>";
                    }
                    $('#zip').hide();
                    if ($('#zip-select').length === 0) {
                        $('#zip').after(options);
                    }
                } else {
                    $('#zip').html(inp);
                    $('.postcode_billing_notinlist').remove();
                }
            }).fail(function (error) {
                $('#error-' + postcode_id).show();
                $('.postcode_loading_mask').remove();
                $('#zip').show();
            });
        };

        window.getZipcodes = function (value, type) {
            if (value !== '' && $('#city-select').length > 0 && $('#city-select').is('select')) {
                getPostcodesForAddress(value, type);
            }
        };

        /* Zip not in list */
        window.notInListZip = function () {
            var postcode_id = "zip";
            $('#' + postcode_id + '-select').remove();
            $('.postcode_billing_notinlist').remove();
            $('.postcode_br_billing_notinlist').remove();
            $('#zip').show();
        };

        window.cityNotInList = function () {
            var city_id = "city";
            $('#' + city_id + '-select').remove();
            $('.billing_notinlist').remove();
            $('.br_billing_notinlist').remove();
            $('#city').show();
        };

        var emptyInput = function (val) {
            if (val === "") {
                val = $('#city').val();
            }
            $('#city').focus();
            $('#city').val(val).change();
            $('#city-select').show();
            if (val !== "") {
                $('#zip-error').remove();
                $('#city-select-error').remove();
                $('#city-select').removeClass('mage-error');
            }
        };

        var emptyInputZip = function (val) {
            if (val === "") {
                val = $('#zip').val();
            }
            $('#zip').focus();
            $('#zip').val(val).change();
            $('#zip-select').show();
            if (val !== "") {
                $('#zip-select-error').remove();
                $('#zip-error').remove();
                $('#zip-select').removeClass('mage-error');
            }
        };

        window.getCityState = function (val) {
            emptyInput(val);
        };

        window.getZipState = function (val) {
            emptyInputZip(val);
        };
    });
