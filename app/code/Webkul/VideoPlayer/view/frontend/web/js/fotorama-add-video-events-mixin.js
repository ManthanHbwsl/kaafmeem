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
    'jquery-ui-modules/widget',
    'catalogGallery',
    'loadVideoPlayer'
], function ($, Plyr) {
    'use strict';

    /**
     * @private
     */
    var allowBase = true; //global var is needed because fotorama always fully reloads events in case of fullscreen

    /**
     * @private
     */
    function parseHref(href) {
        var a = document.createElement('a');

        a.href = href;

        return a;
    }

    /**
     * @private
     */
    function parseURL(href, forceVideo) {
        var id,
            type,
            ampersandPosition,
            vimeoRegex,
            useYoutubeNocookie = false;

        /**
         * Get youtube ID
         * @param {String} srcid
         * @returns {{}}
         */
        function _getYoutubeId(srcid) {
            if (srcid) {
                ampersandPosition = srcid.indexOf('&');

                if (ampersandPosition === -1) {
                    return srcid;
                }

                srcid = srcid.substring(0, ampersandPosition);
            }

            return srcid;
        }

        if (typeof href !== 'string') {
            return href;
        }

        href = parseHref(href);

        if (href.host.match(/youtube\.com/) && href.search) {
            id = href.search.split('v=')[1];

            if (id) {
                id = _getYoutubeId(id);
                type = 'youtube';
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
            id = href.href.match(vimeoRegex)[3];
        }

        if ((!id || !type) && forceVideo) {
            id = href.href;
            type = 'custom';
        }

        return id ? {
            id: id, type: type, s: href.search.replace(/^\?/, ''), useYoutubeNocookie: useYoutubeNocookie
        } : false;
    }

    //create AddFotoramaVideoEvents widget
    $.widget('mage.AddFotoramaVideoEvents', {
        options: {
            videoData: '',
            videoSettings: '',
            optionsVideoData: '',
            videoPlayerConfiguration: '',
            dataMergeStrategy: 'replace',
            vimeoJSFrameworkLoaded: false
        },

        /**
         * @private
         */
        onVimeoJSFramework: function () {},
        defaultVideoData: [],
        PV: 'product-video', // [CONST]
        VU: 'video-unplayed',
        PVLOADED: 'fotorama__product-video--loaded', // [CONST]
        PVLOADING: 'fotorama__product-video--loading', // [CONST]
        VID: 'video', // [CONST]
        VI: 'vimeo', // [CONST]
        FTVC: 'fotorama__video-close',
        FTAR: 'fotorama__arr',
        FTARR: 'fotorama__arr__arr',
        FTNF: 'fotorama__nav__frame',        
        FTI: 'fotorama__img',
        FTVCN:'fotorama-video-container',
        FTA: 'fotorama__active',
        fotoramaSpinner: 'fotorama__spinner',
        fotoramaSpinnerShow: 'fotorama__spinner--show',
        TI: 'video-thumb-icon',
        isFullscreen: false,
        FTCF: '[data-gallery-role="fotorama__fullscreen-icon"]',
        Base: 0, //on check for video is base this setting become true if there is any video with base role
        MobileMaxWidth: 767,
        GP: 'gallery-placeholder', //gallery placeholder class is needed to find and erase <script> tag
        videoData: null,
        videoDataPlaceholder: [{
            id: '',
            isBase: true,
            mediaType: 'image',
            provider: ''
        }],

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            $(this.element).on('gallery:loaded',  $.proxy(function () {
                this.fotoramaItem = $(this.element).find('.fotorama-item');
                this._initialize();
            }, this));
        },

        /**
         *
         * @private
         */
        _initialize: function () {
            if (!this.defaultVideoData.length) {
                this.defaultVideoData = this.options.videoData;
            }

            // If product does not have images, no video data generated,
            // but for configurable product we still need a video data, in case of 'prepend' gallery strategy.
            if (!this.defaultVideoData.length && !this.options.videoData.length) {
                this.defaultVideoData = this.options.videoData = this.videoDataPlaceholder;
            }

            this.clearEvents();

            if (this._checkForVideoExist()) {
                this._checkFullscreen();
                this._listenForFullscreen();
                this._checkForVimeo();
                this._isVideoBase();
                this._initFotoramaVideo();
                this._attachFotoramaEvents();
            }
        },

        /**
         * Clear gallery events to prevent duplicated calls.
         *
         * @private
         */
        clearEvents: function () {
            if (this.fotoramaItem !== undefined) {
                this.fotoramaItem.off(
                    'fotorama:show.' + this.PV +
                    ' fotorama:showend.' + this.PV +
                    ' fotorama:fullscreenenter.' + this.PV +
                    ' fotorama:fullscreenexit.' + this.PV
                );
            }
        },

        /**
         *
         * @param {Object} options
         * @private
         */
        _setOptions: function (options) {
            if (options.videoData && options.videoData.length) {
                this.options.videoData = options.videoData;
            }

            this._loadVideoData(options);
            this._initialize();
        },

        /**
         * Set video data for configurable product.
         *
         * @param {Object} options
         * @private
         */
        _loadVideoData: function (options) {
            if (options.selectedOption) {
                if (options.dataMergeStrategy === 'prepend') {
                    this.options.videoData = [].concat(
                        this.options.optionsVideoData[options.selectedOption],
                        this.defaultVideoData
                    );
                } else {
                    this.options.videoData = this.options.optionsVideoData[options.selectedOption];
                }
            } else {
                this.options.videoData = this.defaultVideoData;
            }
        },

        /**
         *
         * @private
         */
        _checkFullscreen: function () {
            if (this.fotoramaItem.data('fotorama').fullScreen || false) {
                this.isFullscreen = true;
            }
        },

        /**
         *
         * @private
         */
        _listenForFullscreen: function () {
            this.fotoramaItem.on('fotorama:fullscreenenter.' + this.PV, $.proxy(function () {
                this.isFullscreen = true;
            }, this));

            this.fotoramaItem.on('fotorama:fullscreenexit.' + this.PV, $.proxy(function () {
                this.isFullscreen = false;
                this._hideVideoArrows();
            }, this));
        },

        /**
         *
         * @param {Object} inputData
         * @param {bool} isJSON
         * @returns {{}}
         * @private
         */
        _createVideoData: function (inputData, isJSON) {
            var videoData = [],
                dataUrl,
                tmpVideoData,
                tmpInputData,
                i;

            if (isJSON) {
                inputData = $.parseJSON(inputData);
            }

            for (i = 0; i < inputData.length; i++) {
                tmpInputData = inputData[i];
                dataUrl = '';
                tmpVideoData = {
                    mediaType: '',
                    isBase: '',
                    id: '',
                    provider: ''
                };
                tmpVideoData.mediaType = this.VID;

                if (tmpInputData.mediaType !== 'external-video') {
                    tmpVideoData.mediaType = tmpInputData.mediaType;
                }

                tmpVideoData.isBase = tmpInputData.isBase;

                if (tmpInputData.videoUrl && tmpInputData.videoUrl !== null) {
                    dataUrl = tmpInputData.videoUrl;
                    dataUrl = parseURL(dataUrl);
                    tmpVideoData.id = dataUrl.id;
                    tmpVideoData.provider = dataUrl.type;
                    tmpVideoData.videoUrl = tmpInputData.videoUrl;
                    tmpVideoData.useYoutubeNocookie = dataUrl.useYoutubeNocookie;
                    if (dataUrl == false) {
                        tmpVideoData.provider = 'upload';
                        tmpVideoData.videoUrl = tmpInputData.videoUrl;
                    }
                }                

                videoData.push(tmpVideoData);
            }

            return videoData;
        },

        /**
         *
         * @param {Object} fotorama
         * @param {bool} isBase
         * @private
         */
        _createCloseVideo: function (fotorama, isBase) {
            var closeVideo;

            this.fotoramaItem.find('.' + this.FTVC).remove();
            this.fotoramaItem.append('<div class="' + this.FTVC + '"></div>');
            this.fotoramaItem.css('position', 'relative');
            closeVideo = this.fotoramaItem.find('.' + this.FTVC);
            this._closeVideoSetEvents(closeVideo, fotorama);

            if (
                isBase &&
                this.options.videoData[fotorama.activeIndex].isBase &&
                $(window).width() > this.MobileMaxWidth) {
                this._showCloseVideo();
            }
        },

        /**
         *
         * @private
         */
        _hideCloseVideo: function () {
            this.fotoramaItem
                .find('.' + this.FTVC)
                .removeClass('fotorama-show-control');
        },

        /**
         *
         * @private
         */
        _showCloseVideo: function () {
            this.fotoramaItem
                .find('.' + this.FTVC)
                .addClass('fotorama-show-control');
        },

        /**
         *
         * @param {jQuery} $closeVideo
         * @param {jQuery} fotorama
         * @private
         */
        _closeVideoSetEvents: function ($closeVideo, fotorama) {
            $closeVideo.on('click', $.proxy(function () {
                this._unloadVideoPlayer(fotorama.activeFrame.$stageFrame.parent(), fotorama, true);
                this._hideCloseVideo();
            }, this));
        },

        /**
         *
         * @returns {Boolean}
         * @private
         */
        _checkForVideoExist: function () {
            var key, result, checker, videoSettings;

            if (!this.options.videoData) {
                return false;
            }

            if (!this.options.videoSettings) {
                return false;
            }

            result = this._createVideoData(this.options.videoData, false);
            checker = false;
            videoSettings = this.options.videoSettings[0];
            videoSettings.playIfBase = parseInt(videoSettings.playIfBase, 10);
            videoSettings.showRelated = parseInt(videoSettings.showRelated, 10);
            videoSettings.videoAutoRestart = parseInt(videoSettings.videoAutoRestart, 10);

            for (key in result) {
                if (result[key].mediaType === this.VID) {
                    checker = true;
                }
            }

            if (checker) {
                this.options.videoData = result;
            }

            return checker;
        },

        /**
         *
         * @private
         */
        _checkForVimeo: function () {
            var allVideoData = this.options.videoData,
                videoItem;

            if (window.Froogaloop) { // prevent duplicated initialization
                return;
            }

            for (videoItem in allVideoData) {
                if (allVideoData[videoItem].provider === this.VI) {
                    this._loadVimeoJSFramework();

                    return;
                }
            }
        },

        /**
         *
         * @private
         */
        _isVideoBase: function () {
            var allVideoData = this.options.videoData,
                videoItem,
                allVideoDataKeys,
                key,
                i;

            allVideoDataKeys = Object.keys(allVideoData);

            for (i = 0; i < allVideoDataKeys.length; i++) {
                key = allVideoDataKeys[i];
                videoItem = allVideoData[key];

                if (
                    videoItem.mediaType === this.VID && videoItem.isBase &&
                    this.options.videoSettings[0].playIfBase && allowBase
                ) {
                    this.Base = true;
                    allowBase = false;
                }
            }

            if (!this.isFullscreen) {
                this._createCloseVideo(this.fotoramaItem.data('fotorama'), this.Base);
            }
        },

        /**
         *
         * @private
         */
        _loadVimeoJSFramework: function () {
            var element = document.createElement('script'),
                scriptTag = document.getElementsByTagName('script')[0];

            element.async = true;
            element.src = 'https://f.vimeocdn.com/js/froogaloop2.min.js';

            /**
             * Vimeo js framework on load callback.
             */
            element.onload = function () {
                this.onVimeoJSFramework();
                this.vimeoJSFrameworkLoaded = true;
            }.bind(this);
            scriptTag.parentNode.insertBefore(element, scriptTag);
        },

        /**
         *
         * @param {Event} e
         * @private
         */
        _initFotoramaVideo: function (e) {
            var fotorama = this.fotoramaItem.data('fotorama'),
                thumbsParent,
                thumbs,
                t;

            if (!fotorama.activeFrame.$navThumbFrame) {
                this.fotoramaItem.on('fotorama:showend.' + this.PV, $.proxy(function (evt, fotoramaData) {
                    $(fotoramaData.activeFrame.$stageFrame).removeAttr('href');
                }, this));

                this._startPrepareForPlayer(e, fotorama);

                return null;
            }

            fotorama.data.map($.proxy(this._setItemType, this));
            thumbsParent = fotorama.activeFrame.$navThumbFrame.parent();
            thumbs = thumbsParent.find('.fotorama__nav__frame:visible');

            for (t = 0; t < thumbs.length; t++) {
                this._setThumbsIcon(thumbs.eq(t), t);
                this._checkForVideo(e, fotorama, t + 1);
            }

            this.fotoramaItem.on('fotorama:showend.' + this.PV, $.proxy(function (evt, fotoramaData) {
                $(fotoramaData.activeFrame.$stageFrame).removeAttr('href');
            }, this));
        },

        /**
         *
         * @param {Object} elem
         * @param {Number} i
         * @private
         */
        _setThumbsIcon: function (elem, i) {
            var fotorama = this.fotoramaItem.data('fotorama');

            if (fotorama.options.nav === 'dots' && elem.hasClass(this.TI)) {
                elem.removeClass(this.TI);
            }

            if (this.options.videoData[i].mediaType === this.VID &&
                fotorama.data[i].type ===  this.VID &&
                fotorama.options.nav === 'thumbs') {
                elem.addClass(this.TI);
            }
        },

        /**
         * Temporary solution with adding types for configurable product items
         *
         * @param {Object} item
         * @param {Number} i
         * @private
         */
        _setItemType: function (item, i) {
            !item.type && (item.type = this.options.videoData[i].mediaType);
        },

        /**
         * Attach
         *
         * @private
         */
        _attachFotoramaEvents: function () {
            // this.fotoramaItem.on('fotorama:showend.' + this.PV, $.proxy(function (e, fotorama) {
            //     this._startPrepareForPlayer(e, fotorama);
            // }, this));

            // this.fotoramaItem.on('fotorama:show.' + this.PV, $.proxy(function (e, fotorama) {
            //     this._unloadVideoPlayer(fotorama.activeFrame.$stageFrame.parent(), fotorama, true);
            // }, this));

            // this.fotoramaItem.on('fotorama:fullscreenexit.' + this.PV, $.proxy(function (e, fotorama) {
            //     fotorama.activeFrame.$stageFrame.find('.' + this.PV).remove();
            //     this._startPrepareForPlayer(e, fotorama);
            // }, this));
            var self = this; 
            $('.' + this.FTARR).on('click', function() {
                self.manageFrames();
            });
            $('.'+this.FTNF).on('click', function() {
                self.manageFrames();
            });
            setInterval(
                function(){
                    if ($('.fotorama__stage__shaft').hasClass('fotorama__grabbing')) {
                        if ($('.' + self.FTI).hasClass('fotorama__arr--hidden')) {
                            self.manageFrames()
                        }
                    }
                }
                , 300
            )
        },

        manageFrames() {
            var self = this;
            $('.' + self.FTI).removeClass('fotorama__arr--hidden');
            $('.' + self.FTVCN).removeClass('remove-after');
            $('.' + self.FTVCN + ' #player-video').hide();
            $('.' + self.FTVCN + ' .plyr__video-embed').hide();
            $('.' + self.FTVCN).addClass(self.VU);
            $('.' + self.FTVCN+" .product-video").addClass(self.VU);
            this._initFotoramaVideo();   
            this._resetContainerHeight(); 
        },

        /**
         * Start prepare for player
         *
         * @param {Event} e
         * @param {jQuery} fotorama
         * @private
         */
        _startPrepareForPlayer: function (e, fotorama) {
            this._unloadVideoPlayer(fotorama.activeFrame.$stageFrame.parent(), fotorama, false);
            this._checkForVideo(e, fotorama, fotorama.activeFrame.i);
            this._checkForVideo(e, fotorama, fotorama.activeFrame.i - 1);
            this._checkForVideo(e, fotorama, fotorama.activeFrame.i + 1);
        },

        /**
         * Check for video
         *
         * @param {Event} e
         * @param {jQuery} fotorama
         * @param {Number} number
         * @private
         */
        _checkForVideo: function (e, fotorama, number) {
            var videoData = this.options.videoData[number - 1],
                $image = fotorama.data[number - 1];

            if ($image) {
                !$image.type && this._setItemType($image, number - 1);

                if ($image.type === 'image') {
                    $image.$navThumbFrame && $image.$navThumbFrame.removeClass(this.TI);
                    this._hideCloseVideo();

                    return;
                } else if ($image.$navThumbFrame && $image.type === 'video') {
                    !$image.$navThumbFrame.hasClass(this.TI) && $image.$navThumbFrame.addClass(this.TI);
                }

                $image = $image.$stageFrame;
            }

            if ($image && videoData && videoData.mediaType === this.VID) {
                $(fotorama.activeFrame.$stageFrame).removeAttr('href');
                this._prepareForVideoContainer($image, videoData, fotorama, number);
            }

            if (this.isFullscreen && this.fotoramaItem.data('fotorama').activeFrame.i === number) {
                this.fotoramaItem.data('fotorama').activeFrame.$stageFrame[0].click();
            }
        },

        /**
         * Prepare for video container
         *
         * @param {jQuery} $image
         * @param {Object} videoData
         * @param {Object} fotorama
         * @param {Number} number
         * @private
         */
        _prepareForVideoContainer: function ($image, videoData, fotorama, number) {
            $image.addClass('fotorama-video-container').addClass(this.VU);
            this._createVideoContainer(videoData, $image);
            this._setVideoEvent($image, this.PV, fotorama, number);
        },

        /**
         * Create video container
         *
         * @param {Object} videoData
         * @param {jQuery} $image
         * @private
         */
        _createVideoContainer: function (videoData, $image) {
            var videoSettings;

            videoSettings = this.options.videoSettings[0];
            $image.find('.' + this.PV).remove();
            $image.append(
                '<div class="' +
                this.PV +
                '" data-related="' +
                videoSettings.showRelated +
                '" data-videourl="' +
                videoData.videoUrl +
                '" data-loop="' +
                videoSettings.videoAutoRestart +
                '" data-type="' +
                videoData.provider +
                '" data-code="' +
                videoData.id +
                '"  data-youtubenocookie="' +
                videoData.useYoutubeNocookie +
                '" data-width="100%" data-height="100%"></div>'
            );
            // const player = Plyr

            // window.player = player;

        },

        /**
         *
         * @param {Object} $image
         * @param {Object} PV
         * @param {Object} fotorama
         * @param {Number} number
         * @private
         */
        _setVideoEvent: function ($image, PV, fotorama, number) {
            $image.find('.magnify-lens').remove();
            $image
                .off('click tap', $.proxy(this._clickHandler, this))
                .on('click tap', $.proxy(this._clickHandler, this));
            this._handleBaseVideo(fotorama, number); //check for video is it base and handle it if it's base
        },

        /**
         * Hides preview arrows above video player.
         * @private
         */
        _hideVideoArrows: function () {
            var arrows = $('.' + this.FTAR);

            arrows.removeClass('fotorama__arr--shown');
            arrows.removeClass('fotorama__arr--hidden');
        },

        /**
         * @private
         */
        _showLoader: function () {
            var spinner = this.fotoramaItem.find('.' + this.fotoramaSpinner);

            spinner.addClass(this.fotoramaSpinnerShow);
            this.fotoramaItem.data('fotorama').activeFrame.$stageFrame.addClass(this.PVLOADING);
        },

        /**
         * @private
         */
        _hideLoader: function () {
            var spinner = this.fotoramaItem.find('.' + this.fotoramaSpinner);

            spinner.removeClass(this.fotoramaSpinnerShow);
            this.fotoramaItem.data('fotorama').activeFrame.$stageFrame.removeClass(this.PVLOADING);
        },
        
        /**
         * @private
         */
        _changeContainerHeight:function(){

            var v = document.getElementById("player-video");   

            setTimeout(() => $(".plyr__controls").on('mousedown mouseup keydown keyup touchstart touchend', (event) => {event.stopPropagation();}), 3000);
            if(v) {
                v.addEventListener( "loadedmetadata", function (e) {
                    var width = this.videoWidth,
                        height = this.videoHeight;
                    if(height > width){  
                        var prev_width = $('.fotorama__stage').css("width");
                        var prev_height = $('.fotorama__stage').css("height");
                        var width_ratio = ((prev_width.replace("px","") - '160') / width).toFixed(2); 
                        var height_value =  Math.round(height * width_ratio) + 6;                
                    $('.fotorama__stage').css("height",height_value + 'px');
                    $('.fotorama__stage').css("min-height",prev_height);
                    $('.fotorama__stage').css("min-width",prev_width);
                    }
                }, false ); 
            }           
        },
        _resetContainerHeight:function(){
            var height = $('.fotorama__stage').css("min-height");
            var width = $('.fotorama__stage').css("min-width");
            if(height != "0px" && width != "0px"){
                $('.fotorama__stage').css("height",height);
                $('.fotorama__stage').css("width",width);
            }            
        },

        /**
         * @param {Event} event
         * @private
         */
        _clickHandler: function (event) {
            var type;
            var videoPlayerConfiguration = this.options;
            if ($(event.target).hasClass(this.VU)) {
                $(event.target).removeClass(this.VU);
                type = $(event.target).find('.' + this.PV).data('type');

                $(event.target).find('.' + this.PV).productVideoPlayerLoader(videoPlayerConfiguration);
                $(event.target).find('#player-video').show();
                $(event.target).find('.plyr__video-embed').show();

                $('.' + this.FTI).addClass('fotorama__arr--hidden');
                $('.' + this.FTVCN).addClass('remove-after');
                this._changeContainerHeight();
            }
        },

        /**
         * Handle base video
         * @param {Object} fotorama
         * @param {Number} srcNumber
         * @private
         */
        _handleBaseVideo: function (fotorama, srcNumber) {
            var waitForFroogaloop,
                videoData = this.options.videoData,
                activeIndex = fotorama.activeIndex,
                number = parseInt(srcNumber, 10),
                activeIndexIsBase = videoData[activeIndex];

            if (!this.Base) {
                return;
            }

            if (activeIndexIsBase && number === 1 && $(window).width() > this.MobileMaxWidth) {
                if (this.options.videoData[fotorama.activeIndex].provider === this.VI) {
                    waitForFroogaloop = setInterval($.proxy(function () {
                        if (window.Froogaloop) {
                            clearInterval(waitForFroogaloop);
                            fotorama.requestFullScreen();
                            this.fotoramaItem.data('fotorama').activeFrame.$stageFrame[0].click();
                            this.Base = false;
                        }
                    }, this), 50);
                } else { //if not a vimeo - play it immediately with a little lag in case for fotorama fullscreen
                    setTimeout($.proxy(function () {
                        fotorama.requestFullScreen();
                        this.fotoramaItem.data('fotorama').activeFrame.$stageFrame[0].click();
                        this.Base = false;
                    }, this), 50);
                }
            }
        },

        /**
         * Destroy video player
         * @param {jQuery} $wrapper
         * @param {jQuery} current
         * @param {bool} close
         * @private
         */
        _unloadVideoPlayer: function ($wrapper, current, close) {
            var self = this;

            if (!$wrapper) {
                return;
            }

            $wrapper.find('.' + this.PVLOADED).removeClass(this.PVLOADED);
            this._hideLoader();

            $wrapper.find('.' + this.PV).each(function () {
                var $item = $(this).parent(),
                    cloneVideoDiv,
                    iframeElement = $(this).find('iframe'),
                    currentIndex,
                    itemIndex;

                if (iframeElement.length === 0) {
                    return;
                }

                currentIndex = current.activeFrame.$stageFrame.index();
                itemIndex = $item.index();

                if (currentIndex === itemIndex && !close) {
                    return;
                }

                if (currentIndex !== itemIndex && close) {
                    return;
                }

                iframeElement.remove();
                cloneVideoDiv = $(this).clone();
                $(this).remove();
                $item.append(cloneVideoDiv);
                $item.addClass(self.VU);

                self._hideCloseVideo();
                self._hideVideoArrows();

                if (self.isFullscreen && !self.fotoramaItem.data('fotorama').options.fullscreen.arrows) {
                    if ($('.' + self.FTAR + '--prev').is(':focus') || $('.' + self.FTAR + '--next').is(':focus')) {
                        $(self.FTCF).focus();
                    }
                }
            });
        }
    });

    return $.mage.AddFotoramaVideoEvents;
});
