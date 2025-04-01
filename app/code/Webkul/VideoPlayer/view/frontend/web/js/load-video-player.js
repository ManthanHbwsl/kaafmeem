/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_VideoPlayer
 * @author    Webkul
 * @copyright Copyright (c)   Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    'jquery',
    'Plyr',
    'jquery-ui-modules/widget'
], function ($, Plyr) {
    'use strict';

    var videoRegister = {
        _register: {},

        /**
         * Checks, if api is already registered
         *
         * @param {String} api
         * @returns {bool}
         */
        isRegistered: function (api) {
            return this._register[api] !== undefined;
        },

        /**
         * Checks, if api is loaded
         *
         * @param {String} api
         * @returns {bool}
         */
        isLoaded: function (api) {
            return this._register[api] !== undefined && this._register[api] === true;
        },

        /**
         * Register new video api
         * @param {String} api
         * @param {bool} loaded
         */
        register: function (api, loaded) {
            loaded = loaded || false;
            this._register[api] = loaded;
        }
    };

    $.widget('mage.productVideoPlayerLoader', {

        /**
         * @private
         */
        _create: function () {
            var videoPlayerConfiguration = this.options;
            switch (this.element.data('type')) {
                case 'youtube':
                    this.element.videoYoutube(videoPlayerConfiguration);
                    this._player = this.element.data('mageVideoYoutube');
                    break;

                case 'vimeo':
                    this.element.videoVimeo(videoPlayerConfiguration);
                    this._player = this.element.data('mageVideoVimeo');
                    break;
                case 'upload':
                    this.element.videoPlyr(videoPlayerConfiguration);
                    break;
                default:
                    throw {
                        name: 'Video Error',
                        message: 'Unknown video type',

                        /**
                         * join name with message
                         */
                        toString: function () {
                            return this.name + ': ' + this.message;
                        }
                    };
            }
        },

        /**
         * Initializes variables
         * @private
         */
        _initialize: function () {
            this._params = this.element.data('params') || {};
            this._code = this.element.data('code');
            this._width = this.element.data('width');
            this._height = this.element.data('height');
            this._autoplay = !!this.element.data('autoplay');
            this._playing = this._autoplay || false;
            this._loop = this.element.data('loop');
            this._rel = this.element.data('related');
            this.useYoutubeNocookie = this.element.data('youtubenocookie') || false;

            this._responsive = this.element.data('responsive') !== false;

            if (this._responsive === true) {
                this.element.addClass('responsive');
            }

            this._calculateRatio();
        },

        /**
         * Calculates ratio for responsive videos
         * @private
         */
        _calculateRatio: function () {
            if (!this._responsive) {
                return;
            }
            this.element.css('paddingBottom', this._height / this._width * 100 + '%');
        }
    });

    $.widget('mage.videoYoutube', $.mage.productVideoPlayerLoader, {

        /**
         * Initialization of the Youtube widget
         * @private
         */
        _create: function () {
            var self = this;
            var src = this.element.data('videourl')+'?autoplay=1&mute=1';
            this._initialize();
            var videoPlayerConfiguration = (this.options.active != undefined)?this.options:this.options.videoPlayerConfiguration;

            if (this.element.find('#player-youtube').length == 0) {
                this.element.append(
                    $('<div>')
                        .attr('class', 'plyr__video-embed')
                        .attr('id', 'player-youtube')
                        .attr('width', this._width)
                        .attr('height', this._height)
                        .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
                )
            }
            
            this.element.find('#player-youtube').append(
                $('<iframe>')
                    .attr('width', this._width)
                    .attr('height', this._height)
                    .attr('src', src)
            );

            var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
            var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
            new Plyr('#player-youtube', {
                controls: finalControls,
                autoplay: videoPlayerConfiguration.autoplay,
                loop : {active : videoPlayerConfiguration.loop}
            });
        },
    });

    $.widget('mage.videoVimeo', $.mage.productVideoPlayerLoader, {

        /**
         * Initialize the Vimeo widget
         * @private
         */
        _create: function () {
            var timestamp,
                additionalParams = '',
                src;

            this._initialize();
            timestamp = new Date().getTime();
            this._autoplay = true;

            if (this._autoplay) {
                additionalParams += '&autoplay=1';
            }

            if (this._loop) {
                additionalParams += '&loop=1';
            }
            src = 'https://player.vimeo.com/video/' +
                this._code + '?api=1&player_id=vimeo' +
                this._code +
                timestamp +
                additionalParams;
            var videoPlayerConfiguration = (this.options.active != undefined)?this.options:this.options.videoPlayerConfiguration;
                
            if (this.element.find('#player-vimeo').length == 0) {
                this.element.append(
                    $('<div>')
                        .attr('class', 'plyr__video-embed')
                        .attr('id', 'player-vimeo')
                        .attr('width', this._width)
                        .attr('height', this._height)
                        .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
                        )
            }
            
            this.element.find('#player-vimeo').append(
                $('<iframe>')
                    .attr('frameborder', 0)
                    .attr('id', 'vimeo' + this._code + timestamp)
                    .attr('width', this._width)
                    .attr('height', this._height)
                    .attr('src', src)
            );

            var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
            var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
            new Plyr('#player-vimeo', {
                controls: finalControls,
                autoplay: videoPlayerConfiguration.autoplay,
                loop : {active : videoPlayerConfiguration.loop}
            });
        },
    });

    $.widget('mage.videoPlyr', $.mage.productVideoPlayerLoader, {

        /**
         * Initialize the Plyr widget
         * @private
         */
        _create: function () {
            var videoPlayerConfiguration = (this.options.active != undefined)?this.options:this.options.videoPlayerConfiguration;
            this.element.append(
                $('<video>')
                    .attr('id', 'player-video')
                    .attr('width', this._width)
                    .attr('height', this._height)
                    .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
            )
            this.element.find('#player-video').append(
                $('<source>')
                .attr('src', this.element.data('videourl'))
            )
            var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
            var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
            new Plyr('#player-video', {
                controls: finalControls,
                autoplay: videoPlayerConfiguration.autoplay,
                loop : {active : videoPlayerConfiguration.loop}
            });
        }
    });
});
