define([], function () {
    'use strict';

    return function (priceBox) {
        return priceBox.extend({
            options: {
                priceTemplate: '<span class="price"><%= data.formatted %></span>'
            }
        });
    };
});