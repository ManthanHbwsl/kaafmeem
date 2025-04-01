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
    'Magento_Ui/js/modal/alert',
    'Plyr',
    'jquery/ui',
    'mage/translate'
], function ($, alert, Plyr) {
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

        $.widget('mage.productVideoLoader', {

            /**
             * @private
             */
            _create: function () {
                switch (this.element.data('type')) {
                    case 'youtube':
                        this.element.videoYoutube();
                        this._player = this.element.data('mageVideoYoutube');
                        break;

                    case 'vimeo':
                        this.element.videoVimeo();
                        this._player = this.element.data('mageVideoVimeo');
                        break;
                    case 'upload':
                        this.element.videoPlyr();
                        break;
                    default:
                        throw {
                            name: $.mage.__('Video Error'),
                            message: $.mage.__('Unknown video type'),

                            /**
                             * Return string
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

        $.widget('mage.videoYoutube', $.mage.productVideoLoader, {

            /**
             * Initialization of the Youtube widget
             * @private
             */
            _create: function () {
                var self = this;

                var src = this.element.data('videourl')+'?autoplay=1&mute=1';
                
                this._initialize();

                var videoPlayerConfiguration = JSON.parse(window.playerConfiguration);

                // if (this.element.find('.plyr__video-embed').length == 0) {
                this.element.append(
                    $('<div>')
                        .attr('class', 'plyr__video-embed')
                        .attr('id', 'player')
                        .attr('width', this._width)
                        .attr('height', this._height)
                )
                // }
                this.element.find('.plyr__video-embed').append(
                    $('<iframe>')
                        .attr('width', this._width)
                        .attr('height', this._height)
                        .attr('src', src)
                        .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
                );

                var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
                var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
                new Plyr('#player', {
                    controls: finalControls,
                    autoplay: videoPlayerConfiguration.autoplay,
                    loop : {active : videoPlayerConfiguration.loop}
                });
            },
        });

        $.widget('mage.videoVimeo', $.mage.productVideoLoader, {

            /**
             * Initialize the Vimeo widget
             * @private
             */
            _create: function () {
                var timestamp,
                    src,
                    additionalParams;

                this._initialize();
                timestamp = new Date().getTime();

                if (this._autoplay) {
                    additionalParams += '&autoplay=1';
                }

                src = 'https://player.vimeo.com/video/' +
                    this._code + '?api=1&player_id=vimeo' +
                    this._code +
                    timestamp +
                    additionalParams;
                
                var videoPlayerConfiguration = JSON.parse(window.playerConfiguration);
                    
                this.element.append(
                    $('<div>')
                        .attr('class', 'plyr__video-embed')
                        .attr('id', 'player')
                        .attr('width', this._width)
                        .attr('height', this._height)
                )
            
                this.element.find('.plyr__video-embed').append(
                    $('<iframe>')
                        .attr('frameborder', 0)
                        .attr('id', 'vimeo' + this._code + timestamp)
                        .attr('width', this._width)
                        .attr('height', this._height)
                        .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
                        .attr('src', src)
                );

                var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
                var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
                new Plyr('#player', {
                    controls: finalControls,
                    autoplay: videoPlayerConfiguration.autoplay,
                    loop : {active : videoPlayerConfiguration.loop}
                });
            }
        });

        $.widget('mage.videoPlyr', $.mage.productVideoLoader, {

            /**
             * Initialize the Plyr widget
             * @private
             */
            _create: function () {
                var videoPlayerConfiguration = JSON.parse(window.playerConfiguration);
                this.element.append(
                    $('<video>')
                        .attr('id', 'player')
                        .attr('width', this._width)
                        .attr('height', this._height)
                        .attr('style', '--plyr-color-main:'+videoPlayerConfiguration.playercolor)
                )
                this.element.find('#player').append(
                    $('<source>')
                    .attr('src', this.element.data('videourl'))
                )
                
                var controls = ['play', 'progress', 'current-time', 'settings', 'pip', 'airplay'];
                var finalControls = $.merge(controls, videoPlayerConfiguration.playeroptions)
                new Plyr('#player', {
                    controls: finalControls,
                    autoplay: videoPlayerConfiguration.autoplay,
                    loop : {active : videoPlayerConfiguration.loop}
                });
            }
        });
        
        $.widget('mage.videoData', {
            options: {
                youtubeKey: '',
                eventSource: '' //where is data going from - focus out or click on button
            },

            _REQUEST_VIDEO_INFORMATION_TRIGGER: 'request_video_information',

            _UPDATE_VIDEO_INFORMATION_TRIGGER: 'updated_video_information',

            _START_UPDATE_INFORMATION_TRIGGER: 'update_video_information',

            _ERROR_UPDATE_INFORMATION_TRIGGER: 'error_updated_information',

            _FINISH_UPDATE_INFORMATION_TRIGGER: 'finish_update_information',

            _VIDEO_URL_VALIDATE_TRIGGER: 'validate_video_url',

            _videoInformation: null,

            _currentVideoUrl: null,

            /**
             * @private
             */
            _init: function () {
                this.element.on(this._START_UPDATE_INFORMATION_TRIGGER, $.proxy(this._onRequestHandler, this));
                this.element.on(this._ERROR_UPDATE_INFORMATION_TRIGGER, $.proxy(this._onVideoInvalid, this));
                this.element.on(this._FINISH_UPDATE_INFORMATION_TRIGGER, $.proxy(
                    function () {
                        this._currentVideoUrl = null;
                    }, this
                ));
                this.element.on(this._VIDEO_URL_VALIDATE_TRIGGER, $.proxy(this._onUrlValidateHandler, this));
            },

            /**
             * @private
             */
            _onUrlValidateHandler: function (event, callback, forceVideo = true) {
                var url = this.element.val(),
                    videoInfo;

                videoInfo = this._validateURL(url, forceVideo);

                if (videoInfo) {
                    callback();
                } else {
                    this._onRequestError($.mage.__('Invalid video url'));
                }
            },

            /**
             * @private
             */
            _onRequestHandler: function () {
                var url = this.element.val(),
                    self = this,
                    videoInfo,
                    type,
                    id,
                    googleapisUrl;

                if (this._currentVideoUrl === url) {
                    return;
                }

                this._currentVideoUrl = url;

                this.element.trigger(this._REQUEST_VIDEO_INFORMATION_TRIGGER, {
                    url: url
                });

                if (!url) {
                    return;
                }

                videoInfo = this._validateURL(url, true);

                if (!videoInfo) {
                    this._onRequestError($.mage.__('Invalid video url'));

                    return;
                }

                /**
                 *
                 * @param {Object} data
                 * @private
                 */
                function _onYouTubeLoaded(data) {
                    var tmp,
                        uploadedFormatted,
                        respData,
                        createErrorMessage;

                    /**
                     * Create errors message
                     *
                     * @returns {String}
                     */
                    createErrorMessage = function () {
                        var error = data.error,
                            errors = error.errors,
                            i,
                            errLength = errors.length,
                            tmpError,
                            errReason,
                            errorsMessage = [];

                        for (i = 0; i < errLength; i++) {
                            tmpError = errors[i];
                            errReason = tmpError.reason;

                            if (['keyInvalid'].indexOf(errReason) !== -1) {
                                errorsMessage.push($.mage.__('Youtube API key is invalid'));

                                break;
                            }

                            errorsMessage.push(tmpError.message);
                        }

                        return $.mage.__('Video cant be shown due to the following reason: ') +
                            $.unique(errorsMessage).join(', ');
                    };

                    if (data.error && [400, 402, 403].indexOf(data.error.code) !== -1) {
                        this._onRequestError(createErrorMessage());

                        return;
                    }

                    if (!data.items || data.items.length < 1) {
                        this._onRequestError($.mage.__('Video not found'));

                        return;
                    }

                    tmp = data.items[0];
                    uploadedFormatted = tmp.snippet.publishedAt.replace('T', ' ').replace(/\..+/g, '');
                    respData = {
                        duration: this._formatYoutubeDuration(tmp.contentDetails.duration),
                        channel: tmp.snippet.channelTitle,
                        channelId: tmp.snippet.channelId,
                        uploaded: uploadedFormatted,
                        title: tmp.snippet.localized.title,
                        description: tmp.snippet.description,
                        thumbnail: tmp.snippet.thumbnails.high.url,
                        videoId: videoInfo.id,
                        videoProvider: videoInfo.type,
                        useYoutubeNocookie: videoInfo.useYoutubeNocookie
                    };
                    this._videoInformation = respData;
                    this.element.trigger(this._UPDATE_VIDEO_INFORMATION_TRIGGER, respData);
                    this.element.trigger(this._FINISH_UPDATE_INFORMATION_TRIGGER, true);
                }

                /**
                 * @private
                 */
                function _onVimeoLoaded(data) {
                    var tmp,
                        respData;

                    if (data.length < 1) {
                        this._onRequestError($.mage.__('Video not found'));

                        return null;
                    }
                    tmp = data[0];
                    respData = {
                        duration: this._formatVimeoDuration(tmp.duration),
                        channel: tmp['user_name'],
                        channelId: tmp['user_url'],
                        uploaded: tmp['upload_date'],
                        title: tmp.title,
                        description: tmp.description.replace(/(&nbsp;|<([^>]+)>)/ig, ''),
                        thumbnail: tmp['thumbnail_large'],
                        videoId: videoInfo.id,
                        videoProvider: videoInfo.type
                    };
                    this._videoInformation = respData;
                    this.element.trigger(this._UPDATE_VIDEO_INFORMATION_TRIGGER, respData);
                    this.element.trigger(this._FINISH_UPDATE_INFORMATION_TRIGGER, true);
                }

                function _onCustomLoaded(data) {
                    var tmp,
                        respData;

                    if (data.length < 1) {
                        this._onRequestError($.mage.__('Video not found'));

                        return null;
                    }
                    tmp = data._currentVideoUrl;
                    respData = {
                        videoId: tmp,
                        videoProvider: 'upload'
                    };
                    self._videoInformation = respData;
                    self.element.trigger(self._UPDATE_VIDEO_INFORMATION_TRIGGER, respData);
                    self.element.trigger(self._FINISH_UPDATE_INFORMATION_TRIGGER, true);
                }

                type = videoInfo.type;
                id = videoInfo.id;

                if (type === 'youtube') {
                    googleapisUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' +
                        id +
                        '&part=snippet,contentDetails&key=' +
                        this.options.youtubeKey + '&alt=json&callback=?';
                    $.getJSON(googleapisUrl,
                        {
                            format: 'json'
                        },
                        $.proxy(_onYouTubeLoaded, self)
                    ).fail(
                        function () {
                            self._onRequestError('Video not found');
                        }
                    );
                } else if (type === 'vimeo') {
                    $.ajax({
                        url: 'https://www.vimeo.com/api/v2/video/' + id + '.json',
                        dataType: 'jsonp',
                        data: {
                            format: 'json'
                        },
                        timeout: 5000,
                        success:  $.proxy(_onVimeoLoaded, self),

                        /**
                         * @private
                         */
                        error: function () {
                            self._onRequestError($.mage.__('Video not found'));
                        }
                    });
                } else if (type === 'custom') {
                    _onCustomLoaded(self);
                }
            },

            /**
             * @private
             */
            _onVideoInvalid: function (event, data) {
                this._videoInformation = null;
                this.element.val('');
                alert({
                    content: 'Error: "' + data + '"'
                });
            },

            /**
             * @private
             */
            _onRequestError: function (error) {
                this.element.trigger(this._ERROR_UPDATE_INFORMATION_TRIGGER, error);
                this.element.trigger(this._FINISH_UPDATE_INFORMATION_TRIGGER, false);
                this._currentVideoUrl = null;
            },

            /**
             * @private
             */
            _formatYoutubeDuration: function (duration) {
                var match = duration.match(/PT(\d+H)?(\d+M)?(\d+S)?/),
                    hours = parseInt(match[1], 10) || 0,
                    minutes = parseInt(match[2], 10) || 0,
                    seconds = parseInt(match[3], 10) || 0;

                return this._formatVimeoDuration(hours * 3600 + minutes * 60 + seconds);
            },

            /**
             * @private
             */
            _formatVimeoDuration: function (seconds) {
                return (new Date(seconds * 1000)).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0];
            },

            /**
             * @private
             */
            _parseHref: function (href) {
                var a = document.createElement('a');

                a.href = href;

                return a;
            },

            /**
             * @private
             */
            _validateURL: function (href, forceVideo) {
                var id,
                    type,
                    ampersandPosition,
                    vimeoRegex,
                    useYoutubeNocookie = false;

                if (typeof href !== 'string') {
                    return href;
                }
                href = this._parseHref(href);

                if (href.host.match(/youtube\.com/) && href.search) {

                    id = href.search.split('v=')[1];

                    if (id) {
                        ampersandPosition = id.indexOf('&');
                        type = 'youtube';
                    }

                    if (id && ampersandPosition !== -1) {
                        id = id.substring(0, ampersandPosition);
                    }

                } else if (href.host.match(/youtube\.com|youtu\.be|youtube-nocookie.com/)) {
                    id = href.pathname.replace(/^\/(embed\/|v\/)?/, '').replace(/\/.*/, '');
                    type = 'youtube';

                    if (href.host.match(/youtube-nocookie.com/)) {
                        useYoutubeNocookie = true;
                    }
                } else if (href.host.match(/vimeo\.com/)) {
                    type = 'vimeo';
                    vimeoRegex = new RegExp(['https?:\\/\\/(?:www\\.|player\\.)?vimeo.com\\/(?:channels\\/(?:\\w+\\/)',
                        '?|groups\\/([^\\/]*)\\/videos\\/|album\\/(\\d+)\\/video\\/|video\\/|)(\\d+)(?:$|\\/|\\?)'
                    ].join(''));

                    if (href.href.match(vimeoRegex) != null) {
                        id = href.href.match(vimeoRegex)[3];
                    }
                }

                if ((!id || !type) && forceVideo) {
                    id = href.href;
                    type = 'custom';
                }

                return id ? {
                    id: id, type: type, s: href.search.replace(/^\?/, ''), useYoutubeNocookie: useYoutubeNocookie
                } : false;
            }
        });
    });
