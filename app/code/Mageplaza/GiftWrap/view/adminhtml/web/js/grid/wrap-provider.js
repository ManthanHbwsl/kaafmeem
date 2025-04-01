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
    'underscore',
    'jquery',
    'Magento_Ui/js/grid/provider',
    'Magento_Catalog/js/price-utils',
    'mage/translate',
    'Mageplaza_GiftWrap/js/lib/Chart.bundle.min',
    'Mageplaza_GiftWrap/js/lib/chartjs-plugin-labels'
], function (_, $, Element, priceUtils, $t) {
    'use strict';

    return Element.extend({
        /**
         * @param data
         * @returns {*}
         */
        processData: function (data) {
            $('.dashboard-item-content-wrap-chart table tbody tr').remove();
            $('.dashboard-item-content-postcard-chart table tbody tr').remove();
            if ($('.dashboard-item-content-wrap-chart').length) {
                this.buildChart(data);
            }

            return this._super(data);
        },

        buildChart: function (data) {
            var items                    = data.items,
                showWrapChart            = false,
                showPostcardChart        = false,
                self                     = this,
                wrapTableNameElement     = $('.dashboard-item-content-wrap-chart .table-name'),
                postcardTableNameElement = $('.dashboard-item-content-postcard-chart .table-name'),
                wrapData                 = [],
                postcardData             = [];

            if (Object.keys(items).length) {
                var wrapIndex     = 0,
                    postcardIndex = 0;

                _.each(items, function (record) {
                    if (record.wrap_type === 'wrap') {
                        self.createChartData(wrapData, record, wrapIndex);
                        wrapIndex++;
                        if (record.revenue > 0) {
                            showWrapChart = true;
                        }
                    } else {
                        self.createChartData(postcardData, record, postcardIndex);
                        postcardIndex++;
                        if (record.revenue > 0) {
                            showPostcardChart = true;
                        }
                    }
                });

                if (showWrapChart) {
                    wrapData.sort(function (a, b) {
                        return Number(b.data) - Number(a.data);
                    });
                    wrapData = _.filter(wrapData, function (value, index) {
                        return index < 30;
                    });
                    this.addTableName(wrapData, wrapTableNameElement, 'wrap');
                    this.addRowsDataTable('.dashboard-item-content-wrap-chart table', wrapData);
                    this.createChart(this.getChartData(wrapData), 'mpgiftwrap-wrap-chart');
                }
                if (showPostcardChart) {
                    postcardData.sort(function (a, b) {
                        return Number(b.data) - Number(a.data);
                    });
                    postcardData = _.filter(postcardData, function (value, index) {
                        return index < 30;
                    });

                    this.addTableName(postcardData, postcardTableNameElement, 'postcard');
                    this.addRowsDataTable('.dashboard-item-content-postcard-chart table', postcardData);
                    this.createChart(this.getChartData(postcardData), 'mpgiftwrap-postcard-chart');
                }
            }

            this.showChart('wrap', showWrapChart);
            this.showChart('postcard', showPostcardChart);
        },

        addTableName: function (data, tableNameElement, tableType) {
            var tableTypeName = tableType === 'wrap' ? 'Gift Wraps' : 'Postcards';

            if (data.length === 30) {
                tableNameElement.text($t('Top 30 ' + tableTypeName));
            } else {
                tableNameElement.text($t('Top ' + tableTypeName))
            }
        },

        getChartData: function (data) {
            return {
                data: _.pluck(data, 'data'),
                labelColor: {
                    colors: _.pluck(data, 'color'),
                    labels: _.pluck(data, 'label')
                },
                priceFormat: this.priceFormat
            };
        },

        addRowsDataTable: function (tableElement, tableData) {
            var element = '',
                self    = this;

            _.each(tableData, function (data) {
                element += '<tr>' + '<td class="name">' + data.label + '</td>' +
                    '<td class="quantity">' + data.quantity + '</td>' +
                    '<td class="revenue">' + priceUtils.formatPrice(data.data, self.priceFormat) + '</td>' +
                    '</tr>';
            });

            $(tableElement + ' tbody').append(element);
        },

        showChart: function (wrapType, isShow) {
            if (isShow) {
                $('.dashboard-item-content-' + wrapType + '-chart').show();
            } else {
                $('.dashboard-item-content-' + wrapType + '-chart').hide();
            }
        },

        createChartData: function (chartData, record, index) {
            var pieColors = ['#ff1500', '#ffbf00', '#641f6f', '#00a10e', '#0058e6', '#8e6cef', '#8399eb', '#007ed6',
                    '#97d9ff', '#5fb7d4', '#7cdddd', '#26d7ae', '#2dcb75', '#1baa2f', '#52d726', '#d5f30b', '#ffec00', '#ffaf00'],
                color     = index < 18 ? pieColors[index] : this.randomColor();

            chartData.push({data: record.revenue, color: color, label: record.name, quantity: record.quantity});
        },

        randomColor: function () {
            return '#' + (0x1000000 + Math.random() * 0xffffff).toString(16).substr(1, 6);
        },

        createChart: function (chartData, chartElement) {
            var data = {
                type: 'pie',
                data: {
                    labels: chartData.labelColor.labels,
                    datasets: [
                        {
                            data: chartData.data,
                            fill: true,
                            backgroundColor: chartData.labelColor.colors,
                            borderWidth: 1,
                            label: ''
                        }
                    ]
                },
                options: {
                    legend: {
                        display: true,
                        position: 'right'
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var index   = tooltipItem.index;

                                return data.labels[index] + ': ' + (chartData.priceFormat ? priceUtils.formatPrice(dataset.data[index], chartData.priceFormat) : dataset.data[index]);
                            }
                        }
                    },
                    plugins: {
                        labels: {
                            render: 'percentage',
                            precision: 2,
                            fontColor: '#ffffff'
                        }
                    }
                }
            };

            if (typeof window[chartElement] !== 'undefined' && typeof window[chartElement].destroy === 'function') {
                window[chartElement].destroy();
            }

            window[chartElement] = new Chart($('#' + chartElement), data);
        }
    });
});
