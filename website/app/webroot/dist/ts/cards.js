(function(){ "use strict";
var $ = window.jQuery;
var TarotGameStep;
(function (TarotGameStep) {
    /// Nothing yet
    TarotGameStep[TarotGameStep["NONE"] = 0] = "NONE";
    /// After initial step, we just shown the game
    TarotGameStep[TarotGameStep["INITIATED_GAME"] = 1] = "INITIATED_GAME";
    /// We are showing the cards, waiting to shuffle them
    TarotGameStep[TarotGameStep["READY_TO_SHUFFLE"] = 2] = "READY_TO_SHUFFLE";
    /// The cards are shuffled. We wait for the user to select the cards
    TarotGameStep[TarotGameStep["CHOOSE_CARDS"] = 3] = "CHOOSE_CARDS";
    /// All the cards are shuffled. We are querying result data before showing a process animation
    TarotGameStep[TarotGameStep["POST_CHOOSE_CARDS"] = 4] = "POST_CHOOSE_CARDS";
    /// All the cards are shuffled. We are showing an animation processing the cards
    TarotGameStep[TarotGameStep["PROCESS_SELECTION"] = 5] = "PROCESS_SELECTION";
    /// All done, we are showing the results
    TarotGameStep[TarotGameStep["SHOW_RESULTS"] = 6] = "SHOW_RESULTS";
})(TarotGameStep || (TarotGameStep = {}));
/// <reference path="../Globals" />
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
/**
 * @brief A helper with miscellaneous methods used in templates.
 */
var TemplateHelper = /** @class */ (function () {
    function TemplateHelper() {
    }
    /**
     * @brief Builds a card item html element and returns it.
     * @param any config Current game configuration.
     * @param number itemIndex The config of the card item that is being generated.
     * @return JQuery The built card item html element.
     */
    TemplateHelper.buildCardItemHtml = function (config, itemIndex) {
        var card = config.card.Card;
        var cardItemConfig = config.cardItems[itemIndex];
        var cardItem = cardItemConfig.CardItem;
        var cardItemLang = cardItemConfig.CardItemLang;
        var el = $('<div class="tarot-card-item big" role="button" tabindex="0"></div>');
        // set css if given
        el.attr('style', card.item_css || '');
        //
        el.css({
            opacity: 0,
            'background-color': card.item_bg_color || 'transparent'
        });
        // inner
        var elCont = $('<div class="tarot-card-item-inner" role="button" tabindex="0"></div>');
        el.append(elCont);
        // back
        var imgBack = $('<img />');
        imgBack.addClass('tarot-card-item-img-back');
        imgBack.attr('src', TemplateHelper.prefixUrl(card.item_bg_image, config.cardImagesUrl));
        imgBack.attr('alt', 'Card Image');
        elCont.append(imgBack);
        // front
        var imgFront = $('<img />');
        imgFront.addClass('tarot-card-item-img-front');
        imgFront.attr('src', TemplateHelper.prefixUrl(cardItem.image, config.cardItemImagesUrl));
        imgFront.attr('alt', cardItemLang.title);
        elCont.append(imgFront);
        //
        TemplateHelper.setCardItemForElement(el, itemIndex);
        return el;
    };
    /**
     * @brief Cancels an animation by calling its callback (nothing is done if the animation was not found).
     * @param number id An id designating the animation to cancel.
     * @return Promise The return value from the animation callback (should be a promise) or a promise that resolves immediately if the animation was not found.
     */
    TemplateHelper.cancelAnimation = function (id) {
        var r = null;
        if (id in TemplateHelper.animationCancelCallbacks) {
            var callback = TemplateHelper.animationCancelCallbacks[id];
            delete TemplateHelper.animationCancelCallbacks[id];
            r = callback();
        }
        if (r) {
            return r;
        }
        else {
            return new Promise(function (resolve, reject) {
                resolve();
            });
        }
    };
    /**
     * @brief Cancels the registered animation on the given \a element (if any) by calling the appropriate callback.
     * @param JQuery element The element to cancel the animation for.
     * @return Promise The return value from the animation callback (should be a promise) or a promise that resolves immediately if the animation was not found.
     */
    TemplateHelper.cancelAnimationOnElement = function (element) {
        var animationId = element.data('_animationId');
        element.removeData('_animationId');
        return TemplateHelper.cancelAnimation(+animationId);
    };
    /**
     * @brief Builds and returns a url css property value.
     * @param string url    The url to prefix and to convert to css url.
     * @param string pref   The prefix to prepend to the url.
     * @return string The url css property value corresponding to given properties.
     */
    TemplateHelper.cssUrl = function (url, pref) {
        if (pref === void 0) { pref = ''; }
        url = this.prefixUrl(url, pref);
        if (!url) {
            return '';
        }
        return 'url(\'' + url + '\')';
    };
    /**
     * @brief Returns a card item that corresponds to the given element from the given array.
     * @param JQuery element The element to get the card item for.
     * @param any[] cardItems An array containing card items.
     * @return any|null The card item that corresponds to the given element or \c null if not found.
     */
    TemplateHelper.getCardItemFromElement = function (element, cardItems) {
        var i = TemplateHelper.getCardItemIndexFromElement(element);
        if (i === null) {
            return null;
        }
        if (typeof cardItems[i] === 'undefined') {
            return null;
        }
        return cardItems[i];
    };
    /**
     * @brief Returns a card item index that corresponds to the given element from the given array.
     * @param JQuery element The element to get the card item for.
     * @return number|null The card item index that corresponds to the given element or \c null if not found.
     */
    TemplateHelper.getCardItemIndexFromElement = function (element) {
        var i = element.data('_card_item_index');
        if (typeof i === 'undefined') {
            return null;
        }
        return +i;
    };
    /**
     * @brief Gets card item size.
     * @param string className The class name to read css for.
     * @return any An object with `width` and `height` keys corresponding to the card size.
     */
    TemplateHelper.getCardItemSize = function (className) {
        if (className === void 0) { className = ''; }
        if (className && className.charAt(0) != '.') {
            className = '.' + className;
        }
        var p = TemplateHelper.isMobile() ? '.tarot-card-mobile ' : '';
        var style = __assign(__assign({}, TemplateHelper.getStyle(p + '.tarot-game .tarot-card-item')), TemplateHelper.getStyle(p + '.tarot-game .tarot-card-item' + className));
        return {
            width: parseFloat(style.width),
            height: parseFloat(style.height)
        };
    };
    /**
     * @brief Gets an element size.
     * @param string className The class name to read css for.
     * @param boolean mobileCheck Should we check for mobile.
     * @return any An object with `width` and `height` keys corresponding to the card size.
     */
    TemplateHelper.getElementSize = function (className, mobileCheck) {
        if (mobileCheck === void 0) { mobileCheck = true; }
        var p = mobileCheck && TemplateHelper.isMobile() ? '.tarot-card-mobile ' : '';
        var style = __assign({}, TemplateHelper.getStyle(p + className));
        return {
            width: parseFloat(style.width),
            height: parseFloat(style.height)
        };
    };
    /**
     * @brief Reads css style for given class.
     * @param string className The class name to read css for.
     * @return any The read style.
     */
    TemplateHelper.getStyle = function (className) {
        if (typeof TemplateHelper.cache.style === 'undefined') {
            TemplateHelper.cache.style = {};
        }
        if (typeof TemplateHelper.cache.style[className] !== 'undefined') {
            return TemplateHelper.cache.style[className];
        }
        var styleSheets = window.document.styleSheets;
        var cssText = '';
        for (var i = 0; i < styleSheets.length; i++) {
            var classes = styleSheets[i].rules || styleSheets[i].cssRules;
            if (!classes) {
                continue;
            }
            for (var x = 0; x < classes.length; x++) {
                if (classes[x].selectorText === className) {
                    cssText += classes[x].cssText || classes[x].style.cssText;
                }
            }
        }
        var r = {};
        var split = cssText.split(/[\{\};]+/);
        for (var i = 0; i < split.length; ++i) {
            var v = split[i].split(':', 2);
            if (v.length !== 2) {
                continue;
            }
            v[0] = v[0].trim();
            v[1] = v[1].trim();
            r[v[0]] = v[1];
        }
        TemplateHelper.cache.style[className] = r;
        return r;
    };
    /**
     * @brief Returns \c true if the user is viewing the page in mobile, \c false otherwise.
     * @return boolean \c true if the user is viewing the page in mobile, \c false otherwise.
     */
    TemplateHelper.isMobile = function () {
        return $('body').width() < 980;
    };
    /**
     * @brief Returns \c true if the user is viewing the page in safari, \c false otherwise.
     * @return boolean \c true if the user is viewing the page in safari, \c false otherwise.
     */
    TemplateHelper.isSafari = function () {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('safari') != -1) {
            if (ua.indexOf('chrome') === -1) {
                return true;
            }
        }
        return false;
    };
    /**
     * @brief Returns a prefixed url based on the given parameters.
     * @param string url The url to prefix.
     * @param string pref The prefix to prepend to the url.
     * @return string The prefixed url.
     */
    TemplateHelper.prefixUrl = function (url, pref) {
        if (pref === void 0) { pref = ''; }
        if (!url) {
            return '';
        }
        return pref + url;
    };
    /**
     * @brief Preload one single image.
     * @param string url The url of the image to preload.
     * @param callable|null loadCallback    The function to call when the image is done preloading.
     * @param callable|null errorCallback   The function to call when there was an error preloading the image.
     */
    TemplateHelper.preloadImage = function (url, loadCallBack, errorCallBack) {
        if (loadCallBack === void 0) { loadCallBack = null; }
        if (errorCallBack === void 0) { errorCallBack = null; }
        var img = new Image();
        if (loadCallBack) {
            img.onload = loadCallBack;
        }
        if (errorCallBack) {
            img.onerror = errorCallBack;
        }
        img.src = url;
    };
    /**
     * @brief Registers the animation cancelation callback and returns the registration id.
     * @param callable callback The animation cancelation callback.
     * @return number The animation registration id.
     */
    TemplateHelper.registerAnimation = function (callback) {
        TemplateHelper.animationCancelCallbacks.length += 1;
        var id = TemplateHelper.animationCancelCallbacks.length;
        TemplateHelper.animationCancelCallbacks[id] = callback;
        return id;
    };
    /**
     * @brief Registers the animation cancelation callback on the given element and returns the registration id.
     * @param JQuery element The element to register the animation for.
     * @param callable callback The animation cancelation callback.
     * @return number The animation registration id.
     */
    TemplateHelper.registerAnimationOnElement = function (element, callback) {
        var id = TemplateHelper.registerAnimation(callback);
        element.data('_animationId', id);
        return id;
    };
    /**
     * @brief Registers the selectable animation on hover for given card elements.
     * @param JQuery elements The elements to register the animation for.
     */
    TemplateHelper.registerSelectableAnimations = function (elements) {
        elements.each(function () {
            var e = $(this);
            var rz_orig = e.get(0).style.transform;
            if (rz_orig) {
                rz_orig = rz_orig.match(/rotateZ\(([0-9\.]+)deg\)/);
                if (rz_orig) {
                    rz_orig = parseFloat(rz_orig[1] || 0);
                }
            }
            if (!rz_orig) {
                rz_orig = 0;
            }
            e.css('transition', 'none');
            var stepFn = function (now, fx) {
                var sc = 1 + 0.2 * now / 100;
                var ty = -8 * now / 100;
                var rz = rz_orig * (100 - now) / 100;
                $(this).css('transform', 'scale(' + sc + ') translateY(' + ty + '%) rotateZ(' + rz + 'deg)');
            };
            e.on('mouseenter.tarot-cards-selectable', function () {
                e.stop().animate({
                    borderSpacing: 100
                }, {
                    step: stepFn,
                    duration: 500,
                });
            });
            e.on('mouseleave.tarot-cards-selectable', function () {
                e.stop().animate({
                    borderSpacing: 0
                }, {
                    step: stepFn,
                    duration: 500,
                });
            });
        });
    };
    /**
     * @brief Registers the callback to be called when the window is resized.
     * @param callable callback The method to call when the window is resized.
     * @param boolean once      Set to true so that the event is only called once.
     * @param number debounce   Minimum delay for the event to be called.
     */
    TemplateHelper.registerWindowResizeEvent = function (callback, once, debounce) {
        if (once === void 0) { once = false; }
        if (debounce === void 0) { debounce = TemplateHelper.ANIMATION_DELAY_DEBOUNCE; }
        var scheduledEvent = null;
        var fn = function () {
            if (scheduledEvent) {
                return;
            }
            scheduledEvent = window.setTimeout(function () {
                scheduledEvent = null;
                callback();
            }, debounce);
        };
        if (once) {
            $(window).one('resize orientationchange', fn);
        }
        else {
            $(window).on('resize orientationchange', fn);
        }
    };
    /**
     * @brief Saves the given \a cardItemIndex in the given \a element to be retrieved with getCardItemFromElement().
     * @param JQuery element The element to get the card item for.
     * @param number cardItemIndex The index to save.
     */
    TemplateHelper.setCardItemForElement = function (element, cardItemIndex) {
        element.data('_card_item_index', cardItemIndex);
    };
    /**
     * @brief Unregisters the animation callback (nothing is done if the animation was not found).
     * @param number id An id designating the animation to unregister.
     */
    TemplateHelper.unregisterAnimation = function (id) {
        if (id in TemplateHelper.animationCancelCallbacks) {
            delete TemplateHelper.animationCancelCallbacks[id];
        }
    };
    /**
     * @brief Unregisters the registered animation on the given \a element (if any).
     * @param JQuery element The element to cancel the animation for.
     */
    TemplateHelper.unregisterAnimationOnElement = function (element) {
        var animationId = element.data('_animationId');
        element.removeData('_animationId');
        if (animationId) {
            TemplateHelper.unregisterAnimation(+animationId);
        }
    };
    /**
     * @brief Unregisters the selectable animation on hover for given card elements.
     * @param JQuery elements The elements to unregister the animation for.
     */
    TemplateHelper.unregisterSelectableAnimations = function (elements) {
        elements.each(function () {
            var e = $(this);
            e.css('transition', '');
            e.off('.tarot-cards-selectable');
        });
    };
    /**
     * @brief Updates an element transition css property to have the given delay.
     * @param JQuery element The element to process.
     * @param string[] properties List of property names to match.
     * @param number|null newDelayMs The new delay in milliseconds.
     * @param string|null newFunction The new transition function.
     * @param number|null index The index of the transition to change (if there are many).
     */
    TemplateHelper.updateTransitionDelay = function (element, properties, newDelayMs, newFunction, index) {
        if (newDelayMs === void 0) { newDelayMs = null; }
        if (newFunction === void 0) { newFunction = null; }
        if (index === void 0) { index = null; }
        var ns = (newDelayMs === null ? 0 : newDelayMs / 1000.0) + 's';
        var oldTransitionProperties = element.css('transition-property');
        var oldTransitionDurations = element.css('transition-duration');
        var oldTransitionFunctions = element.css('transition-timing-function');
        var split = oldTransitionProperties.split(/\s*,\s*/);
        var split2 = oldTransitionDurations.split(/\s*,\s*/);
        var split3 = oldTransitionFunctions.split(/\s*,\s*/);
        if (newDelayMs !== null && split.length !== split2.length) {
            return;
        }
        if (newFunction !== null && split.length !== split3.length) {
            return;
        }
        if (index !== null && index < 0) {
            var maxIndex = 0;
            for (var i = 0; i < split.length; ++i) {
                var v = split[i];
                if (properties === true) {
                    maxIndex++;
                }
                else {
                    for (var j = 0; j < properties.length; j++) {
                        if (v.toLowerCase().indexOf(properties[j].toLowerCase()) !== -1) {
                            maxIndex++;
                            break;
                        }
                    }
                }
            }
            index = maxIndex + index;
        }
        var newTransitionDurations = [];
        var newTransitionFunctions = [];
        var change = false;
        var currentIndex = -1;
        for (var i = 0; i < split.length; ++i) {
            var v = split[i];
            var v2 = split2[i];
            var v3 = split3[i];
            var found = false;
            if (properties === true) {
                found = true;
            }
            else {
                for (var j = 0; j < properties.length; j++) {
                    if (v.toLowerCase().indexOf(properties[j].toLowerCase()) !== -1) {
                        found = true;
                        break;
                    }
                }
            }
            if (found) {
                currentIndex++;
                if (index === null || currentIndex === index) {
                    v2 = ns;
                    v3 = newFunction;
                    change = true;
                }
            }
            newTransitionDurations.push(v2);
            newTransitionFunctions.push(v3);
        }
        if (change) {
            if (newDelayMs !== null) {
                element.css('transition-duration', newTransitionDurations.join(', '));
            }
            if (newFunction !== null) {
                element.css('transition-timing-function', newTransitionFunctions.join(', '));
            }
        }
    };
    /**
     * @brief Waits for the animation to finish on the given element.
     * @param JQuery element The element to wait for.
     * @param number maxWait Maximum delay to wait before failure.
     * @return Promise A promise for when the animation is done on the element.
     */
    TemplateHelper.waitFinishAnimationOnElement = function (element, maxWait) {
        if (maxWait === void 0) { maxWait = 60000; }
        return new Promise(function (resolve, reject) {
            var animationId = element.data('_animationId');
            if (animationId) {
                var delay_1 = 200;
                if (maxWait <= 0) {
                    reject();
                }
                else {
                    window.setTimeout(function () {
                        TemplateHelper.waitFinishAnimationOnElement(element, maxWait - delay_1).then(resolve).catch(reject);
                    }, delay_1);
                }
            }
            else {
                resolve();
            }
        });
    };
    /// Default delay for debouncing
    TemplateHelper.ANIMATION_DELAY_DEBOUNCE = 500;
    /// Default delay for animations
    TemplateHelper.ANIMATION_DELAY_DEFAULT = 500;
    /// Default delay for text animations
    TemplateHelper.ANIMATION_DELAY_TEXT = TemplateHelper.ANIMATION_DELAY_DEFAULT;
    /// Default easing for animations
    TemplateHelper.ANIMATION_EASING_DEFAULT = 'swing';
    /// Default easing for text animations
    TemplateHelper.ANIMATION_EASING_TEXT = TemplateHelper.ANIMATION_EASING_DEFAULT;
    ///
    TemplateHelper.EVENT_GROUP = '.tarot';
    /// callbacks to cancel animations
    TemplateHelper.animationCancelCallbacks = {
        length: 0
    };
    /// used for caching information within this class
    TemplateHelper.cache = {};
    return TemplateHelper;
}());
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Sets the html in a smooth manner.
 * @param JQuery container The container where we are going to set the text.
 * @param string html The html content to set.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the function is done.
 */
function animSetText(container, html, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_TEXT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_TEXT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var oldSpan = container.find('> span').first();
        var oldHtml = (oldSpan.length ? oldSpan.html() : container.html()).trim();
        html = html.trim();
        // check if we have nothing to do
        if (oldSpan.length && oldHtml === html) {
            resolve();
            return;
        }
        // cancel any existing animation and start the new one
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var newSpan = $('<span>' + html + '</span>');
            var oldSpan = $('<span>' + oldHtml + '</span>');
            var oldContainerPosition = container.css('position');
            container.css('position', 'relative');
            newSpan.css('opacity', 0);
            oldSpan.css({
                position: 'absolute',
                top: container.css('padding-top'),
                left: '50%',
                transform: 'translateX(-50%)',
                opacity: 1,
                'text-align': 'center',
                width: container.width() + 'px'
            });
            container.html('');
            container.append(newSpan);
            container.append(oldSpan);
            TemplateHelper.registerAnimationOnElement(container, function () {
                oldSpan.remove();
                newSpan.stop();
                newSpan.css({ opacity: 1 });
                if (!newSpan.html().trim()) {
                    newSpan.remove();
                }
                container.css('position', oldContainerPosition);
                resolve();
            });
            oldSpan.animate({ opacity: 0 }, __assign(__assign({}, options), { duration: delay, easing: easing, queue: false }));
            newSpan.animate({ opacity: 1 }, __assign(__assign({}, options), { duration: delay, easing: easing, queue: false, always: function () {
                    TemplateHelper.cancelAnimationOnElement(container);
                } }));
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Show the cards to be distributed (center them and show them).
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animInitialShowCards(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var stepContainer = container.find('.tarot-game-step');
            var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
            var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var isMobile = TemplateHelper.isMobile();
            var cardItemElements = container.find('.tarot-card-item');
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    el.stop(true, true);
                });
                resolve();
            });
            // center
            var scW = stepContContainer.width();
            var scH = stepContContainer.height();
            var ciW = cardItemElements.first().outerWidth();
            var ciH = cardItemElements.first().outerHeight();
            var ciCenterX = ((scW - ciW) / 2);
            var ciCenterY = ((scH - ciH) / 2) - (isMobile ? 50 : 45);
            //
            cardItemElements.css({
                left: ciCenterX + 'px',
                top: ciCenterY + 'px',
                'z-index': 1,
            });
            // animate to final positions
            var delta = 5;
            var k = -Math.round(cardItemElements.length / 2);
            var remains = cardItemElements.length;
            cardItemElements.each(function (i, el) {
                el = $(el);
                el.stop(true, true);
                el.css({
                    opacity: 1
                });
                el.animate({
                    left: (ciCenterX + k * delta) + 'px',
                    top: (ciCenterY + k * delta) + 'px',
                }, __assign(__assign({}, options), { duration: delay, easing: easing, queue: false, always: function () {
                        remains--;
                        if (!remains) {
                            TemplateHelper.cancelAnimationOnElement(container);
                        }
                    } }));
                k++;
            });
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Distribute the cards on a line.
 * @param TarotGame game The game object.
 * @param any distributeOrderFn The distribution order function to use.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @param any extraTransformations Extra transformations to apply.
 * @param boolean orderFirst Should we order the cards first.
 * @param boolean preserveZindex Whether z-index should be the same for all cards (\c true) or differently for each card (\c false).
 * @return Promise A promise for when the animation is done.
 */
function animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, extraTransformations, yComputeFn, orderFirst, preserveZindex, padding, paddingMobile) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    if (extraTransformations === void 0) { extraTransformations = {}; }
    if (yComputeFn === void 0) { yComputeFn = null; }
    if (orderFirst === void 0) { orderFirst = false; }
    if (preserveZindex === void 0) { preserveZindex = true; }
    if (padding === void 0) { padding = 0; }
    if (paddingMobile === void 0) { paddingMobile = 0; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var stepContainer = container.find('.tarot-game-step');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var cardItemElements = container.find('.tarot-card-item');
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    el.stop(true, true);
                });
                resolve();
            });
            //
            var isMobile = TemplateHelper.isMobile();
            var scW = stepContContainer.width();
            var scH = stepContContainer.height();
            var cS = TemplateHelper.getCardItemSize();
            var ciW = cS.width;
            var ciH = cS.height;
            var ciCenterX = ((scW - ciW) / 2);
            var ciCenterY = ((scH - ciH) / 2);
            //
            cardItemElements.not('.front').css({
                'z-index': 101,
            });
            //
            if (TemplateHelper.isMobile()) {
                padding = paddingMobile;
            }
            var delta = (scW - ciW - padding) / (cardItemElements.length - 1);
            var baseDelay = delay / cardItemElements.length;
            var remains = cardItemElements.length;
            var order = [];
            if (orderFirst) {
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    var o = distributeOrderFn(baseDelay, +i, cardItemElements.length);
                    order.push([i, o]);
                });
                order.sort(function (a, b) { return a[1] - b[1]; });
            }
            var _loop_1 = function (i) {
                var o = orderFirst ? order[i] : [i, distributeOrderFn(baseDelay, +i, cardItemElements.length)];
                var el = $(cardItemElements.get(o[0]));
                // do not distribute front cards
                if (el.hasClass('front')) {
                    remains--;
                    if (!remains) {
                        TemplateHelper.cancelAnimationOnElement(container);
                    }
                    return "continue";
                }
                //
                el.stop(true, true);
                el.delay(o[1]).promise().then(function () {
                    el.removeClass('big');
                    el.removeClass('small');
                    if (extraTransformations && (typeof (extraTransformations) === 'function')) {
                        el.css(extraTransformations(i, cardItemElements.length));
                    }
                    else {
                        el.css(extraTransformations);
                    }
                    var y = isMobile ? 10 : 55;
                    var yNoComputeFn = isMobile ? y : y - 65;
                    el.animate({
                        left: (padding / 2 + i * delta) + 'px',
                        top: (yComputeFn ? yComputeFn(y, i, cardItemElements.length) : yNoComputeFn) + 'px',
                    }, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                            if (!preserveZindex) {
                                el.css({
                                    'z-index': i + 1,
                                });
                            }
                            remains--;
                            if (!remains) {
                                if (preserveZindex) {
                                    cardItemElements.not('.front').css({
                                        'z-index': 1,
                                    });
                                }
                                TemplateHelper.cancelAnimationOnElement(container);
                            }
                        } }));
                });
            };
            for (var i = 0; i < cardItemElements.length; ++i) {
                _loop_1(i);
            }
            ;
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Distribute the cards on a line and twist them.
 * @param TarotGame game The game object.
 * @param any distributeOrderFn The distribution order function to use.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animDistributeCardsSkewedLine(game, distributeOrderFn, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, {
        transform: 'rotateZ(20deg)'
    }, null, false, true, 50, 50);
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Distribute the cards on a line that's broken into two parts.
 * @param TarotGame game The game object.
 * @param any distributeOrderFn The distribution order function to use.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animDistributeCardsTwoLines(game, distributeOrderFn, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    var isMobile = TemplateHelper.isMobile();
    var BASE_Y = isMobile ? 0 : -140;
    var BASE_DELTA_Y = isMobile ? -50 : -100;
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, {}, function (y, i, count) {
        return y + BASE_Y - Math.abs(count / 2 - i) * BASE_DELTA_Y * 2 / count;
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Distribute the cards on a arc
 * @param TarotGame game The game object.
 * @param any distributeOrderFn The distribution order function to use.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animDistributeCardsArc(game, distributeOrderFn, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    var BASE_DEG = 50;
    var BASE_Y0 = -20;
    var BASE_Y = 300 * Math.cos(BASE_DEG);
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, function (i, count) {
        var d = ((i - count / 2) * BASE_DEG / count);
        return {
            transform: 'rotateZ(' + d + 'deg)'
        };
    }, function (y, i, count) {
        var d = ((i - count / 2) * BASE_DEG / count) * Math.PI / 180;
        return y + BASE_Y0 + Math.sin(Math.abs(d * d)) * BASE_Y - (TemplateHelper.isMobile() ? 20 : 0);
    }, false, true, 50, 50);
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Gather and shuffle the cards.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animShuffleCards(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var stepContainer = container.find('.tarot-game-step');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var cardItemElements = container.find('.tarot-card-item');
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    el.stop(true, true);
                });
                resolve();
            });
            //
            cardItemElements.css({
                'z-index': 101,
            });
            //
            var remains = 0;
            var nextAnimation = 0;
            //
            var animations = [];
            var loadNextAnimation = function () {
                remains--;
                if (remains <= 0) {
                    remains = cardItemElements.length;
                    if (nextAnimation === animations.length) {
                        TemplateHelper.cancelAnimationOnElement(container);
                    }
                    else {
                        nextAnimation++;
                        animations[nextAnimation - 1]();
                    }
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    remains = 0;
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var gatherCenterSplittedAnim = function () {
                //
                var padding = 30;
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cS = TemplateHelper.getCardItemSize('big');
                var ciCenterX = ((scW - cS.width) / 2);
                var ciCenterX1 = Math.max(ciCenterX - cS.width + 20, (scW / 2 - cS.width + padding) / 2);
                var ciCenterX2 = Math.min(ciCenterX + cS.width - 20, (3 * scW / 2 - cS.width - padding) / 2);
                var ciCenterY = ((scH - cS.height) / 2) - (TemplateHelper.isMobile() ? 50 : 45);
                var halfCount = Math.ceil(cardItemElements.length / 2);
                var baseDelay = delay / cardItemElements.length;
                var delta = 3.5;
                var deltaDegY = 1;
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    el.stop(true, true);
                    TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                    var dir = (i < halfCount ? -1 : 1);
                    var rotY = (20 + deltaDegY * i / 2);
                    var ci = i;
                    if (i < halfCount) {
                        el.css({
                            transform: 'rotateZ(20deg) rotateY(-' + rotY + 'deg) rotateX(-5deg)'
                        });
                    }
                    else {
                        ci -= halfCount;
                        el.css({
                            transform: 'rotateZ(-20deg) rotateY(' + rotY + 'deg) rotateX(-5deg)'
                        });
                    }
                    el.delay(baseDelay * ci).promise().then(function () {
                        el.addClass('big');
                        el.removeClass('small');
                        el.animate({
                            left: ((i < halfCount ? ciCenterX1 : ciCenterX2) + dir * ci * delta) + 'px',
                            top: ciCenterY + 'px',
                        }, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                                el.css({ 'z-index': ci + 101 });
                                loadNextAnimation();
                            } }));
                    });
                });
            };
            //
            var gatherCenterAnim = function () {
                //
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cS = TemplateHelper.getCardItemSize('big');
                var ciCenterX = ((scW - cS.width) / 2);
                var ciCenterY = ((scH - cS.height) / 2) - (TemplateHelper.isMobile() ? 50 : 45);
                var halfCount = Math.ceil(cardItemElements.length / 2);
                var baseDelay = delay * 3 / cardItemElements.length;
                var delta = 0;
                cardItemElements.each(function (i, el) {
                    el = $(el);
                    el.stop(true, true);
                    TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                    el.delay(baseDelay * (i < halfCount ? i : i - halfCount)).promise().then(function () {
                        el.addClass('big');
                        el.removeClass('small');
                        el.css({
                            transform: '',
                        });
                        el.animate({
                            left: (ciCenterX + (i * delta)) + 'px',
                            top: ciCenterY + 'px',
                        }, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                                el.css({ 'z-index': 101 });
                                loadNextAnimation();
                            } }));
                    });
                });
            };
            //
            for (var i = 0; i < 2; ++i) {
                animations.push(gatherCenterSplittedAnim);
                animations.push(delayAnim(delay / 4));
                animations.push(gatherCenterAnim);
                animations.push(delayAnim(200));
            }
            loadNextAnimation();
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Animation when clicking on a card
 * @param TarotGame game The game object.
 * @param JQuery element The card item element.
 * @param JQuery placeholder The placeholder where to place the element.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animSelectCard(game, element, placeholder, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var stepContainer = container.find('.tarot-game-step');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                element.stop(true, true);
                resolve();
            });
            //
            element.css({
                'z-index': 101,
            });
            //
            var animations = [];
            var nextAnimation = 0;
            var loadNextAnimation = function () {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                }
                else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var displaySelectedCardAnim = function () {
                //
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cS = TemplateHelper.getCardItemSize('big');
                var ciW = cS.width;
                var ciH = cS.height;
                var ciCenterX = ((scW - ciW) / 2);
                var ciCenterY = ((element.offset().top - ciH) / 2);
                //
                element.stop(true, true);
                element.addClass('big');
                element.removeClass('small');
                element.css({
                    'transform': 'rotateZ(0deg)'
                });
                element.animate({
                    left: ciCenterX + 'px',
                    top: ciCenterY + 'px',
                }, __assign(__assign({}, options), { duration: delay, easing: easing, always: loadNextAnimation }));
            };
            //
            var flipSelectedCardAnim = function () {
                element.stop(true, true);
                element.addClass('front');
                element.addClass('highlight');
                loadNextAnimation();
            };
            //
            var sendToPlaceholderCardAnim = function () {
                var pOff = placeholder.offset();
                element.stop(true, true);
                element.removeClass('big');
                element.addClass('small');
                element.animate({
                    left: placeholder.css('left'),
                    top: placeholder.css('top'),
                }, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                        element.removeClass('highlight');
                        element.css({
                            'z-index': Math.min(99, 1 + (placeholder.siblings('.selected').length)),
                        });
                        placeholder.hide();
                        loadNextAnimation();
                    } }));
            };
            //
            animations.push(displaySelectedCardAnim);
            animations.push(flipSelectedCardAnim);
            animations.push(delayAnim(2000));
            animations.push(sendToPlaceholderCardAnim);
            loadNextAnimation();
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Animate process for "single" game type.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animProcessSingle(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT * 2; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var config = game.getConfig();
            var card = config.card.Card;
            var cardLang = config.card.CardLang;
            var cardItems = config.cardItems;
            var stepContainer = container.find('.tarot-game-step');
            var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var selectedCards = game.getSelectedCardItemIds();
            var cardItemElements = [];
            var FINAL_CARD_SIZE = 'wheelsize';
            // clear the cards container and place elements
            stepContContainer.html('');
            var wheel = $('<div class="tarot-game-interpretation-round1"></div>');
            stepContContainer.append(wheel);
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });
            // add cards with 0 opacity
            for (var i = 0; i < selectedCards.length; ++i) {
                var el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
                el.css({
                    opacity: 0,
                    'z-index': 101,
                });
                el.addClass('front');
                el.addClass('big');
                wheel.append(el);
                cardItemElements.push(el);
            }
            // function to compute position of a card on a circle
            var computeCardPosition = function (index) {
                var cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                var centerX = wheel.width() / 2;
                var centerY = wheel.height() / 2;
                var radius = wheel.width() / 2 - (TemplateHelper.isMobile() ? 10 : 50);
                var count = cardItemElements.length;
                var theta = -Math.PI * 2 * index / count;
                var x0 = Math.sin(theta) * radius - cs.width / 2;
                var y0 = -Math.cos(theta) * radius - cs.height / 2;
                var actualRotation = theta * 180 / Math.PI;
                if (actualRotation) {
                    while (actualRotation > -150) {
                        actualRotation -= 360;
                    }
                }
                return {
                    transform: 'rotateZ(' + actualRotation + 'deg)',
                    left: (centerX + x0) + 'px',
                    top: (centerY + y0) + 'px',
                };
            };
            // reposition cards
            var repositionCards = function (force) {
                if (force === void 0) { force = false; }
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                var scW = wheel.width();
                var scH = wheel.height();
                var cbS = TemplateHelper.getCardItemSize('big');
                var ciCenterX = (scW - cbS.width) / 2;
                var ciCenterY = (scH - cbS.height) / 2;
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = cardItemElements[i];
                    if (el.hasClass('big')) {
                        el.css({
                            left: ciCenterX + 'px',
                            top: ciCenterY + 'px',
                        });
                    }
                    else {
                        el.css(computeCardPosition(i));
                    }
                }
            };
            repositionCards(true);
            TemplateHelper.registerWindowResizeEvent(repositionCards, false, 0);
            // animations
            var nextCardIndex = 0;
            var nextAnimation = 0;
            var animations = [];
            var loadNextAnimation = function () {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                }
                else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var showCardAnim = function () {
                var cardItemLang = cardItems[selectedCards[nextCardIndex]].CardItemLang;
                var keyword = game.getResult().selected_keywords[nextCardIndex] || cardItemLang.title;
                animSetText(stepDescContainer, cardLang.step_interpretation_description.replace('##title##', cardItemLang.title).replace('##keyword##', keyword));
                var el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['opacity'], delay);
                el.css({
                    opacity: 1
                });
                window.setTimeout(loadNextAnimation, delay);
            };
            //
            var positionCardAnim = function () {
                var el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                var css = computeCardPosition(nextCardIndex);
                var anim = { left: css.left, top: css.top };
                delete css.left;
                delete css.top;
                el.css(css);
                el.delay(delay * 0.2).promise().then(function () {
                    el.removeClass('big');
                    el.addClass(FINAL_CARD_SIZE);
                    el.animate(anim, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                            el.css({ 'z-index': 1 });
                            loadNextAnimation();
                        } }));
                });
            };
            //
            var rotateWheelAnim = function () {
                animSetText(stepDescContainer, game.getResult().tr.wait_please);
                var el = wheel;
                var delay = 6000;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                el.css({
                    transform: 'rotateZ(-360deg)'
                });
                window.setTimeout(loadNextAnimation, delay);
            };
            //
            var nextCardFn = function () {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };
            //
            for (var i = 0; i < cardItemElements.length; ++i) {
                animations.push(showCardAnim);
                animations.push(delayAnim(delay / 4));
                animations.push(positionCardAnim);
                animations.push(delayAnim(200));
                animations.push(nextCardFn);
            }
            animations.push(rotateWheelAnim);
            loadNextAnimation();
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Animate process for "fortune" game type.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animProcessFortune(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT * 2; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var config = game.getConfig();
            var card = config.card.Card;
            var cardLang = config.card.CardLang;
            var cardItems = config.cardItems;
            var stepContainer = container.find('.tarot-game-step');
            var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var selectedCards = game.getSelectedCardItemIds();
            var cardItemElements = [];
            var FINAL_CARD_SIZE = 'wheelsize2';
            // clear the cards container and place elements
            stepContContainer.html('');
            var wheel = $('<div class="tarot-game-interpretation-round2"></div>');
            stepContContainer.append(wheel);
            animSetText(stepDescContainer, game.getResult().tr.wait_please);
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });
            // function to compute position of a card on a circle
            var computeCardPosition = function (index, cs, radius, centerOnElement, dx, dy) {
                if (centerOnElement === void 0) { centerOnElement = null; }
                if (dx === void 0) { dx = 0; }
                if (dy === void 0) { dy = 0; }
                if (centerOnElement === null) {
                    centerOnElement = wheel;
                }
                var centerX = centerOnElement.width() / 2;
                var centerY = centerOnElement.height() / 2;
                var count = selectedCards.length;
                var theta = -Math.PI * 2 * index / count;
                var x0 = Math.sin(theta) * radius - cs.width / 2;
                var y0 = -Math.cos(theta) * radius - cs.height / 2;
                var actualRotation = theta * 180 / Math.PI;
                if (actualRotation) {
                    while (actualRotation > -150) {
                        actualRotation -= 360;
                    }
                }
                return {
                    transform: 'rotateZ(' + actualRotation + 'deg)',
                    left: (dx + centerX + x0) + 'px',
                    top: (dy + centerY + y0) + 'px',
                };
            };
            // add wheel cards
            var wheelCards = [];
            for (var i = 0; i < selectedCards.length; ++i) {
                var el = $('<div class="tarot-game-interpretation-round2-card"></div>');
                var cs = TemplateHelper.getElementSize('.tarot-game-interpretation-round2-card');
                var css = computeCardPosition(i, cs, cs.height / 2);
                el.css(css);
                wheel.append(el);
                wheelCards.push(el);
            }
            // add cards with 0 opacity
            for (var i = 0; i < selectedCards.length; ++i) {
                var el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
                el.addClass(FINAL_CARD_SIZE);
                el.addClass('front');
                el.css({
                    opacity: 0,
                    'z-index': 1,
                });
                stepContContainer.append(el);
                cardItemElements.push(el);
            }
            // reposition cards
            var repositionCards = function (force) {
                if (force === void 0) { force = false; }
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                for (var i = 0; i < wheelCards.length; ++i) {
                    var el = wheelCards[i];
                    var cs = TemplateHelper.getElementSize('.tarot-game-interpretation-round2-card');
                    var css = computeCardPosition(i, cs, cs.height / 2);
                    el.css(css);
                }
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = cardItemElements[i];
                    var cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                    var css = computeCardPosition(i, cs, wheel.width() / 2 + (TemplateHelper.isMobile() ? 0 : 10), stepContContainer);
                    delete css['transform'];
                    el.css(css);
                    el.css(css);
                }
            };
            repositionCards(true);
            TemplateHelper.registerWindowResizeEvent(repositionCards, false, 0);
            // animations
            var nextCardIndex = 0;
            var destinationIndex = 0;
            var nextAnimation = 0;
            var doneIndexes = [];
            var animations = [];
            var loadNextAnimation = function () {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                }
                else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var positionCardAnim = function () {
                var el = wheelCards[wheelCards.length - 1 - nextCardIndex];
                var cel = cardItemElements[destinationIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                el.css({
                    'transform': 'rotateZ(360deg)'
                });
                var cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                var css = computeCardPosition(destinationIndex, cs, wheel.width() / 2 + (TemplateHelper.isMobile() ? 0 : 10), null, 0, 18);
                var anim = { left: css.left, top: css.top };
                delete css.left;
                delete css.top;
                el.animate(anim, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                        cel.css({
                            'opacity': 1
                        });
                        el.css({
                            'opacity': 0,
                        });
                        el.css({ 'z-index': 1 });
                        loadNextAnimation();
                    } }));
            };
            //
            var rotateWheelAnim = function () {
                var el = wheel;
                var delay = 6000;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                el.css({
                    transform: 'rotateZ(-720deg)'
                });
                window.setTimeout(loadNextAnimation, delay);
            };
            //
            var nextCardFn = function () {
                doneIndexes.push(destinationIndex);
                nextCardIndex++;
                var init = destinationIndex;
                destinationIndex = init;
                var count = wheelCards.length;
                while (doneIndexes.indexOf(destinationIndex) !== -1) {
                    destinationIndex = destinationIndex + count;
                    if (destinationIndex >= wheelCards.length) {
                        if (count === 1) {
                            destinationIndex = init;
                            break;
                        }
                        count = Math.floor(count / 2);
                        destinationIndex = (init + count) % wheelCards.length;
                    }
                }
                window.setTimeout(loadNextAnimation);
            };
            //
            for (var i = 0; i < cardItemElements.length; ++i) {
                animations.push(positionCardAnim);
                animations.push(delayAnim(200));
                animations.push(nextCardFn);
            }
            animations.push(rotateWheelAnim);
            loadNextAnimation();
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Animate process for "love" game type.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animProcessLove(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var config = game.getConfig();
            var card = config.card.Card;
            var cardLang = config.card.CardLang;
            var cardItems = config.cardItems;
            var stepContainer = container.find('.tarot-game-step');
            var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var selectedCards = game.getSelectedCardItemIds();
            var cardItemElements = [];
            var FINAL_CARD_SIZE = '';
            // clear the cards container and place elements
            stepContContainer.html('');
            var flexcont = $('<div class="tarot-game-interpretation-flexcont"></div>');
            stepContContainer.append(flexcont);
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });
            // add cards
            for (var i = 0; i < selectedCards.length; ++i) {
                var el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
                el.css({
                    opacity: 1,
                    'z-index': 101,
                });
                el.removeClass('big');
                flexcont.append(el);
                cardItemElements.push(el);
            }
            // animations
            var nextCardIndex = 0;
            var nextAnimation = 0;
            var animations = [];
            var loadNextAnimation = function () {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                }
                else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var flipCardAnim = function () {
                var el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                el.toggleClass('front');
                window.setTimeout(loadNextAnimation, 0);
            };
            //
            var flipAllCardsAnim = function () {
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = cardItemElements[i];
                    el.stop(true, true);
                    el.toggleClass('front');
                }
                window.setTimeout(loadNextAnimation, 0);
            };
            //
            var resetNextCardFn = function () {
                nextCardIndex = 0;
                window.setTimeout(loadNextAnimation);
            };
            //
            var nextCardFn = function () {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };
            //
            for (var j = 0; j < 2; ++j) {
                animations.push(resetNextCardFn);
                for (var i = 0; i < cardItemElements.length; ++i) {
                    animations.push(flipCardAnim);
                    animations.push(delayAnim(delay));
                    animations.push(flipCardAnim);
                    animations.push(nextCardFn);
                }
                animations.push(flipAllCardsAnim);
                animations.push(delayAnim(delay));
                animations.push(flipAllCardsAnim);
                animations.push(delayAnim(delay));
            }
            loadNextAnimation();
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Animate process for "yes no" game type.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animProcessYesNo(game, delay, easing, options) {
    if (delay === void 0) { delay = TemplateHelper.ANIMATION_DELAY_DEFAULT * 2; }
    if (easing === void 0) { easing = TemplateHelper.ANIMATION_EASING_DEFAULT; }
    if (options === void 0) { options = {}; }
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(function () {
            var result = game.getResult();
            var config = game.getConfig();
            var card = config.card.Card;
            var cardLang = config.card.CardLang;
            var cardItems = config.cardItems;
            var stepContainer = container.find('.tarot-game-step');
            var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
            var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
            var stepContContainer = stepContainer.find('.tarot-game-step-cont');
            var selectedCards = game.getSelectedCardItemIds();
            var cardItemElements = [];
            var FINAL_CARD_SIZE = 'small';
            //
            var countYes = 0;
            var countNo = 0;
            for (var i = 0; i < result.card_item_answer.length; ++i) {
                var r = result.card_item_answer[i];
                if (r) {
                    countYes++;
                }
                else {
                    countNo++;
                }
            }
            // clear the cards container and place elements
            stepContContainer.html('');
            //
            var yesCont = $('<div class="tarot-game-interpretation-yes-text">' + result.tr.yes + '</div>');
            stepContContainer.append(yesCont);
            var noCont = $('<div class="tarot-game-interpretation-no-text">' + result.tr.no + '</div>');
            stepContContainer.append(noCont);
            var scale = $('<div class="tarot-game-interpretation-scale"></div>');
            stepContContainer.append(scale);
            var scaleBar = $('<div class="tarot-game-interpretation-scale-bar"></div>');
            scale.append(scaleBar);
            var scaleBase = $('<div class="tarot-game-interpretation-scale-base"></div>');
            scale.append(scaleBase);
            var scaleInd = $('<div class="tarot-game-interpretation-scale-ind"></div>');
            scale.append(scaleInd);
            var scaleContL = $('<div class="tarot-game-interpretation-scale-contl"></div>');
            scale.append(scaleContL);
            var scaleContR = $('<div class="tarot-game-interpretation-scale-contr"></div>');
            scale.append(scaleContR);
            // function to rotate the scale (p in range -1 .. 1)
            var scaleContTopAtO = parseInt(scaleContL.css('top'));
            var setScaleValue = function (p) {
                var MAX_ROT_IND = 45;
                scaleInd.css({ transform: 'rotateZ(' + (-p * MAX_ROT_IND) + 'deg)' });
                var MAX_ROT = 30;
                scaleBar.css({ transform: 'rotateZ(' + (p * MAX_ROT) + 'deg)' });
                var radius = TemplateHelper.isMobile() ? (192 * 0.6) : 192;
                var alpha = p * MAX_ROT * Math.PI / 180;
                var y = Math.sin(alpha) * radius;
                var x = (Math.cos(alpha) - 1) * radius;
                scaleContL.css({
                    top: (scaleContTopAtO - y) + 'px',
                    transform: 'translateX(' + (-x) + 'px)'
                });
                scaleContR.css({
                    top: (scaleContTopAtO + y) + 'px',
                    transform: 'translateX(' + x + 'px)'
                });
            };
            // computes card position on scale
            var computePositionOnScale = function (scale, cardEl, cardIndex, cardsCount) {
                var cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                var top = scale.offset().top + scale.height() - scale.parent().parent().offset().top - cs.height;
                var left = scale.offset().left + scale.width() - scale.parent().parent().offset().left - cs.width;
                return {
                    top: top + 'px',
                    left: left + 'px',
                };
            };
            //
            TemplateHelper.registerAnimationOnElement(container, function () {
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });
            // add cards on a line
            for (var i = 0; i < selectedCards.length; ++i) {
                var el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
                el.css({
                    opacity: 1,
                    'z-index': 1,
                });
                el.addClass('front');
                el.removeClass('big');
                el.addClass(FINAL_CARD_SIZE);
                stepContContainer.append(el);
                cardItemElements.push(el);
            }
            // reposition cards
            var repositionCards = function (force) {
                if (force === void 0) { force = false; }
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cbS = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                var padW = Math.min(20, scW / cardItemElements.length - cbS.width);
                var actualW = cbS.width + padW;
                var ciCenterX = (scW - actualW * cardItemElements.length + padW) / 2;
                var ciCenterY = 20;
                for (var i = 0; i < cardItemElements.length; ++i) {
                    var el = cardItemElements[i];
                    var centerOnScale = null;
                    if (!el.hasClass('big')) {
                        el.css({
                            left: ciCenterX + i * actualW + 'px',
                            top: ciCenterY + 'px',
                        });
                    }
                }
            };
            repositionCards(true);
            TemplateHelper.registerWindowResizeEvent(repositionCards, false, 0);
            // animations
            var nextCardIndex = 0;
            var nextAnimation = 0;
            var animations = [];
            var loadNextAnimation = function () {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                }
                else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };
            //
            var delayAnim = function (delay) {
                return function () {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };
            //
            var showCardAnim = function () {
                var showDelay = delay;
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cbS = TemplateHelper.getCardItemSize('big');
                var ciCenterX = (scW - cbS.width) / 2;
                var ciCenterY = (scH - cbS.height) / 2;
                var element = cardItemElements[nextCardIndex];
                element.stop(true, true);
                element.addClass('big');
                element.removeClass('small');
                element.css({
                    'transform': 'rotateZ(0deg)',
                    'z-index': 101
                });
                element.animate({
                    left: ciCenterX + 'px',
                    top: ciCenterY + 'px',
                }, __assign(__assign({}, options), { duration: showDelay, easing: easing, always: loadNextAnimation }));
            };
            //
            var countPlacedYes = 0;
            var countPlacedNo = 0;
            var positionCardAnim = function () {
                var elAnswer = !!result.card_item_answer[nextCardIndex];
                var el = cardItemElements[nextCardIndex];
                var scaleCont = elAnswer ? scaleContR : scaleContL;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                var css = computePositionOnScale(scaleCont, el, elAnswer ? countPlacedYes : countPlacedNo, elAnswer ? countYes : countNo);
                var anim = { left: css.left, top: css.top };
                delete css.left;
                delete css.top;
                el.css(css);
                el.delay(delay * 0.2).promise().then(function () {
                    el.removeClass('big');
                    el.addClass(FINAL_CARD_SIZE);
                    el.animate(anim, __assign(__assign({}, options), { duration: delay, easing: easing, always: function () {
                            el.css({ 'z-index': 1 });
                            el.remove();
                            scaleCont.append(el);
                            if (elAnswer) {
                                countPlacedYes++;
                            }
                            else {
                                countPlacedNo++;
                            }
                            loadNextAnimation();
                        } }));
                });
            };
            //
            var rotateScaleAnim = function () {
                TemplateHelper.updateTransitionDelay(scaleInd, ['transform'], delay);
                TemplateHelper.updateTransitionDelay(scaleBar, ['transform'], delay);
                TemplateHelper.updateTransitionDelay(scaleContL, ['transform', 'top'], delay);
                TemplateHelper.updateTransitionDelay(scaleContR, ['transform', 'top'], delay);
                setScaleValue((countPlacedYes - countPlacedNo) / (countYes + countNo));
                window.setTimeout(loadNextAnimation, delay);
            };
            //
            var nextCardFn = function () {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };
            //
            var showResult = function () {
                var showDelay = 2000;
                var scW = stepContContainer.width();
                var scH = stepContContainer.height();
                var cbS = TemplateHelper.getElementSize('.tarot-game-interpretation-yes-text.big');
                var ciCenterX = (scW - cbS.width) / 2;
                var ciCenterY = (scH - cbS.height) / 2;
                animSetText(stepTitleContainer, result.tr.answer_is);
                var element = countYes > countNo ? yesCont : noCont;
                TemplateHelper.updateTransitionDelay(element, true, showDelay);
                element.stop(true, true);
                element.addClass('big');
                element.css({
                    'z-index': 101
                });
                window.setTimeout(loadNextAnimation, showDelay + 3000);
            };
            //
            animations.push(delayAnim(delay));
            for (var i = 0; i < cardItemElements.length; ++i) {
                animations.push(showCardAnim);
                animations.push(delayAnim(delay));
                animations.push(positionCardAnim);
                animations.push(delayAnim(200));
                animations.push(rotateScaleAnim);
                animations.push(nextCardFn);
            }
            animations.push(delayAnim(delay));
            animations.push(showResult);
            loadNextAnimation();
        });
    });
}
/// <reference path="./animSetText" />
/// <reference path="./animInitialShowCards" />
/// <reference path="./animDistributeCardsLine" />
/// <reference path="./animDistributeCardsSkewedLine" />
/// <reference path="./animDistributeCardsTwoLines" />
/// <reference path="./animDistributeCardsArc" />
/// <reference path="./animShuffleCards" />
/// <reference path="./animSelectCard" />
/// <reference path="./animProcessSingle" />
/// <reference path="./animProcessFortune" />
/// <reference path="./animProcessLove" />
/// <reference path="./animProcessYesNo" />
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Does main initializations to the initial step.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function initialStep(game) {
    return new Promise(function (resolve, reject) {
        var config = game.getConfig();
        var container = game.getContainer();
        var card = config.card.Card;
        var cardLang = config.card.CardLang;
        var stepContainer = container.find('.tarot-game-step');
        var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
        var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-choose');
        // load main css
        if (card.main_css) {
            container.prepend('<style>' + card.main_css + '</style>');
        }
        // load background
        var loadBackgroundFn = function (force) {
            if (force === void 0) { force = false; }
            var isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            }
            else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS || force) {
                var step = 'choose';
                container.css({
                    'background-color': card['step_' + step + '_bg_color'] || 'transparent',
                    'background-image': TemplateHelper.cssUrl(card[isMobile ? 'step_' + step + '_mobile_bg_image' : 'step_' + step + '_bg_image'], config.cardImagesUrl) || 'unset',
                });
            }
        };
        loadBackgroundFn(true);
        // ensure the background image is updated when going to mobile
        TemplateHelper.registerWindowResizeEvent(loadBackgroundFn, false, 0);
        // ensure we have the correct title and description there
        animSetText(stepTitleContainer, cardLang.step_choose_title);
        animSetText(stepDescContainer, cardLang.step_choose_description);
        // clear the cards container and init the cards
        stepContContainer.html('');
        for (var i = 0; i < config.cardItems.length; ++i) {
            stepContContainer.append(TemplateHelper.buildCardItemHtml(config, i));
        }
        //
        animInitialShowCards(game).then(resolve);
    });
}
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Show the cards and get ready to shuffle them.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function showCardsStep(game) {
    return new Promise(function (resolve, reject) {
        var config = game.getConfig();
        var container = game.getContainer();
        var cardLang = config.card.CardLang;
        var stepContainer = container.find('.tarot-game-step');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        //
        var distributionFn = game.getCardDistributionAnimationFn();
        //
        var shuffleBtnContainer = $('<div class="tarot-shuffle-btn-cont"></div>');
        var shuffleBtn = $('<div class="tarot-btn"></div>');
        shuffleBtn.text(config.tr.shuffle_btn_text);
        shuffleBtn.css('opacity', 0);
        shuffleBtnContainer.append(shuffleBtn);
        stepContContainer.find('.tarot-shuff-btn-cont').remove();
        stepContContainer.append(shuffleBtnContainer);
        // ensure the cards are replaced if the window is resized
        var onResizeFn = function () {
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS) {
                TemplateHelper.waitFinishAnimationOnElement(game.getContainer()).then(function () {
                    distributionFn.animationFn(game, distributionFn.orderFn, 0);
                });
            }
        };
        TemplateHelper.registerWindowResizeEvent(onResizeFn, false, 0);
        //
        distributionFn.animationFn(game, distributionFn.orderFn).then(function () {
            window.setTimeout(function () {
                shuffleBtn.css('opacity', 1);
                resolve();
            });
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Shuffle the cards after clicking on the shuffle button.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function shuffleCardsStep(game) {
    return new Promise(function (resolve, reject) {
        var container = game.getContainer();
        var stepContainer = container.find('.tarot-game-step');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        var cardItemElements = container.find('.tarot-card-item');
        var shuffleBtnCont = container.find('.tarot-shuffle-btn-cont');
        var shuffleBtn = shuffleBtnCont.find('.tarot-btn');
        // ensure the shuffle button is rightly placed
        var onResizeFn = function () {
            if (game.getStep() === TarotGameStep.READY_TO_SHUFFLE) {
                var cbS = TemplateHelper.getCardItemSize('');
                var scH = stepContContainer.height();
                var bH = shuffleBtnCont.height();
                var padY = 20;
                var initialY = parseInt(cardItemElements.not('.tarot-card-placeholder').not('.big').first().css('top') || '60') + cbS.height + padY;
                var piY = Math.min(initialY + 50, (scH - padY + initialY - bH) / 2);
                shuffleBtnCont.css('top', piY + 'px');
            }
        };
        TemplateHelper.registerWindowResizeEvent(onResizeFn, false, 0);
        window.setTimeout(onResizeFn);
        //
        var realShuffleCards = function () {
            var _a;
            var a = [];
            // get card info
            cardItemElements.each(function (k, el) {
                el = $(el);
                var imgFront = el.find('img.tarot-card-item-img-front').first();
                a.push({
                    id: TemplateHelper.getCardItemIndexFromElement(el),
                    img: imgFront.attr('src'),
                    imgAlt: imgFront.attr('alt')
                });
            });
            // shuffle the array
            for (var i_1 = a.length - 1; i_1 > 0; i_1--) {
                var j = Math.floor(Math.random() * (i_1 + 1));
                _a = [a[j], a[i_1]], a[i_1] = _a[0], a[j] = _a[1];
            }
            // apply the array
            var i = 0;
            cardItemElements.each(function (k, el) {
                el = $(el);
                var imgFront = el.find('img.tarot-card-item-img-front').first();
                TemplateHelper.setCardItemForElement(el, a[i].id);
                imgFront.attr('src', a[i].img);
                imgFront.attr('alt', a[i].imgAlt);
                i++;
            });
        };
        //
        var shuffling = false;
        var shuffleFn = function () {
            if (shuffling) {
                return;
            }
            shuffling = true;
            shuffleBtn.css('opacity', 0);
            animShuffleCards(game).then(function () {
                shuffleBtnCont.remove();
                cardItemElements.off(TemplateHelper.EVENT_GROUP);
                // actual shuffling
                realShuffleCards();
                // redistribute
                var distributionFn = game.getCardDistributionAnimationFn();
                distributionFn.animationFn(game, distributionFn.orderFn).then(resolve);
            });
        };
        shuffleBtn.off(TemplateHelper.EVENT_GROUP).one('click' + TemplateHelper.EVENT_GROUP, shuffleFn);
        cardItemElements.off(TemplateHelper.EVENT_GROUP).one('click' + TemplateHelper.EVENT_GROUP, shuffleFn);
    });
}
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Lets the user choose the cards.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function chooseCardsStep(game) {
    return new Promise(function (resolve, reject) {
        var config = game.getConfig();
        var card = config.card.Card;
        var cardLang = config.card.CardLang;
        var cardItems = config.cardItems;
        var piCount = +card.count_to_pick;
        var container = game.getContainer();
        var stepContainer = container.find('.tarot-game-step');
        var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
        var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        var cardItemElements = container.find('.tarot-card-item');
        //
        var choose_lines = cardLang.step_choose_lines.split(/\n+/);
        for (var i = 0; i < choose_lines.length; ++i) {
            choose_lines[i] = choose_lines[i].trim();
        }
        if (!choose_lines.length) {
            choose_lines.push(cardLang.step_choose_description);
        }
        stepDescContainer.css('min-height', stepDescContainer.height() + 'px');
        animSetText(stepDescContainer, choose_lines[0]);
        //
        var initPlaceholders = function () {
            for (var i = 0; i < piCount; ++i) {
                var el = $('<div class="tarot-card-placeholder"></div>');
                var img = $('<img />');
                img.attr('src', TemplateHelper.prefixUrl(card.item_disabled_bg_image, config.cardImagesUrl));
                img.attr('alt', 'Card placeholder');
                el.append(img);
                stepContContainer.append(el);
            }
        };
        initPlaceholders();
        //
        var placeholders = stepContContainer.find('.tarot-card-placeholder');
        var repositionPlaceholdersAndSelected = function () {
            var isMobile = TemplateHelper.isMobile();
            var cbS = TemplateHelper.getCardItemSize('');
            var scW = stepContContainer.width();
            var scH = stepContContainer.height();
            var pS = TemplateHelper.getElementSize('.tarot-game .tarot-card-placeholder');
            var piW = pS.width;
            var piH = pS.height;
            var piWAll = piW * piCount;
            var piPadX = Math.min(20, piCount === 1 ? 0 : (scW - piWAll) / (piCount - 1));
            var piX = ((scW - piWAll - piPadX * (piCount - 1)) / 2);
            var piDelta = piW + piPadX;
            var padY = 20;
            var initialY = parseInt(cardItemElements.not('.tarot-card-placeholder').not('.big').first().css('top') || '60') + cbS.height + padY;
            var piY = isMobile ?
                initialY :
                (scH - padY + initialY - piH) / 2;
            placeholders.each(function (i, el) {
                el = $(el);
                var off = {
                    left: piX + i * piDelta + 'px',
                    top: piY + 'px'
                };
                el.css(off);
            });
            var selected = container.find('.tarot-card-item.selected');
            selected.each(function (o, el) {
                el = $(el);
                var card_item_id = TemplateHelper.getCardItemIndexFromElement(el);
                if (card_item_id === null) {
                    return;
                }
                var i = game.getSelectedCardItemIds().indexOf(+card_item_id);
                if (i === -1) {
                    return;
                }
                var off = {
                    left: piX + i * piDelta + 'px',
                    top: piY + 'px'
                };
                el.css(off);
            });
        };
        window.setTimeout(repositionPlaceholdersAndSelected);
        // ensure the placeholders and cards are replaced if the window is resized
        TemplateHelper.registerWindowResizeEvent(function () {
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS) {
                TemplateHelper.waitFinishAnimationOnElement(game.getContainer()).then(function () {
                    repositionPlaceholdersAndSelected();
                });
            }
        }, false, 0);
        // Handle card clicks
        var isSafari = TemplateHelper.isSafari();
        if (isSafari) {
            TemplateHelper.registerSelectableAnimations(cardItemElements);
        }
        else {
            stepContContainer.addClass('selectable-items');
        }
        var disableClick = false;
        cardItemElements.off(TemplateHelper.EVENT_GROUP).on('click' + TemplateHelper.EVENT_GROUP, function () {
            var element = $(this);
            var tmp = TemplateHelper.getCardItemIndexFromElement(element);
            if (tmp === null || element.hasClass('front')) {
                return;
            }
            var cardItemIndex = +tmp;
            var placeholder = $(placeholders.get(game.getSelectedCardItemIds().length));
            if (disableClick) {
                return;
            }
            disableClick = true;
            if (isSafari) {
                TemplateHelper.unregisterSelectableAnimations(cardItemElements);
            }
            else {
                stepContContainer.removeClass('selectable-items');
            }
            animSelectCard(game, element, placeholder).then(function () {
                element.addClass('selected');
                game.addSelectedCardItemId(cardItemIndex);
                if (game.getSelectedCardItemIds().length === piCount) {
                    cardItemElements.off(TemplateHelper.EVENT_GROUP);
                    resolve();
                }
                else {
                    disableClick = false;
                    if (isSafari) {
                        TemplateHelper.registerSelectableAnimations(cardItemElements);
                    }
                    else {
                        stepContContainer.addClass('selectable-items');
                    }
                    if (game.getSelectedCardItemIds().length < choose_lines.length) {
                        animSetText(stepDescContainer, choose_lines[game.getSelectedCardItemIds().length]);
                    }
                }
            });
        });
    });
}
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Shows the processing step.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function processStep(game) {
    return new Promise(function (resolve, reject) {
        var config = game.getConfig();
        var container = game.getContainer();
        var card = config.card.Card;
        var cardLang = config.card.CardLang;
        var cardItems = config.cardItems;
        container.siblings('.tarot-game-main-desc').slideUp();
        var stepContainer = container.find('.tarot-game-step');
        var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
        var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-interpretation');
        // load background
        var loadBackgroundFn = function (force) {
            if (force === void 0) { force = false; }
            var isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            }
            else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() === TarotGameStep.PROCESS_SELECTION || force) {
                var step = 'interpretation';
                container.css({
                    'background-color': card['step_' + step + '_bg_color'] || 'transparent',
                    'background-image': TemplateHelper.cssUrl(card[isMobile ? 'step_' + step + '_mobile_bg_image' : 'step_' + step + '_bg_image'], config.cardImagesUrl) ||
                        TemplateHelper.cssUrl(card[isMobile ? 'step_choose_mobile_bg_image' : 'step_choose_bg_image'], config.cardImagesUrl) ||
                        'unset',
                });
            }
        };
        loadBackgroundFn(true);
        // ensure the background image is updated when going to mobile
        TemplateHelper.registerWindowResizeEvent(loadBackgroundFn, false, 0);
        // ensure we have the correct title and description there
        animSetText(stepTitleContainer, cardLang.step_interpretation_title);
        animSetText(stepDescContainer, '');
        //
        var animFn = animProcessSingle;
        var distributeOrderFn = DistributeOrder.outFirstDistribution;
        var gameType = +config.card.Card.game_type;
        if (gameType === GameType.YES_NO) {
            animFn = animProcessYesNo;
        }
        else if (gameType === GameType.SINGLE) {
            animFn = animProcessSingle;
        }
        else if (gameType === GameType.FORTUNE) {
            animFn = animProcessFortune;
        }
        else if (gameType === GameType.LOVE) {
            animFn = animProcessLove;
        }
        //
        animFn(game).then(resolve);
    });
}
/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />
/**
 * @brief Shows the results step
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function resultStep(game) {
    return new Promise(function (resolve, reject) {
        var config = game.getConfig();
        var container = game.getContainer();
        var card = config.card.Card;
        var cardLang = config.card.CardLang;
        var cardItems = config.cardItems;
        var stepContainer = container.find('.tarot-game-step');
        var stepTitleContainer = stepContainer.find('.tarot-game-step-title');
        var stepDescContainer = stepContainer.find('.tarot-game-step-desc');
        var stepContContainer = stepContainer.find('.tarot-game-step-cont');
        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-result');
        // load background
        var loadBackgroundFn = function (force) {
            if (force === void 0) { force = false; }
            var isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            }
            else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() === TarotGameStep.SHOW_RESULTS || force) {
                var step = 'result';
                container.css({
                    'background-color': card['step_' + step + '_bg_color'] || 'transparent',
                    'background-image': TemplateHelper.cssUrl(card[isMobile ? 'step_' + step + '_mobile_bg_image' : 'step_' + step + '_bg_image'], config.cardImagesUrl) || 'unset',
                });
            }
        };
        loadBackgroundFn(true);
        // ensure the background image is updated when going to mobile
        TemplateHelper.registerWindowResizeEvent(loadBackgroundFn, false, 0);
        // ensure we have the correct title and description there
        animSetText(stepTitleContainer, cardLang.step_result_title);
        animSetText(stepDescContainer, cardLang.step_result_description);
        // clear the cards container and shows the results
        stepContContainer.html('');
        var result = game.getResult();
        var selectedCardItemIds = game.getSelectedCardItemIds();
        var html = $('<div class="tarot-result"></div>');
        stepContContainer.append(html);
        var mainHtml = $('<div class="tarot-result-content"></div>');
        html.append(mainHtml);
        var mainHtmlForCards = $('<div class="tarot-result-content-card"></div>');
        mainHtmlForCards.append('<h3>' + result.tr.card_title + '</h3>');
        var resultTextCount = 0;
        for (var k = 0; k < selectedCardItemIds.length; ++k) {
            var i = +selectedCardItemIds[k];
            var cardItem = cardItems[i].CardItem;
            var cardItemLang = cardItems[i].CardItemLang;
            mainHtmlForCards.append('<div class="numerated-text"><span class="text-num">' + (++resultTextCount) + '</span><p>' + cardItemLang.description + '</p></div>');
        }
        mainHtml.append(mainHtmlForCards);
        if (result.email_form) {
            mainHtmlForCards = $('<div class="tarot-result-content-card card-blur"></div>');
            mainHtml.append(mainHtmlForCards);
            mainHtmlForCards.append('<h3>' + result.tr.result_title + '</h3>');
            mainHtmlForCards.append('<div class="txt-blur">' + result.text + '</div>');
            mainHtml.append('<div class="tarot-result-emailform">' + result.email_form + '</div>');
            mainHtml.append('<div class="tarot_emailform_id" style="display:none">' + result.card_id + '</div>');
            bindEmailFormCard();
        }
        else {
            mainHtmlForCards = $('<div class="tarot-result-content-card"></div>');
            mainHtml.append(mainHtmlForCards);
            mainHtmlForCards.append('<h3>' + result.tr.result_title + '</h3>');
            mainHtmlForCards.append(result.text);
        }
        if (cardLang.step_result_next) {
            mainHtmlForCards.append('<h3 class="card_next_data" style="margin-top:15px;">' + result.tr.next_title + '</h3>');
            mainHtmlForCards.append('<p class="card_next_data">' + cardLang.step_result_next + '</p>');
        }
        if (result.register_form) {
            var clickableTitle_1 = $('<span class="tarot-result-register-btn">' + result.tr.ins_title + '</span>');
            var clickableTitleCont_1 = $('<div class="tarot-result-content-next-btn card_next_data"></div>');
            clickableTitleCont_1.append(clickableTitle_1);
            //mainHtmlForCards.append(clickableTitleCont);
            var registerForm_1 = $('<div class="tarot-result-content-form hidden-form"></div>');
            registerForm_1.append('<h3>' + result.tr.ins_title + '</h3>');
            registerForm_1.append('<div>' + result.register_form + '</div>');
            mainHtmlForCards.append(registerForm_1);
            clickableTitle_1.on('click', function () {
                var scrollTo = clickableTitle_1.offset().top - $('body > header').outerHeight() - 20;
                clickableTitleCont_1.animate({ height: 0, opacity: 0, margin: 0 }, function () {
                    $(this).hide();
                });
                var s1 = $('html').scrollTop();
                var s2 = $('body').scrollTop();
                registerForm_1.removeClass('hidden-form');
                var toFocus = $('#UserFirstname');
                if (!toFocus.length) {
                    toFocus = registerForm_1.find('input:first');
                }
                window.setTimeout(function () {
                    toFocus.focus();
                    $('html, body').stop();
                    $('html').scrollTop(s1);
                    $('body').scrollTop(s2);
                    $('html, body').animate({
                        scrollTop: scrollTo
                    }, 850);
                });
            });
        }
        else {
            mainHtmlForCards.append('<div class="tarot-result-content-next-btn"><a class="tarot-result-register-btn" href="' + (result.next_link || '/') + '">' + result.tr.ins_title + '</a></div>');
        }
        if (result.other_games.length) {
            mainHtml.append('<div class="tarot-result-other-games-head" style="color:' + card.embed_image_text_color + '">' + result.tr.main_other + '</div>');
            var otherGames = $('<div class="tarot-result-other-games"></div>');
            mainHtml.append(otherGames);
            for (var k = 0; k < result.other_games.length; ++k) {
                var otherGame = result.other_games[k];
                var cont = $('<div class="tarot-result-other-game-cont"></div>');
                otherGames.append(cont);
                var img = $('<img />');
                img.attr('src', TemplateHelper.prefixUrl(otherGame.embed_image, config.cardImagesUrl));
                img.attr('alt', otherGame.name);
                cont.append(img);
                cont.append('<div class="tarot-result-other-game-title" style="color:' + otherGame.embed_image_text_color + '">' + otherGame.name + '</div>');
                cont.append('<div class="tarot-result-other-game-desc">' + otherGame.description + '</div>');
                cont.append('<div class="tarot-result-other-game-btn"><a href="' + otherGame.link + '">' + result.tr.see_game + '</a></div>');
            }
        }
        var sideHtml = $('<div class="tarot-result-side"></div>');
        html.append(sideHtml);
        var sideRevHtml = $('<div class="tarot-result-side-rev"></div>');
        sideRevHtml.append('<div class="tarot-result-side-title">' + result.tr.side_rev_title + '</div>');
        sideRevHtml.append('<div class="tarot-result-side-desc">' + result.tr.side_rev_desc + '</div>');
        sideHtml.append(sideRevHtml);
        var sideRevCardsHtml = $('<div class="tarot-result-side-rev-cards"></div>');
        for (var k = 0; k < selectedCardItemIds.length; ++k) {
            var i = +selectedCardItemIds[k];
            var cardItem = cardItems[i].CardItem;
            var cardItemLang = cardItems[i].CardItemLang;
            var el = $('<div class="tarot-result-side-rev-card" tabindex="0"></div>');
            var imgFront = $('<img />');
            imgFront.attr('src', TemplateHelper.prefixUrl(cardItem.image, config.cardItemImagesUrl));
            imgFront.attr('alt', cardItemLang.title);
            el.append('<div class="tarot-result-side-rev-card-title">' + cardItemLang.title + '</div>');
            el.append($('<div class="tarot-result-side-rev-card-image"></div>').append(imgFront));
            el.append('<div class="tarot-result-side-rev-card-description">' + cardItemLang.description + '</div>');
            sideRevCardsHtml.append(el);
        }
        sideRevHtml.append(sideRevCardsHtml);
        var sideRevHtmlMobile = sideRevHtml.clone();
        sideRevHtmlMobile.addClass('mobile');
        html.prepend(sideRevHtmlMobile);
        var sideExpHtml = $('<div class="tarot-result-side-exp"></div>');
        sideHtml.append(sideExpHtml);
        sideExpHtml.append('<div class="tarot-result-side-title">' + result.tr.side_exp_title + '</div>');
        for (var i = 0; i < result.related_experts.length; ++i) {
            var expert = result.related_experts[i];
            var el = $('<div class="tarot-result-side-exp-box"></div>');
            sideExpHtml.append(el);
            var elDiv1 = $('<div class="tarot-result-side-exp-box-img"></div>');
            el.append(elDiv1);
            elDiv1.append('<img class="img-responsive img-circle img-con" alt="' + expert.name + ' Image" src="' + (expert.profile_image || result.default_expert_profile_image) + '" />');
            elDiv1.append('<span class="exp-status exp-status-' + expert.status + '" title="' + expert.status + '"></span>');
            var elDiv2 = $('<div class="tarot-result-side-exp-box-info"></div>');
            el.append(elDiv2);
            elDiv2.append('<div class="tarot-result-side-exp-box-title">' + expert.name + '<span class="exp-rating"><i class="fa fa-star"></i>' + (Math.round(expert.rating * 10) / 10) + '</span></div>');
            var elCats = $('<div class="tarot-result-side-exp-box-cats"></div>');
            elDiv2.append(elCats);
            for (var j = 0; j < Math.min(3, expert.categories.length); ++j) {
                elCats.append('<span>' + expert.categories[j].name + '</span>');
            }
            var elDiv3 = $('<div class="tarot-result-side-exp-box-actions"></div>');
            el.append(elDiv3);
            elDiv3.append('<a href="' + expert.link + '"><span class="exp-tel"><i></i>' + result.tr.tel + '</span></a>');
            elDiv3.append('<a href="' + expert.link + '"><span class="exp-email"><i></i>' + result.tr.email + '</span></a>');
            var elDesc = $('<div class="tarot-result-side-exp-desc"></div>');
            sideExpHtml.append(elDesc);
            elDesc.html('<span>' + result.tr.side_exp_see_title + '</span><p>' + result.tr.side_exp_see_desc + '</p>');
            var elBtn = $('<div class="tarot-result-side-exp-btn"></div>');
            sideExpHtml.append(elBtn);
            elBtn.html('<a href="' + expert.link + '">' + result.tr.side_exp_see_more + ' ' + expert.name.toUpperCase() + '</a>');
        }
        //
        resolve();
    });
}
/// <reference path="./initialStep" />
/// <reference path="./showCardsStep" />
/// <reference path="./shuffleCardsStep" />
/// <reference path="./chooseCardsStep" />
/// <reference path="./processStep" />
/// <reference path="./resultStep" />
/// <reference path="./Globals" />
/// <reference path="./enums/TarotGameStep" />
/// <reference path="./helpers/TemplateHelper" />
/// <reference path="./animations/index" />
/// <reference path="./steps/index" />
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
/**
 * @brief This is the main class for the game.
 */
var TarotGame = /** @class */ (function () {
    /**
     * @brief The constructor of this class.
     * @param JQuery|HTMLElement container The container of the tarot game.
     * @param any config The configuration to use, should contain the following keys:
     *                      `card`, `cardItem`, `cardImagesUrl` and `cardItemImagesUrl`.
     */
    function TarotGame(container, config) {
        var _this = this;
        /// The configuration used in this game
        this.config = null;
        /// The jquery container containing the game
        this.container = null;
        /// Set to \c true if the game is ready (all its assets are loaded)
        this.isReady = false;
        /// Set to \c true if the window is loaded (i.e. window.on('load') is called)
        this.isWindowLoaded = false;
        /// How many images remain to preload before assuming the game is ready
        this.remainingImagesToPreload = 0;
        /// Result object after selection
        this.result = {};
        /// Card Item Ids that are selected
        this.selectedCardItemIds = [];
        /// The jquery spinner element
        this.spinnerEl = null;
        /// Current step
        this.step = TarotGameStep.NONE;
        this.config = config;
        this.container = $(container);
        // preload images related to the card model
        if (config && config.card && config.card.Card) {
            this.preloadImagesFromConfig(config.card.Card, config.cardImagesUrl);
        }
        // preload images related to the card item models
        if (config && config.cardItems) {
            for (var k in config.cardItems) {
                if (config.cardItems[k] && config.cardItems[k].CardItem) {
                    this.preloadImagesFromConfig(config.cardItems[k].CardItem, config.cardItemImagesUrl);
                }
            }
        }
        // show loading spinner and wait for the window to load
        this.showSpinner();
        // wait for the window to load
        //this.isWindowLoaded = true; // actually, don't wait. Comment this and uncomment next lines if you wish to wait.
        $(window).on('load', function () {
            _this.isWindowLoaded = true;
            _this.checkReady();
        });
    }
    /**
     * @brief Adds the given card item id to the list of selected card item ids.
     * @param number cardItemId The card item id to add.
     */
    TarotGame.prototype.addSelectedCardItemId = function (cardItemId) {
        this.selectedCardItemIds.push(cardItemId);
    };
    /**
     * @brief Checks if the game is ready (in particular, if all images are loaded) and set the isReady private field.
     */
    TarotGame.prototype.checkReady = function () {
        if (this.isReady) {
            return;
        }
        if (this.remainingImagesToPreload > 0 || !this.isWindowLoaded) {
            return;
        }
        this.isReady = true;
        this.hideSpinner();
        // init the game
        this.init();
    };
    /**
     * @brief Returns the cards distribution animation and ordering functions based on current game display mode.
     * @return any The cards distribution animation and ordering functions based on current game display mode
     */
    TarotGame.prototype.getCardDistributionAnimationFn = function () {
        var animDistributeFn = animDistributeCardsLine;
        var distributeOrderFn = DistributeOrder.outFirstDistribution;
        var displayMode = +this.config.card.Card.display_mode;
        if (displayMode === DisplayMode.LINE) {
            animDistributeFn = animDistributeCardsLine;
        }
        else if (displayMode === DisplayMode.TWO_LINES) {
            animDistributeFn = animDistributeCardsTwoLines;
        }
        else if (displayMode === DisplayMode.SKEWED_LINE) {
            animDistributeFn = animDistributeCardsSkewedLine;
        }
        else if (displayMode === DisplayMode.ARC_LINE) {
            animDistributeFn = animDistributeCardsArc;
        }
        return {
            animationFn: animDistributeFn,
            orderFn: distributeOrderFn
        };
    };
    /**
     * @brief Returns a reference to the game configuration object.
     * @return JQuery A reference to the game configuration object.
     */
    TarotGame.prototype.getConfig = function () {
        return this.config;
    };
    /**
     * @brief Returns the jquery container containing this game.
     * @return JQuery The container containing this game.
     */
    TarotGame.prototype.getContainer = function () {
        return this.container;
    };
    /**
     * @brief Returns the selection result that was queried from backend.
     * @return any The selection result that was queried from backend.
     */
    TarotGame.prototype.getResult = function () {
        return this.result;
    };
    /**
     * @brief Returns the selected cards in current game.
     * @return number[] The selected cards in current game.
     */
    TarotGame.prototype.getSelectedCardItemIds = function () {
        return this.selectedCardItemIds;
    };
    TarotGame.prototype.getStep = function () {
        return this.step;
    };
    /**
     * @brief Hides the loading spinner.
     */
    TarotGame.prototype.hideSpinner = function () {
        if (!this.spinnerEl) {
            return;
        }
        this.spinnerEl.remove();
        this.spinnerEl = null;
    };
    /**
     * @brief Initiates the game.
     */
    TarotGame.prototype.init = function () {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        this.selectedCardItemIds = [];
                        this.step = TarotGameStep.NONE;
                        return [4 /*yield*/, initialStep(this)];
                    case 1:
                        _a.sent();
                        this.step = TarotGameStep.INITIATED_GAME;
                        return [4 /*yield*/, showCardsStep(this)];
                    case 2:
                        _a.sent();
                        this.step = TarotGameStep.READY_TO_SHUFFLE;
                        return [4 /*yield*/, shuffleCardsStep(this)];
                    case 3:
                        _a.sent();
                        this.step = TarotGameStep.CHOOSE_CARDS;
                        return [4 /*yield*/, chooseCardsStep(this)];
                    case 4:
                        _a.sent();
                        this.step = TarotGameStep.POST_CHOOSE_CARDS;
                        return [4 /*yield*/, this.queryProcessSelection()];
                    case 5:
                        _a.sent();
                        this.step = TarotGameStep.PROCESS_SELECTION;
                        return [4 /*yield*/, processStep(this)];
                    case 6:
                        _a.sent();
                        this.step = TarotGameStep.SHOW_RESULTS;
                        return [4 /*yield*/, resultStep(this)];
                    case 7:
                        _a.sent();
                        return [2 /*return*/];
                }
            });
        });
    };
    /**
     * @brief Event called when one single image image is done preloading.
     */
    TarotGame.prototype.onPreloadImageFinish = function () {
        this.remainingImagesToPreload--;
        this.checkReady();
    };
    /**
     * @brief Preload all images in the given data (i.e. any value with a key ending with the `'image'`).
     * @param any data The url of the image to preload.
     * @param callable|null loadCallback    The function to call when the image is done preloading.
     * @param callable|null errorCallback   The function to call when there was an error preloading the image.
     */
    TarotGame.prototype.preloadImagesFromConfig = function (data, urlPref) {
        for (var k in data) {
            if (k.endsWith('image') && data[k]) {
                this.remainingImagesToPreload++;
                TemplateHelper.preloadImage(urlPref + data[k], this.onPreloadImageFinish.bind(this), this.onPreloadImageFinish.bind(this));
            }
        }
    };
    /**
     * @brief Queries the backend with current selection and updates game data.
     */
    TarotGame.prototype.queryProcessSelection = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var that = _this;
            var real_card_item_ids = [];
            for (var i = 0; i < _this.selectedCardItemIds.length; ++i) {
                real_card_item_ids.push(_this.config.cardItems[_this.selectedCardItemIds[i]].CardItem.card_item_id);
            }
            $.ajax({
                url: '/cards/process_selection',
                method: 'POST',
                data: {
                    card_id: _this.config.card.Card.card_id,
                    lang_id: _this.config.card.CardLang.lang_id,
                    card_item_ids: real_card_item_ids
                },
                dataType: 'json'
            }).done(function (data) {
                if (!data || (typeof data.error !== 'undefined' && data.error)) {
                    reject();
                    return;
                }
                that.result = data;
                resolve();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                reject();
            });
        });
    };
    /**
     * @brief Shows the loading spinner.
     */
    TarotGame.prototype.showSpinner = function () {
        this.hideSpinner();
        this.spinnerEl = $('<div class="cards-spinner-container"><div class="cards-spinner">Loading ...</div></div>');
        this.container.append(this.spinnerEl);
    };
    return TarotGame;
}());
/// <reference path="./Globals" />
/// <reference path="./TarotGame" />
if (typeof window.TarotConfig !== 'undefined') {
    $(window.TarotConfig.selector).each(function (i, v) {
        new TarotGame($(v), window.TarotConfig);
    });
}
var DisplayMode;
(function (DisplayMode) {
    ///
    DisplayMode[DisplayMode["LINE"] = 1] = "LINE";
    ///
    DisplayMode[DisplayMode["TWO_LINES"] = 2] = "TWO_LINES";
    ///
    DisplayMode[DisplayMode["SKEWED_LINE"] = 3] = "SKEWED_LINE";
    ///
    DisplayMode[DisplayMode["ARC_LINE"] = 4] = "ARC_LINE";
})(DisplayMode || (DisplayMode = {}));
var GameType;
(function (GameType) {
    ///
    GameType[GameType["YES_NO"] = 1] = "YES_NO";
    ///
    GameType[GameType["SINGLE"] = 2] = "SINGLE";
    ///
    GameType[GameType["FORTUNE"] = 3] = "FORTUNE";
    ///
    GameType[GameType["LOVE"] = 4] = "LOVE";
})(GameType || (GameType = {}));
/**
 * @brief A helper with miscellaneous methods to define card distribution order.
 */
var DistributeOrder = /** @class */ (function () {
    function DistributeOrder() {
    }
    /**
     * @brief Computes distribution delay so that the cards are distributed one after another.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    DistributeOrder.orderedDistribution = function (baseDelay, i, count) {
        return i * baseDelay;
    };
    /**
     * @brief Computes distribution delay so that the first and last card are distributed first and central cards last.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    DistributeOrder.outFirstDistribution = function (baseDelay, i, count) {
        return Math.abs(i - (i > count / 2 ? count - 1 : 0)) * 2 * baseDelay;
    };
    /**
     * @brief Computes distribution delay so that all the cards are distributed at the same time.
     * @param number baseDelay  Base distribution delay in milliseconds.
     * @param number i          The index of the card to compute the delay for.
     * @param number count      How many cards there are.
     * @return number The computed delay.
     */
    DistributeOrder.synchroniousDistribution = function (baseDelay, i, count) {
        return 0;
    };
    return DistributeOrder;
}());
 })();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkdsb2JhbHMudHMiLCJlbnVtcy9UYXJvdEdhbWVTdGVwLnRzIiwiaGVscGVycy9UZW1wbGF0ZUhlbHBlci50cyIsImFuaW1hdGlvbnMvYW5pbVNldFRleHQudHMiLCJhbmltYXRpb25zL2FuaW1Jbml0aWFsU2hvd0NhcmRzLnRzIiwiYW5pbWF0aW9ucy9hbmltRGlzdHJpYnV0ZUNhcmRzTGluZS50cyIsImFuaW1hdGlvbnMvYW5pbURpc3RyaWJ1dGVDYXJkc1NrZXdlZExpbmUudHMiLCJhbmltYXRpb25zL2FuaW1EaXN0cmlidXRlQ2FyZHNUd29MaW5lcy50cyIsImFuaW1hdGlvbnMvYW5pbURpc3RyaWJ1dGVDYXJkc0FyYy50cyIsImFuaW1hdGlvbnMvYW5pbVNodWZmbGVDYXJkcy50cyIsImFuaW1hdGlvbnMvYW5pbVNlbGVjdENhcmQudHMiLCJhbmltYXRpb25zL2FuaW1Qcm9jZXNzU2luZ2xlLnRzIiwiYW5pbWF0aW9ucy9hbmltUHJvY2Vzc0ZvcnR1bmUudHMiLCJhbmltYXRpb25zL2FuaW1Qcm9jZXNzTG92ZS50cyIsImFuaW1hdGlvbnMvYW5pbVByb2Nlc3NZZXNOby50cyIsImFuaW1hdGlvbnMvaW5kZXgudHMiLCJzdGVwcy9pbml0aWFsU3RlcC50cyIsInN0ZXBzL3Nob3dDYXJkc1N0ZXAudHMiLCJzdGVwcy9zaHVmZmxlQ2FyZHNTdGVwLnRzIiwic3RlcHMvY2hvb3NlQ2FyZHNTdGVwLnRzIiwic3RlcHMvcHJvY2Vzc1N0ZXAudHMiLCJzdGVwcy9yZXN1bHRTdGVwLnRzIiwic3RlcHMvaW5kZXgudHMiLCJUYXJvdEdhbWUudHMiLCJpbmRleC50cyIsImVudW1zL0Rpc3BsYXlNb2RlLnRzIiwiZW51bXMvR2FtZVR5cGUudHMiLCJoZWxwZXJzL0Rpc3RyaWJ1dGVPcmRlci50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO0FBQUEsSUFBTSxDQUFDLEdBQVMsTUFBTyxDQUFDLE1BQU0sQ0FBQztBQ0EvQixJQUFLLGFBcUJKO0FBckJELFdBQUss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SUFBQTtJQWtDQSxDQUFDO0lBaENHOzs7Ozs7T0FNRztJQUNXLG1DQUFtQixHQUFqQyxVQUFrQyxTQUFpQixFQUFFLENBQVMsRUFBRSxLQUFhO1FBQ3pFLE9BQU8sQ0FBQyxHQUFHLFNBQVMsQ0FBQztJQUN6QixDQUFDO0lBRUQ7Ozs7OztPQU1HO0lBQ1csb0NBQW9CLEdBQWxDLFVBQW1DLFNBQWlCLEVBQUUsQ0FBUyxFQUFFLEtBQWE7UUFDMUUsT0FBTyxJQUFJLENBQUMsR0FBRyxDQUFDLENBQUMsR0FBRyxDQUFDLENBQUMsR0FBRyxLQUFLLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQyxLQUFLLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxHQUFHLENBQUMsR0FBRyxTQUFTLENBQUM7SUFDekUsQ0FBQztJQUVEOzs7Ozs7T0FNRztJQUNXLHdDQUF3QixHQUF0QyxVQUF1QyxTQUFpQixFQUFFLENBQVMsRUFBRSxLQUFhO1FBQzlFLE9BQU8sQ0FBQyxDQUFDO0lBQ2IsQ0FBQztJQUNMLHNCQUFDO0FBQUQsQ0FsQ0EsQUFrQ0MsSUFBQSIsImZpbGUiOiJjYXJkcy5qcyIsInNvdXJjZXNDb250ZW50IjpbImNvbnN0ICQgPSAoPGFueT53aW5kb3cpLmpRdWVyeTtcbiIsImVudW0gVGFyb3RHYW1lU3RlcCB7XG4gICAgLy8vIE5vdGhpbmcgeWV0XG4gICAgTk9ORSA9IDAsXG5cbiAgICAvLy8gQWZ0ZXIgaW5pdGlhbCBzdGVwLCB3ZSBqdXN0IHNob3duIHRoZSBnYW1lXG4gICAgSU5JVElBVEVEX0dBTUUgPSAxLFxuXG4gICAgLy8vIFdlIGFyZSBzaG93aW5nIHRoZSBjYXJkcywgd2FpdGluZyB0byBzaHVmZmxlIHRoZW1cbiAgICBSRUFEWV9UT19TSFVGRkxFLFxuXG4gICAgLy8vIFRoZSBjYXJkcyBhcmUgc2h1ZmZsZWQuIFdlIHdhaXQgZm9yIHRoZSB1c2VyIHRvIHNlbGVjdCB0aGUgY2FyZHNcbiAgICBDSE9PU0VfQ0FSRFMsXG5cbiAgICAvLy8gQWxsIHRoZSBjYXJkcyBhcmUgc2h1ZmZsZWQuIFdlIGFyZSBxdWVyeWluZyByZXN1bHQgZGF0YSBiZWZvcmUgc2hvd2luZyBhIHByb2Nlc3MgYW5pbWF0aW9uXG4gICAgUE9TVF9DSE9PU0VfQ0FSRFMsXG5cbiAgICAvLy8gQWxsIHRoZSBjYXJkcyBhcmUgc2h1ZmZsZWQuIFdlIGFyZSBzaG93aW5nIGFuIGFuaW1hdGlvbiBwcm9jZXNzaW5nIHRoZSBjYXJkc1xuICAgIFBST0NFU1NfU0VMRUNUSU9OLFxuXG4gICAgLy8vIEFsbCBkb25lLCB3ZSBhcmUgc2hvd2luZyB0aGUgcmVzdWx0c1xuICAgIFNIT1dfUkVTVUxUUyxcbn1cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgQSBoZWxwZXIgd2l0aCBtaXNjZWxsYW5lb3VzIG1ldGhvZHMgdXNlZCBpbiB0ZW1wbGF0ZXMuXG4gKi9cbmNsYXNzIFRlbXBsYXRlSGVscGVyIHtcbiAgICAvLy8gRGVmYXVsdCBkZWxheSBmb3IgZGVib3VuY2luZ1xuICAgIHB1YmxpYyBzdGF0aWMgcmVhZG9ubHkgQU5JTUFUSU9OX0RFTEFZX0RFQk9VTkNFID0gNTAwO1xuXG4gICAgLy8vIERlZmF1bHQgZGVsYXkgZm9yIGFuaW1hdGlvbnNcbiAgICBwdWJsaWMgc3RhdGljIHJlYWRvbmx5IEFOSU1BVElPTl9ERUxBWV9ERUZBVUxUID0gNTAwO1xuXG4gICAgLy8vIERlZmF1bHQgZGVsYXkgZm9yIHRleHQgYW5pbWF0aW9uc1xuICAgIHB1YmxpYyBzdGF0aWMgcmVhZG9ubHkgQU5JTUFUSU9OX0RFTEFZX1RFWFQgPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fREVMQVlfREVGQVVMVDtcblxuICAgIC8vLyBEZWZhdWx0IGVhc2luZyBmb3IgYW5pbWF0aW9uc1xuICAgIHB1YmxpYyBzdGF0aWMgcmVhZG9ubHkgQU5JTUFUSU9OX0VBU0lOR19ERUZBVUxUID0gJ3N3aW5nJztcblxuICAgIC8vLyBEZWZhdWx0IGVhc2luZyBmb3IgdGV4dCBhbmltYXRpb25zXG4gICAgcHVibGljIHN0YXRpYyByZWFkb25seSBBTklNQVRJT05fRUFTSU5HX1RFWFQgPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fRUFTSU5HX0RFRkFVTFQ7XG5cbiAgICAvLy9cbiAgICBwdWJsaWMgc3RhdGljIHJlYWRvbmx5IEVWRU5UX0dST1VQID0gJy50YXJvdCc7XG5cbiAgICAvLy8gY2FsbGJhY2tzIHRvIGNhbmNlbCBhbmltYXRpb25zXG4gICAgcHJpdmF0ZSBzdGF0aWMgYW5pbWF0aW9uQ2FuY2VsQ2FsbGJhY2tzOiBhbnkgPSB7XG4gICAgICAgIGxlbmd0aDogMFxuICAgIH07XG5cbiAgICAvLy8gdXNlZCBmb3IgY2FjaGluZyBpbmZvcm1hdGlvbiB3aXRoaW4gdGhpcyBjbGFzc1xuICAgIHByaXZhdGUgc3RhdGljIGNhY2hlOiBhbnkgPSB7fTtcblxuICAgIC8qKlxuICAgICAqIEBicmllZiBCdWlsZHMgYSBjYXJkIGl0ZW0gaHRtbCBlbGVtZW50IGFuZCByZXR1cm5zIGl0LlxuICAgICAqIEBwYXJhbSBhbnkgY29uZmlnIEN1cnJlbnQgZ2FtZSBjb25maWd1cmF0aW9uLlxuICAgICAqIEBwYXJhbSBudW1iZXIgaXRlbUluZGV4IFRoZSBjb25maWcgb2YgdGhlIGNhcmQgaXRlbSB0aGF0IGlzIGJlaW5nIGdlbmVyYXRlZC5cbiAgICAgKiBAcmV0dXJuIEpRdWVyeSBUaGUgYnVpbHQgY2FyZCBpdGVtIGh0bWwgZWxlbWVudC5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIGJ1aWxkQ2FyZEl0ZW1IdG1sKGNvbmZpZzogYW55LCBpdGVtSW5kZXg6IG51bWJlcikge1xuICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcbiAgICAgICAgY29uc3QgY2FyZEl0ZW1Db25maWcgPSBjb25maWcuY2FyZEl0ZW1zW2l0ZW1JbmRleF07XG4gICAgICAgIGNvbnN0IGNhcmRJdGVtID0gY2FyZEl0ZW1Db25maWcuQ2FyZEl0ZW07XG4gICAgICAgIGNvbnN0IGNhcmRJdGVtTGFuZyA9IGNhcmRJdGVtQ29uZmlnLkNhcmRJdGVtTGFuZztcbiAgICAgICAgY29uc3QgZWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtY2FyZC1pdGVtIGJpZ1wiIHJvbGU9XCJidXR0b25cIiB0YWJpbmRleD1cIjBcIj48L2Rpdj4nKTtcblxuICAgICAgICAvLyBzZXQgY3NzIGlmIGdpdmVuXG4gICAgICAgIGVsLmF0dHIoJ3N0eWxlJywgY2FyZC5pdGVtX2NzcyB8fCAnJyk7XG5cbiAgICAgICAgLy9cbiAgICAgICAgZWwuY3NzKHtcbiAgICAgICAgICAgIG9wYWNpdHk6IDAsXG4gICAgICAgICAgICAnYmFja2dyb3VuZC1jb2xvcic6IGNhcmQuaXRlbV9iZ19jb2xvciB8fCAndHJhbnNwYXJlbnQnXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIGlubmVyXG4gICAgICAgIGNvbnN0IGVsQ29udCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1jYXJkLWl0ZW0taW5uZXJcIiByb2xlPVwiYnV0dG9uXCIgdGFiaW5kZXg9XCIwXCI+PC9kaXY+Jyk7XG4gICAgICAgIGVsLmFwcGVuZChlbENvbnQpO1xuXG4gICAgICAgIC8vIGJhY2tcbiAgICAgICAgbGV0IGltZ0JhY2sgPSAkKCc8aW1nIC8+Jyk7XG4gICAgICAgIGltZ0JhY2suYWRkQ2xhc3MoJ3Rhcm90LWNhcmQtaXRlbS1pbWctYmFjaycpO1xuICAgICAgICBpbWdCYWNrLmF0dHIoJ3NyYycsIFRlbXBsYXRlSGVscGVyLnByZWZpeFVybChjYXJkLml0ZW1fYmdfaW1hZ2UsIGNvbmZpZy5jYXJkSW1hZ2VzVXJsKSk7XG4gICAgICAgIGltZ0JhY2suYXR0cignYWx0JywgJ0NhcmQgSW1hZ2UnKTtcbiAgICAgICAgZWxDb250LmFwcGVuZChpbWdCYWNrKTtcblxuICAgICAgICAvLyBmcm9udFxuICAgICAgICBsZXQgaW1nRnJvbnQgPSAkKCc8aW1nIC8+Jyk7XG4gICAgICAgIGltZ0Zyb250LmFkZENsYXNzKCd0YXJvdC1jYXJkLWl0ZW0taW1nLWZyb250Jyk7XG4gICAgICAgIGltZ0Zyb250LmF0dHIoJ3NyYycsIFRlbXBsYXRlSGVscGVyLnByZWZpeFVybChjYXJkSXRlbS5pbWFnZSwgY29uZmlnLmNhcmRJdGVtSW1hZ2VzVXJsKSk7XG4gICAgICAgIGltZ0Zyb250LmF0dHIoJ2FsdCcsIGNhcmRJdGVtTGFuZy50aXRsZSk7XG4gICAgICAgIGVsQ29udC5hcHBlbmQoaW1nRnJvbnQpO1xuXG4gICAgICAgIC8vXG4gICAgICAgIFRlbXBsYXRlSGVscGVyLnNldENhcmRJdGVtRm9yRWxlbWVudChlbCwgaXRlbUluZGV4KTtcblxuICAgICAgICByZXR1cm4gZWw7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIENhbmNlbHMgYW4gYW5pbWF0aW9uIGJ5IGNhbGxpbmcgaXRzIGNhbGxiYWNrIChub3RoaW5nIGlzIGRvbmUgaWYgdGhlIGFuaW1hdGlvbiB3YXMgbm90IGZvdW5kKS5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGlkIEFuIGlkIGRlc2lnbmF0aW5nIHRoZSBhbmltYXRpb24gdG8gY2FuY2VsLlxuICAgICAqIEByZXR1cm4gUHJvbWlzZSBUaGUgcmV0dXJuIHZhbHVlIGZyb20gdGhlIGFuaW1hdGlvbiBjYWxsYmFjayAoc2hvdWxkIGJlIGEgcHJvbWlzZSkgb3IgYSBwcm9taXNlIHRoYXQgcmVzb2x2ZXMgaW1tZWRpYXRlbHkgaWYgdGhlIGFuaW1hdGlvbiB3YXMgbm90IGZvdW5kLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgY2FuY2VsQW5pbWF0aW9uKGlkOiBudW1iZXIpIHtcbiAgICAgICAgbGV0IHI6IGFueSA9IG51bGw7XG4gICAgICAgIGlmIChpZCBpbiBUZW1wbGF0ZUhlbHBlci5hbmltYXRpb25DYW5jZWxDYWxsYmFja3MpIHtcbiAgICAgICAgICAgIGNvbnN0IGNhbGxiYWNrID0gVGVtcGxhdGVIZWxwZXIuYW5pbWF0aW9uQ2FuY2VsQ2FsbGJhY2tzW2lkXTtcbiAgICAgICAgICAgIGRlbGV0ZSBUZW1wbGF0ZUhlbHBlci5hbmltYXRpb25DYW5jZWxDYWxsYmFja3NbaWRdO1xuICAgICAgICAgICAgciA9IGNhbGxiYWNrKCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHIpIHtcbiAgICAgICAgICAgIHJldHVybiByO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBDYW5jZWxzIHRoZSByZWdpc3RlcmVkIGFuaW1hdGlvbiBvbiB0aGUgZ2l2ZW4gXFxhIGVsZW1lbnQgKGlmIGFueSkgYnkgY2FsbGluZyB0aGUgYXBwcm9wcmlhdGUgY2FsbGJhY2suXG4gICAgICogQHBhcmFtIEpRdWVyeSBlbGVtZW50IFRoZSBlbGVtZW50IHRvIGNhbmNlbCB0aGUgYW5pbWF0aW9uIGZvci5cbiAgICAgKiBAcmV0dXJuIFByb21pc2UgVGhlIHJldHVybiB2YWx1ZSBmcm9tIHRoZSBhbmltYXRpb24gY2FsbGJhY2sgKHNob3VsZCBiZSBhIHByb21pc2UpIG9yIGEgcHJvbWlzZSB0aGF0IHJlc29sdmVzIGltbWVkaWF0ZWx5IGlmIHRoZSBhbmltYXRpb24gd2FzIG5vdCBmb3VuZC5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIGNhbmNlbEFuaW1hdGlvbk9uRWxlbWVudChlbGVtZW50OiBhbnkpIHtcbiAgICAgICAgY29uc3QgYW5pbWF0aW9uSWQgPSBlbGVtZW50LmRhdGEoJ19hbmltYXRpb25JZCcpO1xuICAgICAgICBlbGVtZW50LnJlbW92ZURhdGEoJ19hbmltYXRpb25JZCcpO1xuICAgICAgICByZXR1cm4gVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uKCthbmltYXRpb25JZCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIEJ1aWxkcyBhbmQgcmV0dXJucyBhIHVybCBjc3MgcHJvcGVydHkgdmFsdWUuXG4gICAgICogQHBhcmFtIHN0cmluZyB1cmwgICAgVGhlIHVybCB0byBwcmVmaXggYW5kIHRvIGNvbnZlcnQgdG8gY3NzIHVybC5cbiAgICAgKiBAcGFyYW0gc3RyaW5nIHByZWYgICBUaGUgcHJlZml4IHRvIHByZXBlbmQgdG8gdGhlIHVybC5cbiAgICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgdXJsIGNzcyBwcm9wZXJ0eSB2YWx1ZSBjb3JyZXNwb25kaW5nIHRvIGdpdmVuIHByb3BlcnRpZXMuXG4gICAgICovXG4gICAgcHVibGljIHN0YXRpYyBjc3NVcmwodXJsOiBzdHJpbmcsIHByZWY6IHN0cmluZyA9ICcnKSB7XG4gICAgICAgIHVybCA9IHRoaXMucHJlZml4VXJsKHVybCwgcHJlZik7XG4gICAgICAgIGlmICghdXJsKSB7XG4gICAgICAgICAgICByZXR1cm4gJyc7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuICd1cmwoXFwnJyArIHVybCArICdcXCcpJztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgUmV0dXJucyBhIGNhcmQgaXRlbSB0aGF0IGNvcnJlc3BvbmRzIHRvIHRoZSBnaXZlbiBlbGVtZW50IGZyb20gdGhlIGdpdmVuIGFycmF5LlxuICAgICAqIEBwYXJhbSBKUXVlcnkgZWxlbWVudCBUaGUgZWxlbWVudCB0byBnZXQgdGhlIGNhcmQgaXRlbSBmb3IuXG4gICAgICogQHBhcmFtIGFueVtdIGNhcmRJdGVtcyBBbiBhcnJheSBjb250YWluaW5nIGNhcmQgaXRlbXMuXG4gICAgICogQHJldHVybiBhbnl8bnVsbCBUaGUgY2FyZCBpdGVtIHRoYXQgY29ycmVzcG9uZHMgdG8gdGhlIGdpdmVuIGVsZW1lbnQgb3IgXFxjIG51bGwgaWYgbm90IGZvdW5kLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgZ2V0Q2FyZEl0ZW1Gcm9tRWxlbWVudChlbGVtZW50OiBhbnksIGNhcmRJdGVtczogYW55W10pIHtcbiAgICAgICAgbGV0IGkgPSBUZW1wbGF0ZUhlbHBlci5nZXRDYXJkSXRlbUluZGV4RnJvbUVsZW1lbnQoZWxlbWVudCk7XG4gICAgICAgIGlmIChpID09PSBudWxsKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgICAgICBpZiAodHlwZW9mIGNhcmRJdGVtc1tpXSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBjYXJkSXRlbXNbaV07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFJldHVybnMgYSBjYXJkIGl0ZW0gaW5kZXggdGhhdCBjb3JyZXNwb25kcyB0byB0aGUgZ2l2ZW4gZWxlbWVudCBmcm9tIHRoZSBnaXZlbiBhcnJheS5cbiAgICAgKiBAcGFyYW0gSlF1ZXJ5IGVsZW1lbnQgVGhlIGVsZW1lbnQgdG8gZ2V0IHRoZSBjYXJkIGl0ZW0gZm9yLlxuICAgICAqIEByZXR1cm4gbnVtYmVyfG51bGwgVGhlIGNhcmQgaXRlbSBpbmRleCB0aGF0IGNvcnJlc3BvbmRzIHRvIHRoZSBnaXZlbiBlbGVtZW50IG9yIFxcYyBudWxsIGlmIG5vdCBmb3VuZC5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIGdldENhcmRJdGVtSW5kZXhGcm9tRWxlbWVudChlbGVtZW50OiBhbnkpIHtcbiAgICAgICAgbGV0IGk6IGFueSA9IGVsZW1lbnQuZGF0YSgnX2NhcmRfaXRlbV9pbmRleCcpO1xuICAgICAgICBpZiAodHlwZW9mIGkgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gK2k7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIEdldHMgY2FyZCBpdGVtIHNpemUuXG4gICAgICogQHBhcmFtIHN0cmluZyBjbGFzc05hbWUgVGhlIGNsYXNzIG5hbWUgdG8gcmVhZCBjc3MgZm9yLlxuICAgICAqIEByZXR1cm4gYW55IEFuIG9iamVjdCB3aXRoIGB3aWR0aGAgYW5kIGBoZWlnaHRgIGtleXMgY29ycmVzcG9uZGluZyB0byB0aGUgY2FyZCBzaXplLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgZ2V0Q2FyZEl0ZW1TaXplKGNsYXNzTmFtZTogc3RyaW5nID0gJycpIHtcbiAgICAgICAgaWYgKGNsYXNzTmFtZSAmJiBjbGFzc05hbWUuY2hhckF0KDApICE9ICcuJykge1xuICAgICAgICAgICAgY2xhc3NOYW1lID0gJy4nICsgY2xhc3NOYW1lO1xuICAgICAgICB9XG4gICAgICAgIGNvbnN0IHAgPSBUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpID8gJy50YXJvdC1jYXJkLW1vYmlsZSAnIDogJyc7XG4gICAgICAgIGNvbnN0IHN0eWxlID0ge1xuICAgICAgICAgICAgLi4uVGVtcGxhdGVIZWxwZXIuZ2V0U3R5bGUocCArICcudGFyb3QtZ2FtZSAudGFyb3QtY2FyZC1pdGVtJyksXG4gICAgICAgICAgICAuLi5UZW1wbGF0ZUhlbHBlci5nZXRTdHlsZShwICsgJy50YXJvdC1nYW1lIC50YXJvdC1jYXJkLWl0ZW0nICsgY2xhc3NOYW1lKVxuICAgICAgICB9O1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgd2lkdGg6IHBhcnNlRmxvYXQoc3R5bGUud2lkdGgpLFxuICAgICAgICAgICAgaGVpZ2h0OiBwYXJzZUZsb2F0KHN0eWxlLmhlaWdodClcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgR2V0cyBhbiBlbGVtZW50IHNpemUuXG4gICAgICogQHBhcmFtIHN0cmluZyBjbGFzc05hbWUgVGhlIGNsYXNzIG5hbWUgdG8gcmVhZCBjc3MgZm9yLlxuICAgICAqIEBwYXJhbSBib29sZWFuIG1vYmlsZUNoZWNrIFNob3VsZCB3ZSBjaGVjayBmb3IgbW9iaWxlLlxuICAgICAqIEByZXR1cm4gYW55IEFuIG9iamVjdCB3aXRoIGB3aWR0aGAgYW5kIGBoZWlnaHRgIGtleXMgY29ycmVzcG9uZGluZyB0byB0aGUgY2FyZCBzaXplLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgZ2V0RWxlbWVudFNpemUoY2xhc3NOYW1lOiBzdHJpbmcsIG1vYmlsZUNoZWNrOiBib29sZWFuID0gdHJ1ZSkge1xuICAgICAgICBjb25zdCBwID0gbW9iaWxlQ2hlY2sgJiYgVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKSA/ICcudGFyb3QtY2FyZC1tb2JpbGUgJyA6ICcnO1xuICAgICAgICBjb25zdCBzdHlsZSA9IHtcbiAgICAgICAgICAgIC4uLlRlbXBsYXRlSGVscGVyLmdldFN0eWxlKHAgKyBjbGFzc05hbWUpXG4gICAgICAgIH07XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICB3aWR0aDogcGFyc2VGbG9hdChzdHlsZS53aWR0aCksXG4gICAgICAgICAgICBoZWlnaHQ6IHBhcnNlRmxvYXQoc3R5bGUuaGVpZ2h0KVxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZWFkcyBjc3Mgc3R5bGUgZm9yIGdpdmVuIGNsYXNzLlxuICAgICAqIEBwYXJhbSBzdHJpbmcgY2xhc3NOYW1lIFRoZSBjbGFzcyBuYW1lIHRvIHJlYWQgY3NzIGZvci5cbiAgICAgKiBAcmV0dXJuIGFueSBUaGUgcmVhZCBzdHlsZS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIGdldFN0eWxlKGNsYXNzTmFtZTogc3RyaW5nKSB7XG4gICAgICAgIGlmICh0eXBlb2YgVGVtcGxhdGVIZWxwZXIuY2FjaGUuc3R5bGUgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYWNoZS5zdHlsZSA9IHt9O1xuICAgICAgICB9XG4gICAgICAgIGlmICh0eXBlb2YgVGVtcGxhdGVIZWxwZXIuY2FjaGUuc3R5bGVbY2xhc3NOYW1lXSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgIHJldHVybiBUZW1wbGF0ZUhlbHBlci5jYWNoZS5zdHlsZVtjbGFzc05hbWVdO1xuICAgICAgICB9XG4gICAgICAgIGxldCBzdHlsZVNoZWV0czogYW55ID0gd2luZG93LmRvY3VtZW50LnN0eWxlU2hlZXRzO1xuICAgICAgICBsZXQgY3NzVGV4dDogc3RyaW5nID0gJyc7XG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgc3R5bGVTaGVldHMubGVuZ3RoOyBpKyspe1xuICAgICAgICAgICAgbGV0IGNsYXNzZXM6IGFueSA9IHN0eWxlU2hlZXRzW2ldLnJ1bGVzIHx8IHN0eWxlU2hlZXRzW2ldLmNzc1J1bGVzO1xuICAgICAgICAgICAgaWYgKCFjbGFzc2VzKSB7XG4gICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBmb3IgKGxldCB4ID0gMDsgeCA8IGNsYXNzZXMubGVuZ3RoOyB4KyspIHtcbiAgICAgICAgICAgICAgICBpZiAoY2xhc3Nlc1t4XS5zZWxlY3RvclRleHQgPT09IGNsYXNzTmFtZSkge1xuICAgICAgICAgICAgICAgICAgICBjc3NUZXh0ICs9IGNsYXNzZXNbeF0uY3NzVGV4dCB8fCBjbGFzc2VzW3hdLnN0eWxlLmNzc1RleHQ7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgbGV0IHI6IGFueSA9IHt9O1xuICAgICAgICBsZXQgc3BsaXQgPSBjc3NUZXh0LnNwbGl0KC9bXFx7XFx9O10rLyk7XG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgc3BsaXQubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgIGxldCB2ID0gc3BsaXRbaV0uc3BsaXQoJzonLCAyKTtcbiAgICAgICAgICAgIGlmICh2Lmxlbmd0aCAhPT0gMikge1xuICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdlswXSA9IHZbMF0udHJpbSgpO1xuICAgICAgICAgICAgdlsxXSA9IHZbMV0udHJpbSgpO1xuICAgICAgICAgICAgclt2WzBdXSA9IHZbMV07XG4gICAgICAgIH1cbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FjaGUuc3R5bGVbY2xhc3NOYW1lXSA9IHI7XG4gICAgICAgIHJldHVybiByO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZXR1cm5zIFxcYyB0cnVlIGlmIHRoZSB1c2VyIGlzIHZpZXdpbmcgdGhlIHBhZ2UgaW4gbW9iaWxlLCBcXGMgZmFsc2Ugb3RoZXJ3aXNlLlxuICAgICAqIEByZXR1cm4gYm9vbGVhbiBcXGMgdHJ1ZSBpZiB0aGUgdXNlciBpcyB2aWV3aW5nIHRoZSBwYWdlIGluIG1vYmlsZSwgXFxjIGZhbHNlIG90aGVyd2lzZS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIGlzTW9iaWxlKCkge1xuICAgICAgICByZXR1cm4gJCgnYm9keScpLndpZHRoKCkgPCA5ODA7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFJldHVybnMgXFxjIHRydWUgaWYgdGhlIHVzZXIgaXMgdmlld2luZyB0aGUgcGFnZSBpbiBzYWZhcmksIFxcYyBmYWxzZSBvdGhlcndpc2UuXG4gICAgICogQHJldHVybiBib29sZWFuIFxcYyB0cnVlIGlmIHRoZSB1c2VyIGlzIHZpZXdpbmcgdGhlIHBhZ2UgaW4gc2FmYXJpLCBcXGMgZmFsc2Ugb3RoZXJ3aXNlLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgaXNTYWZhcmkoKSB7XG4gICAgICAgIGxldCB1YSA9IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgaWYgKHVhLmluZGV4T2YoJ3NhZmFyaScpICE9IC0xKSB7XG4gICAgICAgICAgICBpZiAodWEuaW5kZXhPZignY2hyb21lJykgPT09IC0xKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZXR1cm5zIGEgcHJlZml4ZWQgdXJsIGJhc2VkIG9uIHRoZSBnaXZlbiBwYXJhbWV0ZXJzLlxuICAgICAqIEBwYXJhbSBzdHJpbmcgdXJsIFRoZSB1cmwgdG8gcHJlZml4LlxuICAgICAqIEBwYXJhbSBzdHJpbmcgcHJlZiBUaGUgcHJlZml4IHRvIHByZXBlbmQgdG8gdGhlIHVybC5cbiAgICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgcHJlZml4ZWQgdXJsLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgcHJlZml4VXJsKHVybDogc3RyaW5nLCBwcmVmOiBzdHJpbmcgPSAnJykge1xuICAgICAgICBpZiAoIXVybCkge1xuICAgICAgICAgICAgcmV0dXJuICcnO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBwcmVmICsgdXJsO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBQcmVsb2FkIG9uZSBzaW5nbGUgaW1hZ2UuXG4gICAgICogQHBhcmFtIHN0cmluZyB1cmwgVGhlIHVybCBvZiB0aGUgaW1hZ2UgdG8gcHJlbG9hZC5cbiAgICAgKiBAcGFyYW0gY2FsbGFibGV8bnVsbCBsb2FkQ2FsbGJhY2sgICAgVGhlIGZ1bmN0aW9uIHRvIGNhbGwgd2hlbiB0aGUgaW1hZ2UgaXMgZG9uZSBwcmVsb2FkaW5nLlxuICAgICAqIEBwYXJhbSBjYWxsYWJsZXxudWxsIGVycm9yQ2FsbGJhY2sgICBUaGUgZnVuY3Rpb24gdG8gY2FsbCB3aGVuIHRoZXJlIHdhcyBhbiBlcnJvciBwcmVsb2FkaW5nIHRoZSBpbWFnZS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIHByZWxvYWRJbWFnZSh1cmw6IHN0cmluZywgbG9hZENhbGxCYWNrOiBhbnkgPSBudWxsLCBlcnJvckNhbGxCYWNrOiBhbnkgPSBudWxsKSB7XG4gICAgICAgIGxldCBpbWcgPSBuZXcgSW1hZ2UoKTtcbiAgICAgICAgaWYgKGxvYWRDYWxsQmFjaykge1xuICAgICAgICAgICAgaW1nLm9ubG9hZCA9IGxvYWRDYWxsQmFjaztcbiAgICAgICAgfVxuICAgICAgICBpZiAoZXJyb3JDYWxsQmFjaykge1xuICAgICAgICAgICAgaW1nLm9uZXJyb3IgPSBlcnJvckNhbGxCYWNrO1xuICAgICAgICB9XG4gICAgICAgIGltZy5zcmMgPSB1cmw7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFJlZ2lzdGVycyB0aGUgYW5pbWF0aW9uIGNhbmNlbGF0aW9uIGNhbGxiYWNrIGFuZCByZXR1cm5zIHRoZSByZWdpc3RyYXRpb24gaWQuXG4gICAgICogQHBhcmFtIGNhbGxhYmxlIGNhbGxiYWNrIFRoZSBhbmltYXRpb24gY2FuY2VsYXRpb24gY2FsbGJhY2suXG4gICAgICogQHJldHVybiBudW1iZXIgVGhlIGFuaW1hdGlvbiByZWdpc3RyYXRpb24gaWQuXG4gICAgICovXG4gICAgcHVibGljIHN0YXRpYyByZWdpc3RlckFuaW1hdGlvbihjYWxsYmFjazogYW55KTogbnVtYmVyIHtcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuYW5pbWF0aW9uQ2FuY2VsQ2FsbGJhY2tzLmxlbmd0aCArPSAxO1xuICAgICAgICBjb25zdCBpZCA9IFRlbXBsYXRlSGVscGVyLmFuaW1hdGlvbkNhbmNlbENhbGxiYWNrcy5sZW5ndGg7XG4gICAgICAgIFRlbXBsYXRlSGVscGVyLmFuaW1hdGlvbkNhbmNlbENhbGxiYWNrc1tpZF0gPSBjYWxsYmFjaztcbiAgICAgICAgcmV0dXJuIGlkO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZWdpc3RlcnMgdGhlIGFuaW1hdGlvbiBjYW5jZWxhdGlvbiBjYWxsYmFjayBvbiB0aGUgZ2l2ZW4gZWxlbWVudCBhbmQgcmV0dXJucyB0aGUgcmVnaXN0cmF0aW9uIGlkLlxuICAgICAqIEBwYXJhbSBKUXVlcnkgZWxlbWVudCBUaGUgZWxlbWVudCB0byByZWdpc3RlciB0aGUgYW5pbWF0aW9uIGZvci5cbiAgICAgKiBAcGFyYW0gY2FsbGFibGUgY2FsbGJhY2sgVGhlIGFuaW1hdGlvbiBjYW5jZWxhdGlvbiBjYWxsYmFjay5cbiAgICAgKiBAcmV0dXJuIG51bWJlciBUaGUgYW5pbWF0aW9uIHJlZ2lzdHJhdGlvbiBpZC5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIHJlZ2lzdGVyQW5pbWF0aW9uT25FbGVtZW50KGVsZW1lbnQ6IGFueSwgY2FsbGJhY2s6IGFueSkge1xuICAgICAgICBjb25zdCBpZCA9IFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyQW5pbWF0aW9uKGNhbGxiYWNrKTtcbiAgICAgICAgZWxlbWVudC5kYXRhKCdfYW5pbWF0aW9uSWQnLCBpZCk7XG4gICAgICAgIHJldHVybiBpZDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgUmVnaXN0ZXJzIHRoZSBzZWxlY3RhYmxlIGFuaW1hdGlvbiBvbiBob3ZlciBmb3IgZ2l2ZW4gY2FyZCBlbGVtZW50cy5cbiAgICAgKiBAcGFyYW0gSlF1ZXJ5IGVsZW1lbnRzIFRoZSBlbGVtZW50cyB0byByZWdpc3RlciB0aGUgYW5pbWF0aW9uIGZvci5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIHJlZ2lzdGVyU2VsZWN0YWJsZUFuaW1hdGlvbnMoZWxlbWVudHM6IGFueSkge1xuICAgICAgICBlbGVtZW50cy5lYWNoKGZ1bmN0aW9uKHRoaXM6IGFueSkge1xuICAgICAgICAgICAgY29uc3QgZSA9ICQodGhpcyk7XG4gICAgICAgICAgICBsZXQgcnpfb3JpZyA9IGUuZ2V0KDApLnN0eWxlLnRyYW5zZm9ybTtcbiAgICAgICAgICAgIGlmIChyel9vcmlnKSB7XG4gICAgICAgICAgICAgICAgcnpfb3JpZyA9IHJ6X29yaWcubWF0Y2goL3JvdGF0ZVpcXCgoWzAtOVxcLl0rKWRlZ1xcKS8pO1xuICAgICAgICAgICAgICAgIGlmIChyel9vcmlnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJ6X29yaWcgPSBwYXJzZUZsb2F0KHJ6X29yaWdbMV0gfHwgMCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKCFyel9vcmlnKSB7XG4gICAgICAgICAgICAgICAgcnpfb3JpZyA9IDA7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGUuY3NzKCd0cmFuc2l0aW9uJywgJ25vbmUnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBGbiA9IGZ1bmN0aW9uKHRoaXM6IGFueSwgbm93OiBhbnksIGZ4OiBhbnkpIHtcbiAgICAgICAgICAgICAgICBsZXQgc2MgPSAxICsgMC4yICogbm93IC8gMTAwO1xuICAgICAgICAgICAgICAgIGxldCB0eSA9IC04ICogbm93IC8gMTAwO1xuICAgICAgICAgICAgICAgIGxldCByeiA9IHJ6X29yaWcgKiAoMTAwIC0gbm93KSAvIDEwMDtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcygndHJhbnNmb3JtJywgJ3NjYWxlKCcgKyBzYyArICcpIHRyYW5zbGF0ZVkoJyArIHR5ICsgJyUpIHJvdGF0ZVooJyArIHJ6ICsgJ2RlZyknKTtcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICBlLm9uKCdtb3VzZWVudGVyLnRhcm90LWNhcmRzLXNlbGVjdGFibGUnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBlLnN0b3AoKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgYm9yZGVyU3BhY2luZzogMTAwXG4gICAgICAgICAgICAgICAgfSwge1xuICAgICAgICAgICAgICAgICAgICBzdGVwOiBzdGVwRm4sXG4gICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uOiA1MDAsXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGUub24oJ21vdXNlbGVhdmUudGFyb3QtY2FyZHMtc2VsZWN0YWJsZScsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGUuc3RvcCgpLmFuaW1hdGUoe1xuICAgICAgICAgICAgICAgICAgICBib3JkZXJTcGFjaW5nOiAwXG4gICAgICAgICAgICAgICAgfSwge1xuICAgICAgICAgICAgICAgICAgICBzdGVwOiBzdGVwRm4sXG4gICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uOiA1MDAsXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFJlZ2lzdGVycyB0aGUgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIHdoZW4gdGhlIHdpbmRvdyBpcyByZXNpemVkLlxuICAgICAqIEBwYXJhbSBjYWxsYWJsZSBjYWxsYmFjayBUaGUgbWV0aG9kIHRvIGNhbGwgd2hlbiB0aGUgd2luZG93IGlzIHJlc2l6ZWQuXG4gICAgICogQHBhcmFtIGJvb2xlYW4gb25jZSAgICAgIFNldCB0byB0cnVlIHNvIHRoYXQgdGhlIGV2ZW50IGlzIG9ubHkgY2FsbGVkIG9uY2UuXG4gICAgICogQHBhcmFtIG51bWJlciBkZWJvdW5jZSAgIE1pbmltdW0gZGVsYXkgZm9yIHRoZSBldmVudCB0byBiZSBjYWxsZWQuXG4gICAgICovXG4gICAgcHVibGljIHN0YXRpYyByZWdpc3RlcldpbmRvd1Jlc2l6ZUV2ZW50KGNhbGxiYWNrOiBhbnksIG9uY2UgPSBmYWxzZSwgZGVib3VuY2UgPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fREVMQVlfREVCT1VOQ0UpIHtcbiAgICAgICAgbGV0IHNjaGVkdWxlZEV2ZW50OiBhbnkgPSBudWxsO1xuICAgICAgICBjb25zdCBmbiA9ICgpID0+IHtcbiAgICAgICAgICAgIGlmIChzY2hlZHVsZWRFdmVudCkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNjaGVkdWxlZEV2ZW50ID0gd2luZG93LnNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgIHNjaGVkdWxlZEV2ZW50ID0gbnVsbDtcbiAgICAgICAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICAgICAgfSwgZGVib3VuY2UpO1xuICAgICAgICB9O1xuICAgICAgICBpZiAob25jZSkge1xuICAgICAgICAgICAgJCh3aW5kb3cpLm9uZSgncmVzaXplIG9yaWVudGF0aW9uY2hhbmdlJywgZm4pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgJCh3aW5kb3cpLm9uKCdyZXNpemUgb3JpZW50YXRpb25jaGFuZ2UnLCBmbik7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgU2F2ZXMgdGhlIGdpdmVuIFxcYSBjYXJkSXRlbUluZGV4IGluIHRoZSBnaXZlbiBcXGEgZWxlbWVudCB0byBiZSByZXRyaWV2ZWQgd2l0aCBnZXRDYXJkSXRlbUZyb21FbGVtZW50KCkuXG4gICAgICogQHBhcmFtIEpRdWVyeSBlbGVtZW50IFRoZSBlbGVtZW50IHRvIGdldCB0aGUgY2FyZCBpdGVtIGZvci5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGNhcmRJdGVtSW5kZXggVGhlIGluZGV4IHRvIHNhdmUuXG4gICAgICovXG4gICAgcHVibGljIHN0YXRpYyBzZXRDYXJkSXRlbUZvckVsZW1lbnQoZWxlbWVudDogYW55LCBjYXJkSXRlbUluZGV4OiBudW1iZXIpIHtcbiAgICAgICAgZWxlbWVudC5kYXRhKCdfY2FyZF9pdGVtX2luZGV4JywgY2FyZEl0ZW1JbmRleCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFVucmVnaXN0ZXJzIHRoZSBhbmltYXRpb24gY2FsbGJhY2sgKG5vdGhpbmcgaXMgZG9uZSBpZiB0aGUgYW5pbWF0aW9uIHdhcyBub3QgZm91bmQpLlxuICAgICAqIEBwYXJhbSBudW1iZXIgaWQgQW4gaWQgZGVzaWduYXRpbmcgdGhlIGFuaW1hdGlvbiB0byB1bnJlZ2lzdGVyLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgdW5yZWdpc3RlckFuaW1hdGlvbihpZDogbnVtYmVyKSB7XG4gICAgICAgIGlmIChpZCBpbiBUZW1wbGF0ZUhlbHBlci5hbmltYXRpb25DYW5jZWxDYWxsYmFja3MpIHtcbiAgICAgICAgICAgIGRlbGV0ZSBUZW1wbGF0ZUhlbHBlci5hbmltYXRpb25DYW5jZWxDYWxsYmFja3NbaWRdO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFVucmVnaXN0ZXJzIHRoZSByZWdpc3RlcmVkIGFuaW1hdGlvbiBvbiB0aGUgZ2l2ZW4gXFxhIGVsZW1lbnQgKGlmIGFueSkuXG4gICAgICogQHBhcmFtIEpRdWVyeSBlbGVtZW50IFRoZSBlbGVtZW50IHRvIGNhbmNlbCB0aGUgYW5pbWF0aW9uIGZvci5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIHVucmVnaXN0ZXJBbmltYXRpb25PbkVsZW1lbnQoZWxlbWVudDogYW55KSB7XG4gICAgICAgIGNvbnN0IGFuaW1hdGlvbklkID0gZWxlbWVudC5kYXRhKCdfYW5pbWF0aW9uSWQnKTtcbiAgICAgICAgZWxlbWVudC5yZW1vdmVEYXRhKCdfYW5pbWF0aW9uSWQnKTtcbiAgICAgICAgaWYgKGFuaW1hdGlvbklkKSB7XG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51bnJlZ2lzdGVyQW5pbWF0aW9uKCthbmltYXRpb25JZCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgVW5yZWdpc3RlcnMgdGhlIHNlbGVjdGFibGUgYW5pbWF0aW9uIG9uIGhvdmVyIGZvciBnaXZlbiBjYXJkIGVsZW1lbnRzLlxuICAgICAqIEBwYXJhbSBKUXVlcnkgZWxlbWVudHMgVGhlIGVsZW1lbnRzIHRvIHVucmVnaXN0ZXIgdGhlIGFuaW1hdGlvbiBmb3IuXG4gICAgICovXG4gICAgcHVibGljIHN0YXRpYyB1bnJlZ2lzdGVyU2VsZWN0YWJsZUFuaW1hdGlvbnMoZWxlbWVudHM6IGFueSkge1xuICAgICAgICBlbGVtZW50cy5lYWNoKGZ1bmN0aW9uKHRoaXM6IGFueSkge1xuICAgICAgICAgICAgY29uc3QgZSA9ICQodGhpcyk7XG4gICAgICAgICAgICBlLmNzcygndHJhbnNpdGlvbicsICcnKTtcbiAgICAgICAgICAgIGUub2ZmKCcudGFyb3QtY2FyZHMtc2VsZWN0YWJsZScpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgVXBkYXRlcyBhbiBlbGVtZW50IHRyYW5zaXRpb24gY3NzIHByb3BlcnR5IHRvIGhhdmUgdGhlIGdpdmVuIGRlbGF5LlxuICAgICAqIEBwYXJhbSBKUXVlcnkgZWxlbWVudCBUaGUgZWxlbWVudCB0byBwcm9jZXNzLlxuICAgICAqIEBwYXJhbSBzdHJpbmdbXSBwcm9wZXJ0aWVzIExpc3Qgb2YgcHJvcGVydHkgbmFtZXMgdG8gbWF0Y2guXG4gICAgICogQHBhcmFtIG51bWJlcnxudWxsIG5ld0RlbGF5TXMgVGhlIG5ldyBkZWxheSBpbiBtaWxsaXNlY29uZHMuXG4gICAgICogQHBhcmFtIHN0cmluZ3xudWxsIG5ld0Z1bmN0aW9uIFRoZSBuZXcgdHJhbnNpdGlvbiBmdW5jdGlvbi5cbiAgICAgKiBAcGFyYW0gbnVtYmVyfG51bGwgaW5kZXggVGhlIGluZGV4IG9mIHRoZSB0cmFuc2l0aW9uIHRvIGNoYW5nZSAoaWYgdGhlcmUgYXJlIG1hbnkpLlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgdXBkYXRlVHJhbnNpdGlvbkRlbGF5KGVsZW1lbnQ6IGFueSwgcHJvcGVydGllczogc3RyaW5nW118dHJ1ZSwgbmV3RGVsYXlNczogbnVtYmVyfG51bGwgPSBudWxsLCBuZXdGdW5jdGlvbjogc3RyaW5nfG51bGwgPSBudWxsLCBpbmRleDogbnVtYmVyfG51bGwgPSBudWxsKSB7XG4gICAgICAgIGNvbnN0IG5zID0gKG5ld0RlbGF5TXMgPT09IG51bGwgPyAwIDogbmV3RGVsYXlNcyAvIDEwMDAuMCkgKyAncyc7XG4gICAgICAgIGNvbnN0IG9sZFRyYW5zaXRpb25Qcm9wZXJ0aWVzID0gZWxlbWVudC5jc3MoJ3RyYW5zaXRpb24tcHJvcGVydHknKTtcbiAgICAgICAgY29uc3Qgb2xkVHJhbnNpdGlvbkR1cmF0aW9ucyA9IGVsZW1lbnQuY3NzKCd0cmFuc2l0aW9uLWR1cmF0aW9uJyk7XG4gICAgICAgIGNvbnN0IG9sZFRyYW5zaXRpb25GdW5jdGlvbnMgPSBlbGVtZW50LmNzcygndHJhbnNpdGlvbi10aW1pbmctZnVuY3Rpb24nKTtcbiAgICAgICAgY29uc3Qgc3BsaXQgPSBvbGRUcmFuc2l0aW9uUHJvcGVydGllcy5zcGxpdCgvXFxzKixcXHMqLyk7XG4gICAgICAgIGNvbnN0IHNwbGl0MiA9IG9sZFRyYW5zaXRpb25EdXJhdGlvbnMuc3BsaXQoL1xccyosXFxzKi8pO1xuICAgICAgICBjb25zdCBzcGxpdDMgPSBvbGRUcmFuc2l0aW9uRnVuY3Rpb25zLnNwbGl0KC9cXHMqLFxccyovKTtcbiAgICAgICAgaWYgKG5ld0RlbGF5TXMgIT09IG51bGwgJiYgc3BsaXQubGVuZ3RoICE9PSBzcGxpdDIubGVuZ3RoKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaWYgKG5ld0Z1bmN0aW9uICE9PSBudWxsICYmIHNwbGl0Lmxlbmd0aCAhPT0gc3BsaXQzLmxlbmd0aCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGluZGV4ICE9PSBudWxsICYmIGluZGV4IDwgMCkge1xuICAgICAgICAgICAgbGV0IG1heEluZGV4ID0gMDtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgc3BsaXQubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgICAgICBsZXQgdiA9IHNwbGl0W2ldO1xuICAgICAgICAgICAgICAgIGlmIChwcm9wZXJ0aWVzID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgICAgIG1heEluZGV4Kys7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZm9yIChsZXQgaiA9IDA7IGogPCAoPGFueVtdPnByb3BlcnRpZXMpLmxlbmd0aDsgaisrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodi50b0xvd2VyQ2FzZSgpLmluZGV4T2YoKDxhbnlbXT5wcm9wZXJ0aWVzKVtqXS50b0xvd2VyQ2FzZSgpKSAhPT0gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXhJbmRleCsrO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaW5kZXggPSBtYXhJbmRleCArIGluZGV4O1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IG5ld1RyYW5zaXRpb25EdXJhdGlvbnM6IHN0cmluZ1tdID0gW107XG4gICAgICAgIGxldCBuZXdUcmFuc2l0aW9uRnVuY3Rpb25zOiBzdHJpbmdbXSA9IFtdO1xuICAgICAgICBsZXQgY2hhbmdlID0gZmFsc2U7XG4gICAgICAgIGxldCBjdXJyZW50SW5kZXggPSAtMTtcbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzcGxpdC5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgbGV0IHYgPSBzcGxpdFtpXTtcbiAgICAgICAgICAgIGxldCB2MiA9IHNwbGl0MltpXTtcbiAgICAgICAgICAgIGxldCB2MyA9IHNwbGl0M1tpXTtcbiAgICAgICAgICAgIGxldCBmb3VuZCA9IGZhbHNlO1xuICAgICAgICAgICAgaWYgKHByb3BlcnRpZXMgPT09IHRydWUpIHtcbiAgICAgICAgICAgICAgICBmb3VuZCA9IHRydWU7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGogPSAwOyBqIDwgKDxhbnlbXT5wcm9wZXJ0aWVzKS5sZW5ndGg7IGorKykge1xuICAgICAgICAgICAgICAgICAgICBpZiAodi50b0xvd2VyQ2FzZSgpLmluZGV4T2YoKDxhbnlbXT5wcm9wZXJ0aWVzKVtqXS50b0xvd2VyQ2FzZSgpKSAhPT0gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvdW5kID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKGZvdW5kKSB7XG4gICAgICAgICAgICAgICAgY3VycmVudEluZGV4Kys7XG4gICAgICAgICAgICAgICAgaWYgKGluZGV4ID09PSBudWxsIHx8IGN1cnJlbnRJbmRleCA9PT0gaW5kZXgpIHtcbiAgICAgICAgICAgICAgICAgICAgdjIgPSBucztcbiAgICAgICAgICAgICAgICAgICAgdjMgPSBuZXdGdW5jdGlvbjtcbiAgICAgICAgICAgICAgICAgICAgY2hhbmdlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBuZXdUcmFuc2l0aW9uRHVyYXRpb25zLnB1c2godjIpO1xuICAgICAgICAgICAgbmV3VHJhbnNpdGlvbkZ1bmN0aW9ucy5wdXNoKHYzKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY2hhbmdlKSB7XG4gICAgICAgICAgICBpZiAobmV3RGVsYXlNcyAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuY3NzKCd0cmFuc2l0aW9uLWR1cmF0aW9uJywgbmV3VHJhbnNpdGlvbkR1cmF0aW9ucy5qb2luKCcsICcpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChuZXdGdW5jdGlvbiAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuY3NzKCd0cmFuc2l0aW9uLXRpbWluZy1mdW5jdGlvbicsIG5ld1RyYW5zaXRpb25GdW5jdGlvbnMuam9pbignLCAnKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgV2FpdHMgZm9yIHRoZSBhbmltYXRpb24gdG8gZmluaXNoIG9uIHRoZSBnaXZlbiBlbGVtZW50LlxuICAgICAqIEBwYXJhbSBKUXVlcnkgZWxlbWVudCBUaGUgZWxlbWVudCB0byB3YWl0IGZvci5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIG1heFdhaXQgTWF4aW11bSBkZWxheSB0byB3YWl0IGJlZm9yZSBmYWlsdXJlLlxuICAgICAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGFuaW1hdGlvbiBpcyBkb25lIG9uIHRoZSBlbGVtZW50LlxuICAgICAqL1xuICAgIHB1YmxpYyBzdGF0aWMgd2FpdEZpbmlzaEFuaW1hdGlvbk9uRWxlbWVudChlbGVtZW50OiBhbnksIG1heFdhaXQ6IG51bWJlciA9IDYwMDAwKSB7XG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZTx2b2lkPigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgICAgICBjb25zdCBhbmltYXRpb25JZCA9IGVsZW1lbnQuZGF0YSgnX2FuaW1hdGlvbklkJyk7XG4gICAgICAgICAgICBpZiAoYW5pbWF0aW9uSWQpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBkZWxheSA9IDIwMDtcbiAgICAgICAgICAgICAgICBpZiAobWF4V2FpdCA8PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlamVjdCgpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIud2FpdEZpbmlzaEFuaW1hdGlvbk9uRWxlbWVudChlbGVtZW50LCBtYXhXYWl0IC0gZGVsYXkpLnRoZW4ocmVzb2x2ZSkuY2F0Y2gocmVqZWN0KTtcbiAgICAgICAgICAgICAgICAgICAgfSwgZGVsYXkpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgU2V0cyB0aGUgaHRtbCBpbiBhIHNtb290aCBtYW5uZXIuXG4gKiBAcGFyYW0gSlF1ZXJ5IGNvbnRhaW5lciBUaGUgY29udGFpbmVyIHdoZXJlIHdlIGFyZSBnb2luZyB0byBzZXQgdGhlIHRleHQuXG4gKiBAcGFyYW0gc3RyaW5nIGh0bWwgVGhlIGh0bWwgY29udGVudCB0byBzZXQuXG4gKiBAcGFyYW0gaW50IGRlbGF5IFRoZSBhbmltYXRpb24gZGVsYXkuXG4gKiBAcGFyYW0gc3RyaW5nfGJvb2xlYW4gZWFzaW5nIERlZmF1bHQgYW5pbWF0aW9uIGVhc2luZyB0byBiZSBwYXNzZWQgdG8ganF1ZXJ5LlxuICogQHBhcmFtIGFueSBvcHRpb25zIEV4dHJhIG9wdGlvbnMgdG8gcGFzcyB0byBqcXVlcnkuXG4gKiBAcmV0dXJuIFByb21pc2UgQSBwcm9taXNlIGZvciB3aGVuIHRoZSBmdW5jdGlvbiBpcyBkb25lLlxuICovXG5mdW5jdGlvbiBhbmltU2V0VGV4dChcbiAgICBjb250YWluZXI6IGFueSxcbiAgICBodG1sOiBzdHJpbmcsXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9URVhULFxuICAgIGVhc2luZzogc3RyaW5nfGJvb2xlYW4gPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fRUFTSU5HX1RFWFQsXG4gICAgb3B0aW9uczogYW55ID0ge31cbikge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZTx2b2lkPigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIGNvbnN0IG9sZFNwYW4gPSBjb250YWluZXIuZmluZCgnPiBzcGFuJykuZmlyc3QoKTtcbiAgICAgICAgY29uc3Qgb2xkSHRtbCA9IChvbGRTcGFuLmxlbmd0aCA/IG9sZFNwYW4uaHRtbCgpIDogY29udGFpbmVyLmh0bWwoKSkudHJpbSgpO1xuICAgICAgICBodG1sID0gaHRtbC50cmltKCk7XG5cbiAgICAgICAgLy8gY2hlY2sgaWYgd2UgaGF2ZSBub3RoaW5nIHRvIGRvXG4gICAgICAgIGlmIChvbGRTcGFuLmxlbmd0aCAmJiBvbGRIdG1sID09PSBodG1sKSB7XG4gICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBjYW5jZWwgYW55IGV4aXN0aW5nIGFuaW1hdGlvbiBhbmQgc3RhcnQgdGhlIG5ldyBvbmVcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBuZXdTcGFuOiBhbnkgPSAkKCc8c3Bhbj4nICsgaHRtbCArICc8L3NwYW4+Jyk7XG4gICAgICAgICAgICBjb25zdCBvbGRTcGFuOiBhbnkgPSAkKCc8c3Bhbj4nICsgb2xkSHRtbCArICc8L3NwYW4+Jyk7XG5cbiAgICAgICAgICAgIGNvbnN0IG9sZENvbnRhaW5lclBvc2l0aW9uOiBzdHJpbmcgPSBjb250YWluZXIuY3NzKCdwb3NpdGlvbicpO1xuICAgICAgICAgICAgY29udGFpbmVyLmNzcygncG9zaXRpb24nLCAncmVsYXRpdmUnKTtcblxuICAgICAgICAgICAgbmV3U3Bhbi5jc3MoJ29wYWNpdHknLCAwKTtcblxuICAgICAgICAgICAgb2xkU3Bhbi5jc3Moe1xuICAgICAgICAgICAgICAgIHBvc2l0aW9uOiAnYWJzb2x1dGUnLFxuICAgICAgICAgICAgICAgIHRvcDogY29udGFpbmVyLmNzcygncGFkZGluZy10b3AnKSxcbiAgICAgICAgICAgICAgICBsZWZ0OiAnNTAlJyxcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICd0cmFuc2xhdGVYKC01MCUpJyxcbiAgICAgICAgICAgICAgICBvcGFjaXR5OiAxLFxuICAgICAgICAgICAgICAgICd0ZXh0LWFsaWduJzogJ2NlbnRlcicsXG4gICAgICAgICAgICAgICAgd2lkdGg6IGNvbnRhaW5lci53aWR0aCgpICsgJ3B4J1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGNvbnRhaW5lci5odG1sKCcnKTtcbiAgICAgICAgICAgIGNvbnRhaW5lci5hcHBlbmQobmV3U3Bhbik7XG4gICAgICAgICAgICBjb250YWluZXIuYXBwZW5kKG9sZFNwYW4pO1xuXG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlckFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIG9sZFNwYW4ucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgbmV3U3Bhbi5zdG9wKCk7XG4gICAgICAgICAgICAgICAgbmV3U3Bhbi5jc3MoeyBvcGFjaXR5OiAxIH0pO1xuICAgICAgICAgICAgICAgIGlmICghbmV3U3Bhbi5odG1sKCkudHJpbSgpKSB7XG4gICAgICAgICAgICAgICAgICAgIG5ld1NwYW4ucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvbnRhaW5lci5jc3MoJ3Bvc2l0aW9uJywgb2xkQ29udGFpbmVyUG9zaXRpb24pO1xuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBvbGRTcGFuLmFuaW1hdGUoeyBvcGFjaXR5OiAwIH0sIHtcbiAgICAgICAgICAgICAgICAuLi5vcHRpb25zLFxuICAgICAgICAgICAgICAgIGR1cmF0aW9uOiBkZWxheSxcbiAgICAgICAgICAgICAgICBlYXNpbmc6IGVhc2luZyxcbiAgICAgICAgICAgICAgICBxdWV1ZTogZmFsc2UsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIG5ld1NwYW4uYW5pbWF0ZSh7IG9wYWNpdHk6IDEgfSwge1xuICAgICAgICAgICAgICAgIC4uLm9wdGlvbnMsXG4gICAgICAgICAgICAgICAgZHVyYXRpb246IGRlbGF5LFxuICAgICAgICAgICAgICAgIGVhc2luZzogZWFzaW5nLFxuICAgICAgICAgICAgICAgIHF1ZXVlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICBhbHdheXM6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9KTtcbn1cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9UYXJvdEdhbWVcIiAvPlxuXG4vKipcbiAqIEBicmllZiBTaG93IHRoZSBjYXJkcyB0byBiZSBkaXN0cmlidXRlZCAoY2VudGVyIHRoZW0gYW5kIHNob3cgdGhlbSkuXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1Jbml0aWFsU2hvd0NhcmRzKFxuICAgIGdhbWU6IFRhcm90R2FtZSxcbiAgICBkZWxheTogbnVtYmVyID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0RFTEFZX0RFRkFVTFQsXG4gICAgZWFzaW5nOiBzdHJpbmd8Ym9vbGVhbiA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9FQVNJTkdfREVGQVVMVCxcbiAgICBvcHRpb25zOiBhbnkgPSB7fVxuKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcblxuICAgICAgICAvLyBjYW5jZWwgYW5pbWF0aW9ucyBvbiBjb250YWluZXIgZmlyc3RcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBUaXRsZUNvbnRhaW5lciAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC10aXRsZScpO1xuICAgICAgICAgICAgY29uc3Qgc3RlcERlc2NDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWRlc2MnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1jb250Jyk7XG4gICAgICAgICAgICBjb25zdCBpc01vYmlsZSA9IFRlbXBsYXRlSGVscGVyLmlzTW9iaWxlKCk7XG5cbiAgICAgICAgICAgIGNvbnN0IGNhcmRJdGVtRWxlbWVudHMgPSBjb250YWluZXIuZmluZCgnLnRhcm90LWNhcmQtaXRlbScpO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIucmVnaXN0ZXJBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLmVhY2goKGk6IGFueSwgZWw6IGFueSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbCA9ICQoZWwpO1xuICAgICAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvLyBjZW50ZXJcbiAgICAgICAgICAgIGNvbnN0IHNjVyA9IHN0ZXBDb250Q29udGFpbmVyLndpZHRoKCk7XG4gICAgICAgICAgICBjb25zdCBzY0ggPSBzdGVwQ29udENvbnRhaW5lci5oZWlnaHQoKTtcbiAgICAgICAgICAgIGNvbnN0IGNpVyA9IGNhcmRJdGVtRWxlbWVudHMuZmlyc3QoKS5vdXRlcldpZHRoKCk7XG4gICAgICAgICAgICBjb25zdCBjaUggPSBjYXJkSXRlbUVsZW1lbnRzLmZpcnN0KCkub3V0ZXJIZWlnaHQoKTtcbiAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWCA9ICgoc2NXIC0gY2lXKSAvIDIpO1xuICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJZID0gKChzY0ggLSBjaUgpIC8gMikgLSAoaXNNb2JpbGUgPyA1MCA6IDQ1KTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMuY3NzKHtcbiAgICAgICAgICAgICAgICBsZWZ0OiBjaUNlbnRlclggKyAncHgnLFxuICAgICAgICAgICAgICAgIHRvcDogY2lDZW50ZXJZICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAnei1pbmRleCc6IDEsXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gYW5pbWF0ZSB0byBmaW5hbCBwb3NpdGlvbnNcbiAgICAgICAgICAgIGNvbnN0IGRlbHRhID0gNTtcbiAgICAgICAgICAgIGxldCBrID0gLU1hdGgucm91bmQoY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGggLyAyKTtcbiAgICAgICAgICAgIGxldCByZW1haW5zID0gY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7XG4gICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLmVhY2goKGk6IGFueSwgZWw6IGFueSkgPT4ge1xuICAgICAgICAgICAgICAgIGVsID0gJChlbCk7XG4gICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICBlbC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICBvcGFjaXR5OiAxXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgZWwuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIGxlZnQ6IChjaUNlbnRlclggKyBrICogZGVsdGEpICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiAoY2lDZW50ZXJZICsgayAqIGRlbHRhKSArICdweCcsXG4gICAgICAgICAgICAgICAgfSwge1xuICAgICAgICAgICAgICAgICAgICAuLi5vcHRpb25zLFxuICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogZGVsYXksXG4gICAgICAgICAgICAgICAgICAgIGVhc2luZzogZWFzaW5nLFxuICAgICAgICAgICAgICAgICAgICBxdWV1ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGFsd2F5czogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZW1haW5zLS07XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXJlbWFpbnMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGsrKztcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9KTtcbn1cblxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL0dsb2JhbHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2hlbHBlcnMvVGVtcGxhdGVIZWxwZXJcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL1Rhcm90R2FtZVwiIC8+XG5cbi8qKlxuICogQGJyaWVmIERpc3RyaWJ1dGUgdGhlIGNhcmRzIG9uIGEgbGluZS5cbiAqIEBwYXJhbSBUYXJvdEdhbWUgZ2FtZSBUaGUgZ2FtZSBvYmplY3QuXG4gKiBAcGFyYW0gYW55IGRpc3RyaWJ1dGVPcmRlckZuIFRoZSBkaXN0cmlidXRpb24gb3JkZXIgZnVuY3Rpb24gdG8gdXNlLlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHBhcmFtIGFueSBleHRyYVRyYW5zZm9ybWF0aW9ucyBFeHRyYSB0cmFuc2Zvcm1hdGlvbnMgdG8gYXBwbHkuXG4gKiBAcGFyYW0gYm9vbGVhbiBvcmRlckZpcnN0IFNob3VsZCB3ZSBvcmRlciB0aGUgY2FyZHMgZmlyc3QuXG4gKiBAcGFyYW0gYm9vbGVhbiBwcmVzZXJ2ZVppbmRleCBXaGV0aGVyIHotaW5kZXggc2hvdWxkIGJlIHRoZSBzYW1lIGZvciBhbGwgY2FyZHMgKFxcYyB0cnVlKSBvciBkaWZmZXJlbnRseSBmb3IgZWFjaCBjYXJkIChcXGMgZmFsc2UpLlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1EaXN0cmlidXRlQ2FyZHNMaW5lKFxuICAgIGdhbWU6IFRhcm90R2FtZSxcbiAgICBkaXN0cmlidXRlT3JkZXJGbjogYW55LFxuICAgIGRlbGF5OiBudW1iZXIgPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fREVMQVlfREVGQVVMVCxcbiAgICBlYXNpbmc6IHN0cmluZ3xib29sZWFuID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0VBU0lOR19ERUZBVUxULFxuICAgIG9wdGlvbnM6IGFueSA9IHt9LFxuICAgIGV4dHJhVHJhbnNmb3JtYXRpb25zOiBhbnkgPSB7fSxcbiAgICB5Q29tcHV0ZUZuOiBhbnkgPSBudWxsLFxuICAgIG9yZGVyRmlyc3Q6IGJvb2xlYW4gPSBmYWxzZSxcbiAgICBwcmVzZXJ2ZVppbmRleDogYm9vbGVhbiA9IHRydWUsXG4gICAgcGFkZGluZzogbnVtYmVyID0gMCxcbiAgICBwYWRkaW5nTW9iaWxlOiBudW1iZXIgPSAwLFxuKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcblxuICAgICAgICAvLyBjYW5jZWwgYW5pbWF0aW9ucyBvbiBjb250YWluZXIgZmlyc3RcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcblxuICAgICAgICAgICAgY29uc3QgY2FyZEl0ZW1FbGVtZW50cyA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtY2FyZC1pdGVtJyk7XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlckFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMuZWFjaCgoaTogYW55LCBlbDogYW55KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGVsID0gJChlbCk7XG4gICAgICAgICAgICAgICAgICAgIGVsLnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBpc01vYmlsZSA9IFRlbXBsYXRlSGVscGVyLmlzTW9iaWxlKCk7XG4gICAgICAgICAgICBjb25zdCBzY1cgPSBzdGVwQ29udENvbnRhaW5lci53aWR0aCgpO1xuICAgICAgICAgICAgY29uc3Qgc2NIID0gc3RlcENvbnRDb250YWluZXIuaGVpZ2h0KCk7XG4gICAgICAgICAgICBjb25zdCBjUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgpO1xuICAgICAgICAgICAgY29uc3QgY2lXID0gY1Mud2lkdGg7XG4gICAgICAgICAgICBjb25zdCBjaUggPSBjUy5oZWlnaHQ7XG4gICAgICAgICAgICBjb25zdCBjaUNlbnRlclggPSAoKHNjVyAtIGNpVykgLyAyKTtcbiAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWSA9ICgoc2NIIC0gY2lIKSAvIDIpO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY2FyZEl0ZW1FbGVtZW50cy5ub3QoJy5mcm9udCcpLmNzcyh7XG4gICAgICAgICAgICAgICAgJ3otaW5kZXgnOiAxMDEsXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGlmIChUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpKSB7XG4gICAgICAgICAgICAgICAgcGFkZGluZyA9IHBhZGRpbmdNb2JpbGU7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBsZXQgZGVsdGEgPSAoc2NXIC0gY2lXIC0gcGFkZGluZykgLyAoY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGggLSAxKTtcbiAgICAgICAgICAgIGxldCBiYXNlRGVsYXkgPSBkZWxheSAvIGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoO1xuICAgICAgICAgICAgbGV0IHJlbWFpbnMgPSBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDtcblxuICAgICAgICAgICAgbGV0IG9yZGVyOiBhbnlbXSA9IFtdO1xuXG4gICAgICAgICAgICBpZiAob3JkZXJGaXJzdCkge1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMuZWFjaCgoaTogYW55LCBlbDogYW55KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGVsID0gJChlbCk7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IG8gPSBkaXN0cmlidXRlT3JkZXJGbihiYXNlRGVsYXksICtpLCBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aCk7XG4gICAgICAgICAgICAgICAgICAgIG9yZGVyLnB1c2goW2ksIG9dKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBvcmRlci5zb3J0KGZ1bmN0aW9uKGEsIGIpe3JldHVybiBhWzFdIC0gYlsxXX0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBvID0gb3JkZXJGaXJzdCA/IG9yZGVyW2ldIDogW2ksIGRpc3RyaWJ1dGVPcmRlckZuKGJhc2VEZWxheSwgK2ksIGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoKV07XG5cbiAgICAgICAgICAgICAgICBsZXQgZWwgPSAkKGNhcmRJdGVtRWxlbWVudHMuZ2V0KG9bMF0pKTtcblxuICAgICAgICAgICAgICAgIC8vIGRvIG5vdCBkaXN0cmlidXRlIGZyb250IGNhcmRzXG4gICAgICAgICAgICAgICAgaWYgKGVsLmhhc0NsYXNzKCdmcm9udCcpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlbWFpbnMtLTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFyZW1haW5zKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL1xuICAgICAgICAgICAgICAgIGVsLnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgZWwuZGVsYXkob1sxXSkucHJvbWlzZSgpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbC5yZW1vdmVDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgICAgIGVsLnJlbW92ZUNsYXNzKCdzbWFsbCcpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoZXh0cmFUcmFuc2Zvcm1hdGlvbnMgJiYgKHR5cGVvZihleHRyYVRyYW5zZm9ybWF0aW9ucykgPT09ICdmdW5jdGlvbicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5jc3MoZXh0cmFUcmFuc2Zvcm1hdGlvbnMoaSwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGgpKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmNzcyhleHRyYVRyYW5zZm9ybWF0aW9ucyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgeSA9IGlzTW9iaWxlID8gMTAgOiA1NTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgeU5vQ29tcHV0ZUZuID0gaXNNb2JpbGUgPyB5IDogeSAtIDY1O1xuICAgICAgICAgICAgICAgICAgICBlbC5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxlZnQ6IChwYWRkaW5nIC8gMiArIGkgKiBkZWx0YSkgKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiAoeUNvbXB1dGVGbiA/IHlDb21wdXRlRm4oeSwgaSwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGgpIDogeU5vQ29tcHV0ZUZuKSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgIH0sIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC4uLm9wdGlvbnMsXG4gICAgICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogZGVsYXksXG4gICAgICAgICAgICAgICAgICAgICAgICBlYXNpbmc6IGVhc2luZyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGFsd2F5czogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFwcmVzZXJ2ZVppbmRleCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ3otaW5kZXgnOiBpICsgMSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlbWFpbnMtLTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXJlbWFpbnMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHByZXNlcnZlWmluZGV4KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLm5vdCgnLmZyb250JykuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnei1pbmRleCc6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59XG5cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9UYXJvdEdhbWVcIiAvPlxuXG4vKipcbiAqIEBicmllZiBEaXN0cmlidXRlIHRoZSBjYXJkcyBvbiBhIGxpbmUgYW5kIHR3aXN0IHRoZW0uXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIGFueSBkaXN0cmlidXRlT3JkZXJGbiBUaGUgZGlzdHJpYnV0aW9uIG9yZGVyIGZ1bmN0aW9uIHRvIHVzZS5cbiAqIEBwYXJhbSBpbnQgZGVsYXkgVGhlIGFuaW1hdGlvbiBkZWxheS5cbiAqIEBwYXJhbSBzdHJpbmd8Ym9vbGVhbiBlYXNpbmcgRGVmYXVsdCBhbmltYXRpb24gZWFzaW5nIHRvIGJlIHBhc3NlZCB0byBqcXVlcnkuXG4gKiBAcGFyYW0gYW55IG9wdGlvbnMgRXh0cmEgb3B0aW9ucyB0byBwYXNzIHRvIGpxdWVyeS5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGFuaW1hdGlvbiBpcyBkb25lLlxuICovXG5mdW5jdGlvbiBhbmltRGlzdHJpYnV0ZUNhcmRzU2tld2VkTGluZShcbiAgICBnYW1lOiBUYXJvdEdhbWUsXG4gICAgZGlzdHJpYnV0ZU9yZGVyRm46IGFueSxcbiAgICBkZWxheTogbnVtYmVyID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0RFTEFZX0RFRkFVTFQsXG4gICAgZWFzaW5nOiBzdHJpbmd8Ym9vbGVhbiA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9FQVNJTkdfREVGQVVMVCxcbiAgICBvcHRpb25zOiBhbnkgPSB7fVxuKSB7XG4gICAgcmV0dXJuIGFuaW1EaXN0cmlidXRlQ2FyZHNMaW5lKGdhbWUsIGRpc3RyaWJ1dGVPcmRlckZuLCBkZWxheSwgZWFzaW5nLCBvcHRpb25zLCB7XG4gICAgICAgIHRyYW5zZm9ybTogJ3JvdGF0ZVooMjBkZWcpJ1xuICAgIH0sIG51bGwsIGZhbHNlLCB0cnVlLCA1MCwgNTApO1xufVxuXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgRGlzdHJpYnV0ZSB0aGUgY2FyZHMgb24gYSBsaW5lIHRoYXQncyBicm9rZW4gaW50byB0d28gcGFydHMuXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIGFueSBkaXN0cmlidXRlT3JkZXJGbiBUaGUgZGlzdHJpYnV0aW9uIG9yZGVyIGZ1bmN0aW9uIHRvIHVzZS5cbiAqIEBwYXJhbSBpbnQgZGVsYXkgVGhlIGFuaW1hdGlvbiBkZWxheS5cbiAqIEBwYXJhbSBzdHJpbmd8Ym9vbGVhbiBlYXNpbmcgRGVmYXVsdCBhbmltYXRpb24gZWFzaW5nIHRvIGJlIHBhc3NlZCB0byBqcXVlcnkuXG4gKiBAcGFyYW0gYW55IG9wdGlvbnMgRXh0cmEgb3B0aW9ucyB0byBwYXNzIHRvIGpxdWVyeS5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGFuaW1hdGlvbiBpcyBkb25lLlxuICovXG5mdW5jdGlvbiBhbmltRGlzdHJpYnV0ZUNhcmRzVHdvTGluZXMoXG4gICAgZ2FtZTogVGFyb3RHYW1lLFxuICAgIGRpc3RyaWJ1dGVPcmRlckZuOiBhbnksXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9ERUZBVUxULFxuICAgIGVhc2luZzogc3RyaW5nfGJvb2xlYW4gPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fRUFTSU5HX0RFRkFVTFQsXG4gICAgb3B0aW9uczogYW55ID0ge31cbikge1xuICAgIGNvbnN0IGlzTW9iaWxlID0gVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKTtcbiAgICBjb25zdCBCQVNFX1kgPSBpc01vYmlsZSA/IDAgOiAtMTQwO1xuICAgIGNvbnN0IEJBU0VfREVMVEFfWSA9IGlzTW9iaWxlID8gLTUwIDogLTEwMDtcbiAgICByZXR1cm4gYW5pbURpc3RyaWJ1dGVDYXJkc0xpbmUoZ2FtZSwgZGlzdHJpYnV0ZU9yZGVyRm4sIGRlbGF5LCBlYXNpbmcsIG9wdGlvbnMsIHt9LCBmdW5jdGlvbih5OiBudW1iZXIsIGk6IG51bWJlciwgY291bnQ6IG51bWJlcikge1xuICAgICAgICByZXR1cm4geSArIEJBU0VfWSAtIE1hdGguYWJzKGNvdW50IC8gMiAtIGkpICogQkFTRV9ERUxUQV9ZICogMiAvIGNvdW50O1xuICAgIH0pO1xufVxuXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgRGlzdHJpYnV0ZSB0aGUgY2FyZHMgb24gYSBhcmNcbiAqIEBwYXJhbSBUYXJvdEdhbWUgZ2FtZSBUaGUgZ2FtZSBvYmplY3QuXG4gKiBAcGFyYW0gYW55IGRpc3RyaWJ1dGVPcmRlckZuIFRoZSBkaXN0cmlidXRpb24gb3JkZXIgZnVuY3Rpb24gdG8gdXNlLlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1EaXN0cmlidXRlQ2FyZHNBcmMoXG4gICAgZ2FtZTogVGFyb3RHYW1lLFxuICAgIGRpc3RyaWJ1dGVPcmRlckZuOiBhbnksXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9ERUZBVUxULFxuICAgIGVhc2luZzogc3RyaW5nfGJvb2xlYW4gPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fRUFTSU5HX0RFRkFVTFQsXG4gICAgb3B0aW9uczogYW55ID0ge31cbikge1xuICAgIGNvbnN0IEJBU0VfREVHID0gNTA7XG4gICAgY29uc3QgQkFTRV9ZMCA9IC0yMDtcbiAgICBjb25zdCBCQVNFX1kgPSAzMDAgKiBNYXRoLmNvcyhCQVNFX0RFRyk7XG4gICAgcmV0dXJuIGFuaW1EaXN0cmlidXRlQ2FyZHNMaW5lKGdhbWUsIGRpc3RyaWJ1dGVPcmRlckZuLCBkZWxheSwgZWFzaW5nLCBvcHRpb25zLCBmdW5jdGlvbihpOiBudW1iZXIsIGNvdW50OiBudW1iZXIpIHtcbiAgICAgICAgY29uc3QgZCA9ICgoaSAtIGNvdW50IC8gMikgKiBCQVNFX0RFRyAvIGNvdW50KTtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHRyYW5zZm9ybTogJ3JvdGF0ZVooJyArIGQgICsgJ2RlZyknXG4gICAgICAgIH07XG4gICAgfSwgZnVuY3Rpb24oeTogbnVtYmVyLCBpOiBudW1iZXIsIGNvdW50OiBudW1iZXIpIHtcbiAgICAgICAgY29uc3QgZCA9ICgoaSAtIGNvdW50IC8gMikgKiBCQVNFX0RFRyAvIGNvdW50KSAqIE1hdGguUEkgLyAxODA7XG4gICAgICAgIHJldHVybiB5ICsgQkFTRV9ZMCArIE1hdGguc2luKE1hdGguYWJzKGQqZCkpICogQkFTRV9ZIC0gKFRlbXBsYXRlSGVscGVyLmlzTW9iaWxlKCkgPyAyMCA6IDApO1xuICAgIH0sIGZhbHNlLCB0cnVlLCA1MCwgNTApO1xufVxuXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgR2F0aGVyIGFuZCBzaHVmZmxlIHRoZSBjYXJkcy5cbiAqIEBwYXJhbSBUYXJvdEdhbWUgZ2FtZSBUaGUgZ2FtZSBvYmplY3QuXG4gKiBAcGFyYW0gaW50IGRlbGF5IFRoZSBhbmltYXRpb24gZGVsYXkuXG4gKiBAcGFyYW0gc3RyaW5nfGJvb2xlYW4gZWFzaW5nIERlZmF1bHQgYW5pbWF0aW9uIGVhc2luZyB0byBiZSBwYXNzZWQgdG8ganF1ZXJ5LlxuICogQHBhcmFtIGFueSBvcHRpb25zIEV4dHJhIG9wdGlvbnMgdG8gcGFzcyB0byBqcXVlcnkuXG4gKiBAcmV0dXJuIFByb21pc2UgQSBwcm9taXNlIGZvciB3aGVuIHRoZSBhbmltYXRpb24gaXMgZG9uZS5cbiAqL1xuZnVuY3Rpb24gYW5pbVNodWZmbGVDYXJkcyhcbiAgICBnYW1lOiBUYXJvdEdhbWUsXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9ERUZBVUxULFxuICAgIGVhc2luZzogc3RyaW5nfGJvb2xlYW4gPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fRUFTSU5HX0RFRkFVTFQsXG4gICAgb3B0aW9uczogYW55ID0ge31cbikge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZTx2b2lkPigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIGNvbnN0IGNvbnRhaW5lciA9IGdhbWUuZ2V0Q29udGFpbmVyKCk7XG5cbiAgICAgICAgLy8gY2FuY2VsIGFuaW1hdGlvbnMgb24gY29udGFpbmVyIGZpcnN0XG4gICAgICAgIFRlbXBsYXRlSGVscGVyLmNhbmNlbEFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgY29uc3Qgc3RlcENvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwJyk7XG4gICAgICAgICAgICBjb25zdCBzdGVwQ29udENvbnRhaW5lciAgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtY29udCcpO1xuXG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbUVsZW1lbnRzID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1jYXJkLWl0ZW0nKTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lciwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY2FyZEl0ZW1FbGVtZW50cy5lYWNoKChpOiBhbnksIGVsOiBhbnkpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZWwgPSAkKGVsKTtcbiAgICAgICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMuY3NzKHtcbiAgICAgICAgICAgICAgICAnei1pbmRleCc6IDEwMSxcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgbGV0IHJlbWFpbnMgPSAwO1xuICAgICAgICAgICAgbGV0IG5leHRBbmltYXRpb24gPSAwO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgbGV0IGFuaW1hdGlvbnM6IGFueVtdID0gW107XG4gICAgICAgICAgICBjb25zdCBsb2FkTmV4dEFuaW1hdGlvbiA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIHJlbWFpbnMtLTtcbiAgICAgICAgICAgICAgICBpZiAocmVtYWlucyA8PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlbWFpbnMgPSBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDtcbiAgICAgICAgICAgICAgICAgICAgaWYgKG5leHRBbmltYXRpb24gPT09IGFuaW1hdGlvbnMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5leHRBbmltYXRpb24rKztcbiAgICAgICAgICAgICAgICAgICAgICAgIGFuaW1hdGlvbnNbbmV4dEFuaW1hdGlvbiAtIDFdKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgZGVsYXlBbmltID0gZnVuY3Rpb24oZGVsYXk6IG51bWJlcikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVtYWlucyA9IDA7XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uLCBkZWxheSk7XG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBnYXRoZXJDZW50ZXJTcGxpdHRlZEFuaW0gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAvL1xuICAgICAgICAgICAgICAgIGNvbnN0IHBhZGRpbmcgPSAzMDtcbiAgICAgICAgICAgICAgICBjb25zdCBzY1cgPSBzdGVwQ29udENvbnRhaW5lci53aWR0aCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHNjSCA9IHN0ZXBDb250Q29udGFpbmVyLmhlaWdodCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNTID0gVGVtcGxhdGVIZWxwZXIuZ2V0Q2FyZEl0ZW1TaXplKCdiaWcnKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjaUNlbnRlclggPSAoKHNjVyAtIGNTLndpZHRoKSAvIDIpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWDEgPSBNYXRoLm1heChjaUNlbnRlclggLSBjUy53aWR0aCArIDIwLCAoc2NXIC8gMiAtIGNTLndpZHRoICsgcGFkZGluZykgLyAyKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjaUNlbnRlclgyID0gTWF0aC5taW4oY2lDZW50ZXJYICsgY1Mud2lkdGggLSAyMCwgKDMgKiBzY1cgLyAyIC0gY1Mud2lkdGggLSBwYWRkaW5nKSAvIDIpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWSA9ICgoc2NIIC0gY1MuaGVpZ2h0KSAvIDIpIC0gKFRlbXBsYXRlSGVscGVyLmlzTW9iaWxlKCkgPyA1MCA6IDQ1KTtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGhhbGZDb3VudCA9IE1hdGguY2VpbChjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aCAvIDIpO1xuICAgICAgICAgICAgICAgIGxldCBiYXNlRGVsYXkgPSBkZWxheSAvIGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoO1xuICAgICAgICAgICAgICAgIGxldCBkZWx0YSA9IDMuNTtcbiAgICAgICAgICAgICAgICBsZXQgZGVsdGFEZWdZID0gMTtcbiAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLmVhY2goKGk6IGFueSwgZWw6IGFueSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbCA9ICQoZWwpO1xuICAgICAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoZWwsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZGlyID0gKGkgPCBoYWxmQ291bnQgPyAtMSA6IDEpO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCByb3RZID0gKDIwICsgZGVsdGFEZWdZICogaSAvIDIpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgY2kgPSBpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoaSA8IGhhbGZDb3VudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICdyb3RhdGVaKDIwZGVnKSByb3RhdGVZKC0nICsgcm90WSArICdkZWcpIHJvdGF0ZVgoLTVkZWcpJ1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjaS09IGhhbGZDb3VudDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiAncm90YXRlWigtMjBkZWcpIHJvdGF0ZVkoJyArIHJvdFkgKyAnZGVnKSByb3RhdGVYKC01ZGVnKSdcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsLmRlbGF5KGJhc2VEZWxheSAqIGNpKS5wcm9taXNlKCkudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5hZGRDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5yZW1vdmVDbGFzcygnc21hbGwnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmFuaW1hdGUoe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxlZnQ6ICgoaSA8IGhhbGZDb3VudCA/IGNpQ2VudGVyWDEgOiBjaUNlbnRlclgyKSArIGRpciAqIGNpICogZGVsdGEpICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b3A6IGNpQ2VudGVyWSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogZGVsYXksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWFzaW5nOiBlYXNpbmcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKHsgJ3otaW5kZXgnOiBjaSArIDEwMSB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9hZE5leHRBbmltYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgZ2F0aGVyQ2VudGVyQW5pbSA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIC8vXG4gICAgICAgICAgICAgICAgY29uc3Qgc2NXID0gc3RlcENvbnRDb250YWluZXIud2lkdGgoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBzY0ggPSBzdGVwQ29udENvbnRhaW5lci5oZWlnaHQoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgnYmlnJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJYID0gKChzY1cgLSBjUy53aWR0aCkgLyAyKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjaUNlbnRlclkgPSAoKHNjSCAtIGNTLmhlaWdodCkgLyAyKSAtIChUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpID8gNTAgOiA0NSk7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBoYWxmQ291bnQgPSBNYXRoLmNlaWwoY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGggLyAyKTtcbiAgICAgICAgICAgICAgICBsZXQgYmFzZURlbGF5ID0gZGVsYXkgKiAzIC8gY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7XG4gICAgICAgICAgICAgICAgbGV0IGRlbHRhID0gMDtcbiAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLmVhY2goKGk6IGFueSwgZWw6IGFueSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbCA9ICQoZWwpO1xuICAgICAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoZWwsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICAgICAgZWwuZGVsYXkoYmFzZURlbGF5ICogKGkgPCBoYWxmQ291bnQgPyBpIDogaSAtIGhhbGZDb3VudCkpLnByb21pc2UoKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKCdiaWcnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLnJlbW92ZUNsYXNzKCdzbWFsbCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICcnLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZWZ0OiAoY2lDZW50ZXJYICsgKGkgKiBkZWx0YSkpICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b3A6IGNpQ2VudGVyWSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogZGVsYXksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWFzaW5nOiBlYXNpbmcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKHsgJ3otaW5kZXgnOiAxMDEgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvYWROZXh0QW5pbWF0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgMjsgKytpKSB7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGdhdGhlckNlbnRlclNwbGl0dGVkQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbShkZWxheSAvIDQpKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZ2F0aGVyQ2VudGVyQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbSgyMDApKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGxvYWROZXh0QW5pbWF0aW9uKCk7XG4gICAgICAgIH0pO1xuICAgIH0pO1xufVxuXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgQW5pbWF0aW9uIHdoZW4gY2xpY2tpbmcgb24gYSBjYXJkXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIEpRdWVyeSBlbGVtZW50IFRoZSBjYXJkIGl0ZW0gZWxlbWVudC5cbiAqIEBwYXJhbSBKUXVlcnkgcGxhY2Vob2xkZXIgVGhlIHBsYWNlaG9sZGVyIHdoZXJlIHRvIHBsYWNlIHRoZSBlbGVtZW50LlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1TZWxlY3RDYXJkKFxuICAgIGdhbWU6IFRhcm90R2FtZSxcbiAgICBlbGVtZW50OiBhbnksXG4gICAgcGxhY2Vob2xkZXI6IGFueSxcbiAgICBkZWxheTogbnVtYmVyID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0RFTEFZX0RFRkFVTFQsXG4gICAgZWFzaW5nOiBzdHJpbmd8Ym9vbGVhbiA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9FQVNJTkdfREVGQVVMVCxcbiAgICBvcHRpb25zOiBhbnkgPSB7fVxuKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcblxuICAgICAgICAvLyBjYW5jZWwgYW5pbWF0aW9ucyBvbiBjb250YWluZXIgZmlyc3RcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lciwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgZWxlbWVudC5jc3Moe1xuICAgICAgICAgICAgICAgICd6LWluZGV4JzogMTAxLFxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBsZXQgYW5pbWF0aW9uczogYW55W10gPSBbXTtcbiAgICAgICAgICAgIGxldCBuZXh0QW5pbWF0aW9uID0gMDtcbiAgICAgICAgICAgIGNvbnN0IGxvYWROZXh0QW5pbWF0aW9uID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgaWYgKG5leHRBbmltYXRpb24gPT09IGFuaW1hdGlvbnMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLmNhbmNlbEFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIG5leHRBbmltYXRpb24rKztcbiAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9uc1tuZXh0QW5pbWF0aW9uIC0gMV0oKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgZGVsYXlBbmltID0gZnVuY3Rpb24oZGVsYXk6IG51bWJlcikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNvbnN0IGRpc3BsYXlTZWxlY3RlZENhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgLy9cbiAgICAgICAgICAgICAgICBjb25zdCBzY1cgPSBzdGVwQ29udENvbnRhaW5lci53aWR0aCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHNjSCA9IHN0ZXBDb250Q29udGFpbmVyLmhlaWdodCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNTID0gVGVtcGxhdGVIZWxwZXIuZ2V0Q2FyZEl0ZW1TaXplKCdiaWcnKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjaVcgPSBjUy53aWR0aDtcbiAgICAgICAgICAgICAgICBjb25zdCBjaUggPSBjUy5oZWlnaHQ7XG4gICAgICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJYID0gKChzY1cgLSBjaVcpIC8gMik7XG4gICAgICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJZID0gKChlbGVtZW50Lm9mZnNldCgpLnRvcCAtIGNpSCkgLyAyKTtcblxuICAgICAgICAgICAgICAgIC8vXG4gICAgICAgICAgICAgICAgZWxlbWVudC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuYWRkQ2xhc3MoJ2JpZycpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnQucmVtb3ZlQ2xhc3MoJ3NtYWxsJyk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAndHJhbnNmb3JtJzogJ3JvdGF0ZVooMGRlZyknXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgbGVmdDogY2lDZW50ZXJYICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiBjaUNlbnRlclkgKyAncHgnLFxuICAgICAgICAgICAgICAgIH0sIHtcbiAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgZHVyYXRpb246IGRlbGF5LFxuICAgICAgICAgICAgICAgICAgICBlYXNpbmc6IGVhc2luZyxcbiAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBsb2FkTmV4dEFuaW1hdGlvblxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNvbnN0IGZsaXBTZWxlY3RlZENhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuYWRkQ2xhc3MoJ2Zyb250Jyk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5hZGRDbGFzcygnaGlnaGxpZ2h0Jyk7XG4gICAgICAgICAgICAgICAgbG9hZE5leHRBbmltYXRpb24oKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBzZW5kVG9QbGFjZWhvbGRlckNhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbGV0IHBPZmYgPSBwbGFjZWhvbGRlci5vZmZzZXQoKTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5yZW1vdmVDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5hZGRDbGFzcygnc21hbGwnKTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LmFuaW1hdGUoe1xuICAgICAgICAgICAgICAgICAgICBsZWZ0OiBwbGFjZWhvbGRlci5jc3MoJ2xlZnQnKSxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiBwbGFjZWhvbGRlci5jc3MoJ3RvcCcpLFxuICAgICAgICAgICAgICAgIH0sIHtcbiAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgZHVyYXRpb246IGRlbGF5LFxuICAgICAgICAgICAgICAgICAgICBlYXNpbmc6IGVhc2luZyxcbiAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgZWxlbWVudC5yZW1vdmVDbGFzcygnaGlnaGxpZ2h0Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbGVtZW50LmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJ3otaW5kZXgnOiBNYXRoLm1pbig5OSwgMSArIChwbGFjZWhvbGRlci5zaWJsaW5ncygnLnNlbGVjdGVkJykubGVuZ3RoKSksXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBsYWNlaG9sZGVyLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxvYWROZXh0QW5pbWF0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZGlzcGxheVNlbGVjdGVkQ2FyZEFuaW0pO1xuICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGZsaXBTZWxlY3RlZENhcmRBbmltKTtcbiAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChkZWxheUFuaW0oMjAwMCkpO1xuICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKHNlbmRUb1BsYWNlaG9sZGVyQ2FyZEFuaW0pO1xuICAgICAgICAgICAgbG9hZE5leHRBbmltYXRpb24oKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59XG5cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9UYXJvdEdhbWVcIiAvPlxuXG4vKipcbiAqIEBicmllZiBBbmltYXRlIHByb2Nlc3MgZm9yIFwic2luZ2xlXCIgZ2FtZSB0eXBlLlxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cbiAqIEBwYXJhbSBpbnQgZGVsYXkgVGhlIGFuaW1hdGlvbiBkZWxheS5cbiAqIEBwYXJhbSBzdHJpbmd8Ym9vbGVhbiBlYXNpbmcgRGVmYXVsdCBhbmltYXRpb24gZWFzaW5nIHRvIGJlIHBhc3NlZCB0byBqcXVlcnkuXG4gKiBAcGFyYW0gYW55IG9wdGlvbnMgRXh0cmEgb3B0aW9ucyB0byBwYXNzIHRvIGpxdWVyeS5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGFuaW1hdGlvbiBpcyBkb25lLlxuICovXG5mdW5jdGlvbiBhbmltUHJvY2Vzc1NpbmdsZShcbiAgICBnYW1lOiBUYXJvdEdhbWUsXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9ERUZBVUxUICogMixcbiAgICBlYXNpbmc6IHN0cmluZ3xib29sZWFuID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0VBU0lOR19ERUZBVUxULFxuICAgIG9wdGlvbnM6IGFueSA9IHt9XG4pIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2U8dm9pZD4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCBjb250YWluZXIgPSBnYW1lLmdldENvbnRhaW5lcigpO1xuXG4gICAgICAgIC8vIGNhbmNlbCBhbmltYXRpb25zIG9uIGNvbnRhaW5lciBmaXJzdFxuICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGNvbmZpZyA9IGdhbWUuZ2V0Q29uZmlnKCk7XG4gICAgICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcbiAgICAgICAgICAgIGNvbnN0IGNhcmRMYW5nID0gY29uZmlnLmNhcmQuQ2FyZExhbmc7XG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbXMgPSBjb25maWcuY2FyZEl0ZW1zO1xuICAgICAgICAgICAgY29uc3Qgc3RlcENvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwJyk7XG4gICAgICAgICAgICBjb25zdCBzdGVwRGVzY0NvbnRhaW5lciAgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtZGVzYycpO1xuICAgICAgICAgICAgY29uc3Qgc3RlcENvbnRDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcbiAgICAgICAgICAgIGNvbnN0IHNlbGVjdGVkQ2FyZHMgPSBnYW1lLmdldFNlbGVjdGVkQ2FyZEl0ZW1JZHMoKTtcbiAgICAgICAgICAgIGNvbnN0IGNhcmRJdGVtRWxlbWVudHM6IGFueVtdID0gW107XG4gICAgICAgICAgICBjb25zdCBGSU5BTF9DQVJEX1NJWkUgPSAnd2hlZWxzaXplJztcblxuICAgICAgICAgICAgLy8gY2xlYXIgdGhlIGNhcmRzIGNvbnRhaW5lciBhbmQgcGxhY2UgZWxlbWVudHNcbiAgICAgICAgICAgIHN0ZXBDb250Q29udGFpbmVyLmh0bWwoJycpO1xuXG4gICAgICAgICAgICBjb25zdCB3aGVlbCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXJvdW5kMVwiPjwvZGl2PicpO1xuICAgICAgICAgICAgc3RlcENvbnRDb250YWluZXIuYXBwZW5kKHdoZWVsKTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lciwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gJChjYXJkSXRlbUVsZW1lbnRzW2ldKTtcbiAgICAgICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vIGFkZCBjYXJkcyB3aXRoIDAgb3BhY2l0eVxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzZWxlY3RlZENhcmRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSBUZW1wbGF0ZUhlbHBlci5idWlsZENhcmRJdGVtSHRtbChjb25maWcsIHNlbGVjdGVkQ2FyZHNbaV0pO1xuICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDAsXG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMTAxLFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKCdmcm9udCcpO1xuICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKCdiaWcnKTtcbiAgICAgICAgICAgICAgICB3aGVlbC5hcHBlbmQoZWwpO1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMucHVzaChlbCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIGZ1bmN0aW9uIHRvIGNvbXB1dGUgcG9zaXRpb24gb2YgYSBjYXJkIG9uIGEgY2lyY2xlXG4gICAgICAgICAgICBjb25zdCBjb21wdXRlQ2FyZFBvc2l0aW9uID0gZnVuY3Rpb24gKGluZGV4OiBudW1iZXIpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBjcyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZShGSU5BTF9DQVJEX1NJWkUpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlclggPSB3aGVlbC53aWR0aCgpIC8gMjtcbiAgICAgICAgICAgICAgICBjb25zdCBjZW50ZXJZID0gd2hlZWwuaGVpZ2h0KCkgLyAyO1xuICAgICAgICAgICAgICAgIGNvbnN0IHJhZGl1cyA9IHdoZWVsLndpZHRoKCkgLyAyIC0gKFRlbXBsYXRlSGVscGVyLmlzTW9iaWxlKCkgPyAxMCA6IDUwKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjb3VudCA9IGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgdGhldGEgPSAtTWF0aC5QSSAqIDIgKiBpbmRleCAvIGNvdW50O1xuICAgICAgICAgICAgICAgIGNvbnN0IHgwID0gTWF0aC5zaW4odGhldGEpICogcmFkaXVzIC0gY3Mud2lkdGggLyAyO1xuICAgICAgICAgICAgICAgIGNvbnN0IHkwID0gLU1hdGguY29zKHRoZXRhKSAqIHJhZGl1cyAtY3MuaGVpZ2h0IC8gMjtcblxuICAgICAgICAgICAgICAgIGxldCBhY3R1YWxSb3RhdGlvbiA9IHRoZXRhICogMTgwIC8gTWF0aC5QSTtcbiAgICAgICAgICAgICAgICBpZiAoYWN0dWFsUm90YXRpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgd2hpbGUgKGFjdHVhbFJvdGF0aW9uID4gLTE1MCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYWN0dWFsUm90YXRpb24tPSAzNjA7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICdyb3RhdGVaKCcgKyBhY3R1YWxSb3RhdGlvbiArICdkZWcpJyxcbiAgICAgICAgICAgICAgICAgICAgbGVmdDogKGNlbnRlclggKyB4MCkgKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICB0b3A6IChjZW50ZXJZICsgeTApICsgJ3B4JyxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gcmVwb3NpdGlvbiBjYXJkc1xuICAgICAgICAgICAgY29uc3QgcmVwb3NpdGlvbkNhcmRzID0gZnVuY3Rpb24oZm9yY2UgPSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIGlmIChnYW1lLmdldFN0ZXAoKSAhPT0gVGFyb3RHYW1lU3RlcC5QUk9DRVNTX1NFTEVDVElPTiAmJiAhZm9yY2UpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBjb25zdCBzY1cgPSB3aGVlbC53aWR0aCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHNjSCA9IHdoZWVsLmhlaWdodCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNiUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgnYmlnJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJYID0gKHNjVyAtIGNiUy53aWR0aCkgLyAyO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWSA9IChzY0ggLSBjYlMuaGVpZ2h0KSAvIDI7XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gY2FyZEl0ZW1FbGVtZW50c1tpXTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGVsLmhhc0NsYXNzKCdiaWcnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZWZ0OiBjaUNlbnRlclggKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRvcDogY2lDZW50ZXJZICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgZWwuY3NzKGNvbXB1dGVDYXJkUG9zaXRpb24oaSkpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIHJlcG9zaXRpb25DYXJkcyh0cnVlKTtcbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyV2luZG93UmVzaXplRXZlbnQocmVwb3NpdGlvbkNhcmRzLCBmYWxzZSwgMCk7XG5cbiAgICAgICAgICAgIC8vIGFuaW1hdGlvbnNcbiAgICAgICAgICAgIGxldCBuZXh0Q2FyZEluZGV4ID0gMDtcbiAgICAgICAgICAgIGxldCBuZXh0QW5pbWF0aW9uID0gMDtcbiAgICAgICAgICAgIGxldCBhbmltYXRpb25zOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgY29uc3QgbG9hZE5leHRBbmltYXRpb24gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAobmV4dEFuaW1hdGlvbiA9PT0gYW5pbWF0aW9ucy5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcik7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgbmV4dEFuaW1hdGlvbisrO1xuICAgICAgICAgICAgICAgICAgICBhbmltYXRpb25zW25leHRBbmltYXRpb24gLSAxXSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBkZWxheUFuaW0gPSBmdW5jdGlvbihkZWxheTogbnVtYmVyKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cuc2V0VGltZW91dChsb2FkTmV4dEFuaW1hdGlvbiwgZGVsYXkpO1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3Qgc2hvd0NhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgY2FyZEl0ZW1MYW5nID0gY2FyZEl0ZW1zW3NlbGVjdGVkQ2FyZHNbbmV4dENhcmRJbmRleF1dLkNhcmRJdGVtTGFuZztcbiAgICAgICAgICAgICAgICBjb25zdCBrZXl3b3JkID0gZ2FtZS5nZXRSZXN1bHQoKS5zZWxlY3RlZF9rZXl3b3Jkc1tuZXh0Q2FyZEluZGV4XSB8fCBjYXJkSXRlbUxhbmcudGl0bGU7XG4gICAgICAgICAgICAgICAgYW5pbVNldFRleHQoc3RlcERlc2NDb250YWluZXIsIGNhcmRMYW5nLnN0ZXBfaW50ZXJwcmV0YXRpb25fZGVzY3JpcHRpb24ucmVwbGFjZSgnIyN0aXRsZSMjJywgY2FyZEl0ZW1MYW5nLnRpdGxlKS5yZXBsYWNlKCcjI2tleXdvcmQjIycsIGtleXdvcmQpKTtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gY2FyZEl0ZW1FbGVtZW50c1tuZXh0Q2FyZEluZGV4XTtcbiAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnVwZGF0ZVRyYW5zaXRpb25EZWxheShlbCwgWydvcGFjaXR5J10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBlbC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICBvcGFjaXR5OiAxXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIGRlbGF5KTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBwb3NpdGlvbkNhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSBjYXJkSXRlbUVsZW1lbnRzW25leHRDYXJkSW5kZXhdO1xuICAgICAgICAgICAgICAgIGVsLnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIudXBkYXRlVHJhbnNpdGlvbkRlbGF5KGVsLCBbJ3RyYW5zZm9ybSddLCBkZWxheSwgJ2N1YmljLWJlemllciguNiwtMC42MSwuNDQsLjk4KScsIC0xKTtcbiAgICAgICAgICAgICAgICBsZXQgY3NzID0gY29tcHV0ZUNhcmRQb3NpdGlvbihuZXh0Q2FyZEluZGV4KTtcbiAgICAgICAgICAgICAgICBsZXQgYW5pbSA9IHsgbGVmdDogY3NzLmxlZnQsIHRvcDogY3NzLnRvcCB9O1xuICAgICAgICAgICAgICAgIGRlbGV0ZSBjc3MubGVmdDtcbiAgICAgICAgICAgICAgICBkZWxldGUgY3NzLnRvcDtcbiAgICAgICAgICAgICAgICBlbC5jc3MoY3NzKTtcbiAgICAgICAgICAgICAgICBlbC5kZWxheShkZWxheSAqIDAuMikucHJvbWlzZSgpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbC5yZW1vdmVDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKEZJTkFMX0NBUkRfU0laRSk7XG4gICAgICAgICAgICAgICAgICAgIGVsLmFuaW1hdGUoYW5pbSwge1xuICAgICAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uOiBkZWxheSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGVhc2luZzogZWFzaW5nLFxuICAgICAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbC5jc3MoeyAnei1pbmRleCc6IDEgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9hZE5leHRBbmltYXRpb24oKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3Qgcm90YXRlV2hlZWxBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgYW5pbVNldFRleHQoc3RlcERlc2NDb250YWluZXIsIGdhbWUuZ2V0UmVzdWx0KCkudHIud2FpdF9wbGVhc2UpO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSB3aGVlbDtcbiAgICAgICAgICAgICAgICBjb25zdCBkZWxheSA9IDYwMDA7XG4gICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoZWwsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBlbC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICdyb3RhdGVaKC0zNjBkZWcpJ1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uLCBkZWxheSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgbmV4dENhcmRGbiA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIG5leHRDYXJkSW5kZXgrKztcbiAgICAgICAgICAgICAgICB3aW5kb3cuc2V0VGltZW91dChsb2FkTmV4dEFuaW1hdGlvbik7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKHNob3dDYXJkQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbShkZWxheSAvIDQpKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2gocG9zaXRpb25DYXJkQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbSgyMDApKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2gobmV4dENhcmRGbik7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBhbmltYXRpb25zLnB1c2gocm90YXRlV2hlZWxBbmltKTtcbiAgICAgICAgICAgIGxvYWROZXh0QW5pbWF0aW9uKCk7XG4gICAgICAgIH0pO1xuICAgIH0pO1xufVxuXG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgQW5pbWF0ZSBwcm9jZXNzIGZvciBcImZvcnR1bmVcIiBnYW1lIHR5cGUuXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1Qcm9jZXNzRm9ydHVuZShcbiAgICBnYW1lOiBUYXJvdEdhbWUsXG4gICAgZGVsYXk6IG51bWJlciA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9ERUxBWV9ERUZBVUxUICogMixcbiAgICBlYXNpbmc6IHN0cmluZ3xib29sZWFuID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0VBU0lOR19ERUZBVUxULFxuICAgIG9wdGlvbnM6IGFueSA9IHt9XG4pIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2U8dm9pZD4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCBjb250YWluZXIgPSBnYW1lLmdldENvbnRhaW5lcigpO1xuXG4gICAgICAgIC8vIGNhbmNlbCBhbmltYXRpb25zIG9uIGNvbnRhaW5lciBmaXJzdFxuICAgICAgICBUZW1wbGF0ZUhlbHBlci5jYW5jZWxBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGNvbmZpZyA9IGdhbWUuZ2V0Q29uZmlnKCk7XG4gICAgICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcbiAgICAgICAgICAgIGNvbnN0IGNhcmRMYW5nID0gY29uZmlnLmNhcmQuQ2FyZExhbmc7XG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbXMgPSBjb25maWcuY2FyZEl0ZW1zO1xuICAgICAgICAgICAgY29uc3Qgc3RlcENvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwJyk7XG4gICAgICAgICAgICBjb25zdCBzdGVwRGVzY0NvbnRhaW5lciAgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtZGVzYycpO1xuICAgICAgICAgICAgY29uc3Qgc3RlcENvbnRDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcbiAgICAgICAgICAgIGNvbnN0IHNlbGVjdGVkQ2FyZHMgPSBnYW1lLmdldFNlbGVjdGVkQ2FyZEl0ZW1JZHMoKTtcbiAgICAgICAgICAgIGNvbnN0IGNhcmRJdGVtRWxlbWVudHM6IGFueVtdID0gW107XG4gICAgICAgICAgICBjb25zdCBGSU5BTF9DQVJEX1NJWkUgPSAnd2hlZWxzaXplMic7XG5cbiAgICAgICAgICAgIC8vIGNsZWFyIHRoZSBjYXJkcyBjb250YWluZXIgYW5kIHBsYWNlIGVsZW1lbnRzXG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5odG1sKCcnKTtcblxuICAgICAgICAgICAgY29uc3Qgd2hlZWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtZ2FtZS1pbnRlcnByZXRhdGlvbi1yb3VuZDJcIj48L2Rpdj4nKTtcbiAgICAgICAgICAgIHN0ZXBDb250Q29udGFpbmVyLmFwcGVuZCh3aGVlbCk7XG4gICAgICAgICAgICBhbmltU2V0VGV4dChzdGVwRGVzY0NvbnRhaW5lciwgZ2FtZS5nZXRSZXN1bHQoKS50ci53YWl0X3BsZWFzZSk7XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlckFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbCA9ICQoY2FyZEl0ZW1FbGVtZW50c1tpXSk7XG4gICAgICAgICAgICAgICAgICAgIGVsLnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvLyBmdW5jdGlvbiB0byBjb21wdXRlIHBvc2l0aW9uIG9mIGEgY2FyZCBvbiBhIGNpcmNsZVxuICAgICAgICAgICAgY29uc3QgY29tcHV0ZUNhcmRQb3NpdGlvbiA9IGZ1bmN0aW9uIChpbmRleDogbnVtYmVyLCBjczogYW55LCByYWRpdXM6IG51bWJlciwgY2VudGVyT25FbGVtZW50OiBhbnkgPSBudWxsLCBkeCA9IDAsIGR5ID0gMCkge1xuICAgICAgICAgICAgICAgIGlmIChjZW50ZXJPbkVsZW1lbnQgPT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgY2VudGVyT25FbGVtZW50ID0gd2hlZWw7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlclggPSBjZW50ZXJPbkVsZW1lbnQud2lkdGgoKSAvIDI7XG4gICAgICAgICAgICAgICAgY29uc3QgY2VudGVyWSA9IGNlbnRlck9uRWxlbWVudC5oZWlnaHQoKSAvIDI7XG4gICAgICAgICAgICAgICAgY29uc3QgY291bnQgPSBzZWxlY3RlZENhcmRzLmxlbmd0aDtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHRoZXRhID0gLU1hdGguUEkgKiAyICogaW5kZXggLyBjb3VudDtcbiAgICAgICAgICAgICAgICBjb25zdCB4MCA9IE1hdGguc2luKHRoZXRhKSAqIHJhZGl1cyAtIGNzLndpZHRoIC8gMjtcbiAgICAgICAgICAgICAgICBjb25zdCB5MCA9IC1NYXRoLmNvcyh0aGV0YSkgKiByYWRpdXMgLSBjcy5oZWlnaHQgLyAyO1xuXG4gICAgICAgICAgICAgICAgbGV0IGFjdHVhbFJvdGF0aW9uID0gdGhldGEgKiAxODAgLyBNYXRoLlBJO1xuICAgICAgICAgICAgICAgIGlmIChhY3R1YWxSb3RhdGlvbikge1xuICAgICAgICAgICAgICAgICAgICB3aGlsZSAoYWN0dWFsUm90YXRpb24gPiAtMTUwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhY3R1YWxSb3RhdGlvbi09IDM2MDtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgIHRyYW5zZm9ybTogJ3JvdGF0ZVooJyArIGFjdHVhbFJvdGF0aW9uICsgJ2RlZyknLFxuICAgICAgICAgICAgICAgICAgICBsZWZ0OiAoZHggKyBjZW50ZXJYICsgeDApICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiAoZHkgKyBjZW50ZXJZICsgeTApICsgJ3B4JyxcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gYWRkIHdoZWVsIGNhcmRzXG4gICAgICAgICAgICBjb25zdCB3aGVlbENhcmRzOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzZWxlY3RlZENhcmRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtZ2FtZS1pbnRlcnByZXRhdGlvbi1yb3VuZDItY2FyZFwiPjwvZGl2PicpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNzID0gVGVtcGxhdGVIZWxwZXIuZ2V0RWxlbWVudFNpemUoJy50YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXJvdW5kMi1jYXJkJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgY3NzID0gY29tcHV0ZUNhcmRQb3NpdGlvbihpLCBjcywgY3MuaGVpZ2h0IC8gMik7XG4gICAgICAgICAgICAgICAgZWwuY3NzKGNzcyk7XG4gICAgICAgICAgICAgICAgd2hlZWwuYXBwZW5kKGVsKTtcbiAgICAgICAgICAgICAgICB3aGVlbENhcmRzLnB1c2goZWwpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBhZGQgY2FyZHMgd2l0aCAwIG9wYWNpdHlcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgc2VsZWN0ZWRDYXJkcy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gVGVtcGxhdGVIZWxwZXIuYnVpbGRDYXJkSXRlbUh0bWwoY29uZmlnLCBzZWxlY3RlZENhcmRzW2ldKTtcbiAgICAgICAgICAgICAgICBlbC5hZGRDbGFzcyhGSU5BTF9DQVJEX1NJWkUpO1xuICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKCdmcm9udCcpO1xuICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDAsXG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMSxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoZWwpO1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMucHVzaChlbCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIHJlcG9zaXRpb24gY2FyZHNcbiAgICAgICAgICAgIGNvbnN0IHJlcG9zaXRpb25DYXJkcyA9IGZ1bmN0aW9uKGZvcmNlID0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAoZ2FtZS5nZXRTdGVwKCkgIT09IFRhcm90R2FtZVN0ZXAuUFJPQ0VTU19TRUxFQ1RJT04gJiYgIWZvcmNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCB3aGVlbENhcmRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gd2hlZWxDYXJkc1tpXTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgY3MgPSBUZW1wbGF0ZUhlbHBlci5nZXRFbGVtZW50U2l6ZSgnLnRhcm90LWdhbWUtaW50ZXJwcmV0YXRpb24tcm91bmQyLWNhcmQnKTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgY3NzID0gY29tcHV0ZUNhcmRQb3NpdGlvbihpLCBjcywgY3MuaGVpZ2h0IC8gMik7XG4gICAgICAgICAgICAgICAgICAgIGVsLmNzcyhjc3MpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZWwgPSBjYXJkSXRlbUVsZW1lbnRzW2ldO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBjcyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZShGSU5BTF9DQVJEX1NJWkUpO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBjc3MgPSBjb21wdXRlQ2FyZFBvc2l0aW9uKGksIGNzLCB3aGVlbC53aWR0aCgpIC8gMiArIChUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpID8gMCA6IDEwKSwgc3RlcENvbnRDb250YWluZXIpO1xuICAgICAgICAgICAgICAgICAgICBkZWxldGUgY3NzWyd0cmFuc2Zvcm0nXTtcbiAgICAgICAgICAgICAgICAgICAgZWwuY3NzKGNzcyk7XG4gICAgICAgICAgICAgICAgICAgIGVsLmNzcyhjc3MpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICByZXBvc2l0aW9uQ2FyZHModHJ1ZSk7XG4gICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlcldpbmRvd1Jlc2l6ZUV2ZW50KHJlcG9zaXRpb25DYXJkcywgZmFsc2UsIDApO1xuXG4gICAgICAgICAgICAvLyBhbmltYXRpb25zXG4gICAgICAgICAgICBsZXQgbmV4dENhcmRJbmRleCA9IDA7XG4gICAgICAgICAgICBsZXQgZGVzdGluYXRpb25JbmRleCA9IDA7XG4gICAgICAgICAgICBsZXQgbmV4dEFuaW1hdGlvbiA9IDA7XG4gICAgICAgICAgICBsZXQgZG9uZUluZGV4ZXM6IG51bWJlcltdID0gW107XG4gICAgICAgICAgICBsZXQgYW5pbWF0aW9uczogYW55W10gPSBbXTtcbiAgICAgICAgICAgIGNvbnN0IGxvYWROZXh0QW5pbWF0aW9uID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgaWYgKG5leHRBbmltYXRpb24gPT09IGFuaW1hdGlvbnMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLmNhbmNlbEFuaW1hdGlvbk9uRWxlbWVudChjb250YWluZXIpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIG5leHRBbmltYXRpb24rKztcbiAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9uc1tuZXh0QW5pbWF0aW9uIC0gMV0oKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgZGVsYXlBbmltID0gZnVuY3Rpb24oZGVsYXk6IG51bWJlcikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNvbnN0IHBvc2l0aW9uQ2FyZEFuaW0gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBlbCA9IHdoZWVsQ2FyZHNbd2hlZWxDYXJkcy5sZW5ndGggLSAxIC0gbmV4dENhcmRJbmRleF07XG4gICAgICAgICAgICAgICAgY29uc3QgY2VsID0gY2FyZEl0ZW1FbGVtZW50c1tkZXN0aW5hdGlvbkluZGV4XTtcbiAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnVwZGF0ZVRyYW5zaXRpb25EZWxheShlbCwgWyd0cmFuc2Zvcm0nXSwgZGVsYXksICdjdWJpYy1iZXppZXIoLjYsLTAuNjEsLjQ0LC45OCknLCAtMSk7XG4gICAgICAgICAgICAgICAgZWwuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgJ3RyYW5zZm9ybSc6ICdyb3RhdGVaKDM2MGRlZyknXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgY29uc3QgY3MgPSBUZW1wbGF0ZUhlbHBlci5nZXRDYXJkSXRlbVNpemUoRklOQUxfQ0FSRF9TSVpFKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjc3MgPSBjb21wdXRlQ2FyZFBvc2l0aW9uKGRlc3RpbmF0aW9uSW5kZXgsIGNzLCB3aGVlbC53aWR0aCgpIC8gMiArIChUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpID8gMCA6IDEwKSwgbnVsbCwgMCwgMTgpO1xuICAgICAgICAgICAgICAgIGxldCBhbmltID0geyBsZWZ0OiBjc3MubGVmdCwgdG9wOiBjc3MudG9wIH07XG4gICAgICAgICAgICAgICAgZGVsZXRlIGNzcy5sZWZ0O1xuICAgICAgICAgICAgICAgIGRlbGV0ZSBjc3MudG9wO1xuICAgICAgICAgICAgICAgIGVsLmFuaW1hdGUoYW5pbSwge1xuICAgICAgICAgICAgICAgICAgICAuLi5vcHRpb25zLFxuICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogZGVsYXksXG4gICAgICAgICAgICAgICAgICAgIGVhc2luZzogZWFzaW5nLFxuICAgICAgICAgICAgICAgICAgICBhbHdheXM6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2VsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJ29wYWNpdHknOiAxXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJ29wYWNpdHknOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5jc3MoeyAnei1pbmRleCc6IDEgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsb2FkTmV4dEFuaW1hdGlvbigpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3Qgcm90YXRlV2hlZWxBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSB3aGVlbDtcbiAgICAgICAgICAgICAgICBjb25zdCBkZWxheSA9IDYwMDA7XG4gICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoZWwsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBlbC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06ICdyb3RhdGVaKC03MjBkZWcpJ1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uLCBkZWxheSk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgbmV4dENhcmRGbiA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGRvbmVJbmRleGVzLnB1c2goZGVzdGluYXRpb25JbmRleCk7XG4gICAgICAgICAgICAgICAgbmV4dENhcmRJbmRleCsrO1xuXG4gICAgICAgICAgICAgICAgbGV0IGluaXQgPSBkZXN0aW5hdGlvbkluZGV4O1xuICAgICAgICAgICAgICAgIGRlc3RpbmF0aW9uSW5kZXggPSBpbml0O1xuICAgICAgICAgICAgICAgIGxldCBjb3VudCA9IHdoZWVsQ2FyZHMubGVuZ3RoO1xuICAgICAgICAgICAgICAgIHdoaWxlIChkb25lSW5kZXhlcy5pbmRleE9mKGRlc3RpbmF0aW9uSW5kZXgpICE9PSAtMSkge1xuICAgICAgICAgICAgICAgICAgICBkZXN0aW5hdGlvbkluZGV4ID0gZGVzdGluYXRpb25JbmRleCArIGNvdW50O1xuICAgICAgICAgICAgICAgICAgICBpZiAoZGVzdGluYXRpb25JbmRleCA+PSB3aGVlbENhcmRzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvdW50ID09PSAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVzdGluYXRpb25JbmRleCA9IGluaXQ7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBjb3VudCA9IE1hdGguZmxvb3IoY291bnQgLyAyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlc3RpbmF0aW9uSW5kZXggPSAoaW5pdCArIGNvdW50KSAlIHdoZWVsQ2FyZHMubGVuZ3RoO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChwb3NpdGlvbkNhcmRBbmltKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZGVsYXlBbmltKDIwMCkpO1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChuZXh0Q2FyZEZuKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChyb3RhdGVXaGVlbEFuaW0pO1xuICAgICAgICAgICAgbG9hZE5leHRBbmltYXRpb24oKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59XG5cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9UYXJvdEdhbWVcIiAvPlxuXG4vKipcbiAqIEBicmllZiBBbmltYXRlIHByb2Nlc3MgZm9yIFwibG92ZVwiIGdhbWUgdHlwZS5cbiAqIEBwYXJhbSBUYXJvdEdhbWUgZ2FtZSBUaGUgZ2FtZSBvYmplY3QuXG4gKiBAcGFyYW0gaW50IGRlbGF5IFRoZSBhbmltYXRpb24gZGVsYXkuXG4gKiBAcGFyYW0gc3RyaW5nfGJvb2xlYW4gZWFzaW5nIERlZmF1bHQgYW5pbWF0aW9uIGVhc2luZyB0byBiZSBwYXNzZWQgdG8ganF1ZXJ5LlxuICogQHBhcmFtIGFueSBvcHRpb25zIEV4dHJhIG9wdGlvbnMgdG8gcGFzcyB0byBqcXVlcnkuXG4gKiBAcmV0dXJuIFByb21pc2UgQSBwcm9taXNlIGZvciB3aGVuIHRoZSBhbmltYXRpb24gaXMgZG9uZS5cbiAqL1xuZnVuY3Rpb24gYW5pbVByb2Nlc3NMb3ZlKFxuICAgIGdhbWU6IFRhcm90R2FtZSxcbiAgICBkZWxheTogbnVtYmVyID0gVGVtcGxhdGVIZWxwZXIuQU5JTUFUSU9OX0RFTEFZX0RFRkFVTFQsXG4gICAgZWFzaW5nOiBzdHJpbmd8Ym9vbGVhbiA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9FQVNJTkdfREVGQVVMVCxcbiAgICBvcHRpb25zOiBhbnkgPSB7fVxuKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcblxuICAgICAgICAvLyBjYW5jZWwgYW5pbWF0aW9ucyBvbiBjb250YWluZXIgZmlyc3RcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBjb25maWcgPSBnYW1lLmdldENvbmZpZygpO1xuICAgICAgICAgICAgY29uc3QgY2FyZCA9IGNvbmZpZy5jYXJkLkNhcmQ7XG4gICAgICAgICAgICBjb25zdCBjYXJkTGFuZyA9IGNvbmZpZy5jYXJkLkNhcmRMYW5nO1xuICAgICAgICAgICAgY29uc3QgY2FyZEl0ZW1zID0gY29uZmlnLmNhcmRJdGVtcztcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250YWluZXIgPSBjb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcCcpO1xuICAgICAgICAgICAgY29uc3Qgc3RlcERlc2NDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWRlc2MnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1jb250Jyk7XG4gICAgICAgICAgICBjb25zdCBzZWxlY3RlZENhcmRzID0gZ2FtZS5nZXRTZWxlY3RlZENhcmRJdGVtSWRzKCk7XG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbUVsZW1lbnRzOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgY29uc3QgRklOQUxfQ0FSRF9TSVpFID0gJyc7XG5cbiAgICAgICAgICAgIC8vIGNsZWFyIHRoZSBjYXJkcyBjb250YWluZXIgYW5kIHBsYWNlIGVsZW1lbnRzXG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5odG1sKCcnKTtcblxuICAgICAgICAgICAgY29uc3QgZmxleGNvbnQgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtZ2FtZS1pbnRlcnByZXRhdGlvbi1mbGV4Y29udFwiPjwvZGl2PicpO1xuICAgICAgICAgICAgc3RlcENvbnRDb250YWluZXIuYXBwZW5kKGZsZXhjb250KTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lciwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBjYXJkSXRlbUVsZW1lbnRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gJChjYXJkSXRlbUVsZW1lbnRzW2ldKTtcbiAgICAgICAgICAgICAgICAgICAgZWwuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vIGFkZCBjYXJkc1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzZWxlY3RlZENhcmRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSBUZW1wbGF0ZUhlbHBlci5idWlsZENhcmRJdGVtSHRtbChjb25maWcsIHNlbGVjdGVkQ2FyZHNbaV0pO1xuICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDEsXG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMTAxLFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGVsLnJlbW92ZUNsYXNzKCdiaWcnKTtcbiAgICAgICAgICAgICAgICBmbGV4Y29udC5hcHBlbmQoZWwpO1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMucHVzaChlbCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIGFuaW1hdGlvbnNcbiAgICAgICAgICAgIGxldCBuZXh0Q2FyZEluZGV4ID0gMDtcbiAgICAgICAgICAgIGxldCBuZXh0QW5pbWF0aW9uID0gMDtcbiAgICAgICAgICAgIGxldCBhbmltYXRpb25zOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgY29uc3QgbG9hZE5leHRBbmltYXRpb24gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAobmV4dEFuaW1hdGlvbiA9PT0gYW5pbWF0aW9ucy5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcik7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgbmV4dEFuaW1hdGlvbisrO1xuICAgICAgICAgICAgICAgICAgICBhbmltYXRpb25zW25leHRBbmltYXRpb24gLSAxXSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBkZWxheUFuaW0gPSBmdW5jdGlvbihkZWxheTogbnVtYmVyKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cuc2V0VGltZW91dChsb2FkTmV4dEFuaW1hdGlvbiwgZGVsYXkpO1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgZmxpcENhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSBjYXJkSXRlbUVsZW1lbnRzW25leHRDYXJkSW5kZXhdO1xuICAgICAgICAgICAgICAgIGVsLnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgZWwudG9nZ2xlQ2xhc3MoJ2Zyb250Jyk7XG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIDApO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNvbnN0IGZsaXBBbGxDYXJkc0FuaW0gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZWwgPSBjYXJkSXRlbUVsZW1lbnRzW2ldO1xuICAgICAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICBlbC50b2dnbGVDbGFzcygnZnJvbnQnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIDApO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGNvbnN0IHJlc2V0TmV4dENhcmRGbiA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIG5leHRDYXJkSW5kZXggPSAwO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBuZXh0Q2FyZEZuID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbmV4dENhcmRJbmRleCsrO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBmb3IgKGxldCBqID0gMDsgaiA8IDI7ICsraikge1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChyZXNldE5leHRDYXJkRm4pO1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZmxpcENhcmRBbmltKTtcbiAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbShkZWxheSkpO1xuICAgICAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZmxpcENhcmRBbmltKTtcbiAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKG5leHRDYXJkRm4pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZmxpcEFsbENhcmRzQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbShkZWxheSkpO1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChmbGlwQWxsQ2FyZHNBbmltKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZGVsYXlBbmltKGRlbGF5KSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBsb2FkTmV4dEFuaW1hdGlvbigpO1xuICAgICAgICB9KTtcbiAgICB9KTtcbn1cblxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL0dsb2JhbHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2hlbHBlcnMvVGVtcGxhdGVIZWxwZXJcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL1Rhcm90R2FtZVwiIC8+XG5cbi8qKlxuICogQGJyaWVmIEFuaW1hdGUgcHJvY2VzcyBmb3IgXCJ5ZXMgbm9cIiBnYW1lIHR5cGUuXG4gKiBAcGFyYW0gVGFyb3RHYW1lIGdhbWUgVGhlIGdhbWUgb2JqZWN0LlxuICogQHBhcmFtIGludCBkZWxheSBUaGUgYW5pbWF0aW9uIGRlbGF5LlxuICogQHBhcmFtIHN0cmluZ3xib29sZWFuIGVhc2luZyBEZWZhdWx0IGFuaW1hdGlvbiBlYXNpbmcgdG8gYmUgcGFzc2VkIHRvIGpxdWVyeS5cbiAqIEBwYXJhbSBhbnkgb3B0aW9ucyBFeHRyYSBvcHRpb25zIHRvIHBhc3MgdG8ganF1ZXJ5LlxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgYW5pbWF0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGFuaW1Qcm9jZXNzWWVzTm8oXG4gICAgZ2FtZTogVGFyb3RHYW1lLFxuICAgIGRlbGF5OiBudW1iZXIgPSBUZW1wbGF0ZUhlbHBlci5BTklNQVRJT05fREVMQVlfREVGQVVMVCAqIDIsXG4gICAgZWFzaW5nOiBzdHJpbmd8Ym9vbGVhbiA9IFRlbXBsYXRlSGVscGVyLkFOSU1BVElPTl9FQVNJTkdfREVGQVVMVCxcbiAgICBvcHRpb25zOiBhbnkgPSB7fVxuKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcblxuICAgICAgICAvLyBjYW5jZWwgYW5pbWF0aW9ucyBvbiBjb250YWluZXIgZmlyc3RcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcikudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCByZXN1bHQgPSBnYW1lLmdldFJlc3VsdCgpO1xuICAgICAgICAgICAgY29uc3QgY29uZmlnID0gZ2FtZS5nZXRDb25maWcoKTtcbiAgICAgICAgICAgIGNvbnN0IGNhcmQgPSBjb25maWcuY2FyZC5DYXJkO1xuICAgICAgICAgICAgY29uc3QgY2FyZExhbmcgPSBjb25maWcuY2FyZC5DYXJkTGFuZztcbiAgICAgICAgICAgIGNvbnN0IGNhcmRJdGVtcyA9IGNvbmZpZy5jYXJkSXRlbXM7XG4gICAgICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBUaXRsZUNvbnRhaW5lciAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC10aXRsZScpO1xuICAgICAgICAgICAgY29uc3Qgc3RlcERlc2NDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWRlc2MnKTtcbiAgICAgICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1jb250Jyk7XG4gICAgICAgICAgICBjb25zdCBzZWxlY3RlZENhcmRzID0gZ2FtZS5nZXRTZWxlY3RlZENhcmRJdGVtSWRzKCk7XG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbUVsZW1lbnRzOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgY29uc3QgRklOQUxfQ0FSRF9TSVpFID0gJ3NtYWxsJztcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGxldCBjb3VudFllcyA9IDA7XG4gICAgICAgICAgICBsZXQgY291bnRObyA9IDA7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHJlc3VsdC5jYXJkX2l0ZW1fYW5zd2VyLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgciA9IHJlc3VsdC5jYXJkX2l0ZW1fYW5zd2VyW2ldO1xuICAgICAgICAgICAgICAgIGlmIChyKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvdW50WWVzKys7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgY291bnRObysrO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gY2xlYXIgdGhlIGNhcmRzIGNvbnRhaW5lciBhbmQgcGxhY2UgZWxlbWVudHNcbiAgICAgICAgICAgIHN0ZXBDb250Q29udGFpbmVyLmh0bWwoJycpO1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3QgeWVzQ29udCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXllcy10ZXh0XCI+JyArIHJlc3VsdC50ci55ZXMgKyAnPC9kaXY+Jyk7XG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoeWVzQ29udCk7XG4gICAgICAgICAgICBjb25zdCBub0NvbnQgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtZ2FtZS1pbnRlcnByZXRhdGlvbi1uby10ZXh0XCI+JyArIHJlc3VsdC50ci5ubyArICc8L2Rpdj4nKTtcbiAgICAgICAgICAgIHN0ZXBDb250Q29udGFpbmVyLmFwcGVuZChub0NvbnQpO1xuXG4gICAgICAgICAgICBjb25zdCBzY2FsZSA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXNjYWxlXCI+PC9kaXY+Jyk7XG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoc2NhbGUpO1xuXG4gICAgICAgICAgICBjb25zdCBzY2FsZUJhciA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXNjYWxlLWJhclwiPjwvZGl2PicpO1xuICAgICAgICAgICAgc2NhbGUuYXBwZW5kKHNjYWxlQmFyKTtcblxuICAgICAgICAgICAgY29uc3Qgc2NhbGVCYXNlID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LWdhbWUtaW50ZXJwcmV0YXRpb24tc2NhbGUtYmFzZVwiPjwvZGl2PicpO1xuICAgICAgICAgICAgc2NhbGUuYXBwZW5kKHNjYWxlQmFzZSk7XG5cbiAgICAgICAgICAgIGNvbnN0IHNjYWxlSW5kID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LWdhbWUtaW50ZXJwcmV0YXRpb24tc2NhbGUtaW5kXCI+PC9kaXY+Jyk7XG4gICAgICAgICAgICBzY2FsZS5hcHBlbmQoc2NhbGVJbmQpO1xuXG4gICAgICAgICAgICBjb25zdCBzY2FsZUNvbnRMID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LWdhbWUtaW50ZXJwcmV0YXRpb24tc2NhbGUtY29udGxcIj48L2Rpdj4nKTtcbiAgICAgICAgICAgIHNjYWxlLmFwcGVuZChzY2FsZUNvbnRMKTtcblxuICAgICAgICAgICAgY29uc3Qgc2NhbGVDb250UiA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXNjYWxlLWNvbnRyXCI+PC9kaXY+Jyk7XG4gICAgICAgICAgICBzY2FsZS5hcHBlbmQoc2NhbGVDb250Uik7XG5cblxuICAgICAgICAgICAgLy8gZnVuY3Rpb24gdG8gcm90YXRlIHRoZSBzY2FsZSAocCBpbiByYW5nZSAtMSAuLiAxKVxuICAgICAgICAgICAgY29uc3Qgc2NhbGVDb250VG9wQXRPID0gcGFyc2VJbnQoc2NhbGVDb250TC5jc3MoJ3RvcCcpKTtcbiAgICAgICAgICAgIGNvbnN0IHNldFNjYWxlVmFsdWUgPSBmdW5jdGlvbihwOiBudW1iZXIpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBNQVhfUk9UX0lORCA9IDQ1O1xuICAgICAgICAgICAgICAgIHNjYWxlSW5kLmNzcyh7IHRyYW5zZm9ybTogJ3JvdGF0ZVooJyArICgtcCAqIE1BWF9ST1RfSU5EKSArICdkZWcpJyB9KTtcblxuICAgICAgICAgICAgICAgIGNvbnN0IE1BWF9ST1QgPSAzMDtcbiAgICAgICAgICAgICAgICBzY2FsZUJhci5jc3MoeyB0cmFuc2Zvcm06ICdyb3RhdGVaKCcgKyAocCAqIE1BWF9ST1QpICsgJ2RlZyknIH0pO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgcmFkaXVzID0gVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKSA/ICgxOTIgKiAwLjYpIDogMTkyO1xuICAgICAgICAgICAgICAgIGNvbnN0IGFscGhhID0gcCAqIE1BWF9ST1QgKiBNYXRoLlBJIC8gMTgwO1xuICAgICAgICAgICAgICAgIGNvbnN0IHkgPSBNYXRoLnNpbihhbHBoYSkgKiByYWRpdXM7XG4gICAgICAgICAgICAgICAgY29uc3QgeCA9IChNYXRoLmNvcyhhbHBoYSkgLSAxKSAqIHJhZGl1cztcbiAgICAgICAgICAgICAgICBzY2FsZUNvbnRMLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIHRvcDogKHNjYWxlQ29udFRvcEF0TyAtIHkpICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiAndHJhbnNsYXRlWCgnICsgKC14KSArICdweCknXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgc2NhbGVDb250Ui5jc3Moe1xuICAgICAgICAgICAgICAgICAgICB0b3A6IChzY2FsZUNvbnRUb3BBdE8gKyB5KSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgIHRyYW5zZm9ybTogJ3RyYW5zbGF0ZVgoJyArIHggKyAncHgpJ1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gY29tcHV0ZXMgY2FyZCBwb3NpdGlvbiBvbiBzY2FsZVxuICAgICAgICAgICAgY29uc3QgY29tcHV0ZVBvc2l0aW9uT25TY2FsZSA9IGZ1bmN0aW9uKHNjYWxlOiBhbnksIGNhcmRFbDogYW55LCBjYXJkSW5kZXg6IG51bWJlciwgY2FyZHNDb3VudDogbnVtYmVyKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgY3MgPSBUZW1wbGF0ZUhlbHBlci5nZXRDYXJkSXRlbVNpemUoRklOQUxfQ0FSRF9TSVpFKTtcbiAgICAgICAgICAgICAgICBsZXQgdG9wID0gc2NhbGUub2Zmc2V0KCkudG9wICsgc2NhbGUuaGVpZ2h0KCkgLSBzY2FsZS5wYXJlbnQoKS5wYXJlbnQoKS5vZmZzZXQoKS50b3AgLSBjcy5oZWlnaHQ7XG4gICAgICAgICAgICAgICAgbGV0IGxlZnQgPSBzY2FsZS5vZmZzZXQoKS5sZWZ0ICsgc2NhbGUud2lkdGgoKSAtIHNjYWxlLnBhcmVudCgpLnBhcmVudCgpLm9mZnNldCgpLmxlZnQgLSBjcy53aWR0aDtcbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICB0b3A6IHRvcCArICdweCcsXG4gICAgICAgICAgICAgICAgICAgIGxlZnQ6IGxlZnQgKyAncHgnLFxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIucmVnaXN0ZXJBbmltYXRpb25PbkVsZW1lbnQoY29udGFpbmVyLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZWwgPSAkKGNhcmRJdGVtRWxlbWVudHNbaV0pO1xuICAgICAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gYWRkIGNhcmRzIG9uIGEgbGluZVxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzZWxlY3RlZENhcmRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZWwgPSBUZW1wbGF0ZUhlbHBlci5idWlsZENhcmRJdGVtSHRtbChjb25maWcsIHNlbGVjdGVkQ2FyZHNbaV0pO1xuICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDEsXG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMSxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBlbC5hZGRDbGFzcygnZnJvbnQnKTtcbiAgICAgICAgICAgICAgICBlbC5yZW1vdmVDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgZWwuYWRkQ2xhc3MoRklOQUxfQ0FSRF9TSVpFKTtcbiAgICAgICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoZWwpO1xuICAgICAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMucHVzaChlbCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIHJlcG9zaXRpb24gY2FyZHNcbiAgICAgICAgICAgIGNvbnN0IHJlcG9zaXRpb25DYXJkcyA9IGZ1bmN0aW9uKGZvcmNlID0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAoZ2FtZS5nZXRTdGVwKCkgIT09IFRhcm90R2FtZVN0ZXAuUFJPQ0VTU19TRUxFQ1RJT04gJiYgIWZvcmNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgY29uc3Qgc2NXID0gc3RlcENvbnRDb250YWluZXIud2lkdGgoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBzY0ggPSBzdGVwQ29udENvbnRhaW5lci5oZWlnaHQoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjYlMgPSBUZW1wbGF0ZUhlbHBlci5nZXRDYXJkSXRlbVNpemUoRklOQUxfQ0FSRF9TSVpFKTtcbiAgICAgICAgICAgICAgICBjb25zdCBwYWRXID0gTWF0aC5taW4oMjAsIHNjVyAvIGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoIC0gY2JTLndpZHRoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBhY3R1YWxXID0gY2JTLndpZHRoICsgcGFkVztcbiAgICAgICAgICAgICAgICBjb25zdCBjaUNlbnRlclggPSAoc2NXIC0gYWN0dWFsVyAqIGNhcmRJdGVtRWxlbWVudHMubGVuZ3RoICsgcGFkVykgLyAyO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWSA9IDIwO1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbCA9IGNhcmRJdGVtRWxlbWVudHNbaV07XG4gICAgICAgICAgICAgICAgICAgIGxldCBjZW50ZXJPblNjYWxlID0gbnVsbDtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFlbC5oYXNDbGFzcygnYmlnJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGVmdDogY2lDZW50ZXJYICsgaSAqIGFjdHVhbFcgKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRvcDogY2lDZW50ZXJZICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIHJlcG9zaXRpb25DYXJkcyh0cnVlKTtcbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyV2luZG93UmVzaXplRXZlbnQocmVwb3NpdGlvbkNhcmRzLCBmYWxzZSwgMCk7XG5cbiAgICAgICAgICAgIC8vIGFuaW1hdGlvbnNcbiAgICAgICAgICAgIGxldCBuZXh0Q2FyZEluZGV4ID0gMDtcbiAgICAgICAgICAgIGxldCBuZXh0QW5pbWF0aW9uID0gMDtcbiAgICAgICAgICAgIGxldCBhbmltYXRpb25zOiBhbnlbXSA9IFtdO1xuICAgICAgICAgICAgY29uc3QgbG9hZE5leHRBbmltYXRpb24gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAobmV4dEFuaW1hdGlvbiA9PT0gYW5pbWF0aW9ucy5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIuY2FuY2VsQW5pbWF0aW9uT25FbGVtZW50KGNvbnRhaW5lcik7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgbmV4dEFuaW1hdGlvbisrO1xuICAgICAgICAgICAgICAgICAgICBhbmltYXRpb25zW25leHRBbmltYXRpb24gLSAxXSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBkZWxheUFuaW0gPSBmdW5jdGlvbihkZWxheTogbnVtYmVyKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cuc2V0VGltZW91dChsb2FkTmV4dEFuaW1hdGlvbiwgZGVsYXkpO1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvL1xuICAgICAgICAgICAgY29uc3Qgc2hvd0NhcmRBbmltID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2hvd0RlbGF5ID0gZGVsYXk7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBzY1cgPSBzdGVwQ29udENvbnRhaW5lci53aWR0aCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHNjSCA9IHN0ZXBDb250Q29udGFpbmVyLmhlaWdodCgpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNiUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgnYmlnJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgY2lDZW50ZXJYID0gKHNjVyAtIGNiUy53aWR0aCkgLyAyO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWSA9IChzY0ggLSBjYlMuaGVpZ2h0KSAvIDI7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBlbGVtZW50ID0gY2FyZEl0ZW1FbGVtZW50c1tuZXh0Q2FyZEluZGV4XTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LnN0b3AodHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5hZGRDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5yZW1vdmVDbGFzcygnc21hbGwnKTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICd0cmFuc2Zvcm0nOiAncm90YXRlWigwZGVnKScsXG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMTAxXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICAgICAgbGVmdDogY2lDZW50ZXJYICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiBjaUNlbnRlclkgKyAncHgnLFxuICAgICAgICAgICAgICAgIH0sIHtcbiAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgZHVyYXRpb246IHNob3dEZWxheSxcbiAgICAgICAgICAgICAgICAgICAgZWFzaW5nOiBlYXNpbmcsXG4gICAgICAgICAgICAgICAgICAgIGFsd2F5czogbG9hZE5leHRBbmltYXRpb25cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBsZXQgY291bnRQbGFjZWRZZXMgPSAwO1xuICAgICAgICAgICAgbGV0IGNvdW50UGxhY2VkTm8gPSAwO1xuICAgICAgICAgICAgY29uc3QgcG9zaXRpb25DYXJkQW5pbSA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGNvbnN0IGVsQW5zd2VyID0gISFyZXN1bHQuY2FyZF9pdGVtX2Fuc3dlcltuZXh0Q2FyZEluZGV4XTtcbiAgICAgICAgICAgICAgICBjb25zdCBlbCA9IGNhcmRJdGVtRWxlbWVudHNbbmV4dENhcmRJbmRleF07XG4gICAgICAgICAgICAgICAgY29uc3Qgc2NhbGVDb250ID0gZWxBbnN3ZXIgPyBzY2FsZUNvbnRSIDogc2NhbGVDb250TDtcbiAgICAgICAgICAgICAgICBlbC5zdG9wKHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnVwZGF0ZVRyYW5zaXRpb25EZWxheShlbCwgWyd0cmFuc2Zvcm0nXSwgZGVsYXksICdjdWJpYy1iZXppZXIoLjYsLTAuNjEsLjQ0LC45OCknLCAtMSk7XG4gICAgICAgICAgICAgICAgbGV0IGNzcyA9IGNvbXB1dGVQb3NpdGlvbk9uU2NhbGUoc2NhbGVDb250LCBlbCwgZWxBbnN3ZXIgPyBjb3VudFBsYWNlZFllcyA6IGNvdW50UGxhY2VkTm8sIGVsQW5zd2VyID8gY291bnRZZXMgOiBjb3VudE5vKTtcbiAgICAgICAgICAgICAgICBsZXQgYW5pbSA9IHsgbGVmdDogY3NzLmxlZnQsIHRvcDogY3NzLnRvcCB9O1xuICAgICAgICAgICAgICAgIGRlbGV0ZSBjc3MubGVmdDtcbiAgICAgICAgICAgICAgICBkZWxldGUgY3NzLnRvcDtcbiAgICAgICAgICAgICAgICBlbC5jc3MoY3NzKTtcbiAgICAgICAgICAgICAgICBlbC5kZWxheShkZWxheSAqIDAuMikucHJvbWlzZSgpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBlbC5yZW1vdmVDbGFzcygnYmlnJyk7XG4gICAgICAgICAgICAgICAgICAgIGVsLmFkZENsYXNzKEZJTkFMX0NBUkRfU0laRSk7XG4gICAgICAgICAgICAgICAgICAgIGVsLmFuaW1hdGUoYW5pbSwge1xuICAgICAgICAgICAgICAgICAgICAgICAgLi4ub3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uOiBkZWxheSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGVhc2luZzogZWFzaW5nLFxuICAgICAgICAgICAgICAgICAgICAgICAgYWx3YXlzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbC5jc3MoeyAnei1pbmRleCc6IDEgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWwucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NhbGVDb250LmFwcGVuZChlbCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGVsQW5zd2VyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvdW50UGxhY2VkWWVzKys7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY291bnRQbGFjZWRObysrO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2FkTmV4dEFuaW1hdGlvbigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCByb3RhdGVTY2FsZUFuaW0gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoc2NhbGVJbmQsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoc2NhbGVCYXIsIFsndHJhbnNmb3JtJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoc2NhbGVDb250TCwgWyd0cmFuc2Zvcm0nLCAndG9wJ10sIGRlbGF5KTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci51cGRhdGVUcmFuc2l0aW9uRGVsYXkoc2NhbGVDb250UiwgWyd0cmFuc2Zvcm0nLCAndG9wJ10sIGRlbGF5KTtcblxuICAgICAgICAgICAgICAgIHNldFNjYWxlVmFsdWUoKGNvdW50UGxhY2VkWWVzIC0gY291bnRQbGFjZWRObykgLyAoY291bnRZZXMgKyBjb3VudE5vKSk7XG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIGRlbGF5KTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBuZXh0Q2FyZEZuID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgbmV4dENhcmRJbmRleCsrO1xuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGxvYWROZXh0QW5pbWF0aW9uKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIC8vXG4gICAgICAgICAgICBjb25zdCBzaG93UmVzdWx0ID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2hvd0RlbGF5ID0gMjAwMDtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHNjVyA9IHN0ZXBDb250Q29udGFpbmVyLndpZHRoKCk7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2NIID0gc3RlcENvbnRDb250YWluZXIuaGVpZ2h0KCk7XG4gICAgICAgICAgICAgICAgY29uc3QgY2JTID0gVGVtcGxhdGVIZWxwZXIuZ2V0RWxlbWVudFNpemUoJy50YXJvdC1nYW1lLWludGVycHJldGF0aW9uLXllcy10ZXh0LmJpZycpO1xuICAgICAgICAgICAgICAgIGNvbnN0IGNpQ2VudGVyWCA9IChzY1cgLSBjYlMud2lkdGgpIC8gMjtcbiAgICAgICAgICAgICAgICBjb25zdCBjaUNlbnRlclkgPSAoc2NIIC0gY2JTLmhlaWdodCkgLyAyO1xuXG4gICAgICAgICAgICAgICAgYW5pbVNldFRleHQoc3RlcFRpdGxlQ29udGFpbmVyLCByZXN1bHQudHIuYW5zd2VyX2lzKTtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGVsZW1lbnQgPSBjb3VudFllcyA+IGNvdW50Tm8gPyB5ZXNDb250IDogbm9Db250O1xuICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnVwZGF0ZVRyYW5zaXRpb25EZWxheShlbGVtZW50LCB0cnVlLCBzaG93RGVsYXkpO1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuc3RvcCh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LmFkZENsYXNzKCdiaWcnKTtcbiAgICAgICAgICAgICAgICBlbGVtZW50LmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICd6LWluZGV4JzogMTAxXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgd2luZG93LnNldFRpbWVvdXQobG9hZE5leHRBbmltYXRpb24sIHNob3dEZWxheSArIDMwMDApO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy9cbiAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChkZWxheUFuaW0oZGVsYXkpKTtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgY2FyZEl0ZW1FbGVtZW50cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChzaG93Q2FyZEFuaW0pO1xuICAgICAgICAgICAgICAgIGFuaW1hdGlvbnMucHVzaChkZWxheUFuaW0oZGVsYXkpKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2gocG9zaXRpb25DYXJkQW5pbSk7XG4gICAgICAgICAgICAgICAgYW5pbWF0aW9ucy5wdXNoKGRlbGF5QW5pbSgyMDApKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2gocm90YXRlU2NhbGVBbmltKTtcbiAgICAgICAgICAgICAgICBhbmltYXRpb25zLnB1c2gobmV4dENhcmRGbik7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBhbmltYXRpb25zLnB1c2goZGVsYXlBbmltKGRlbGF5KSk7XG4gICAgICAgICAgICBhbmltYXRpb25zLnB1c2goc2hvd1Jlc3VsdCk7XG4gICAgICAgICAgICBsb2FkTmV4dEFuaW1hdGlvbigpO1xuICAgICAgICB9KTtcbiAgICB9KTtcbn1cblxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbVNldFRleHRcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbUluaXRpYWxTaG93Q2FyZHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbURpc3RyaWJ1dGVDYXJkc0xpbmVcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbURpc3RyaWJ1dGVDYXJkc1NrZXdlZExpbmVcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbURpc3RyaWJ1dGVDYXJkc1R3b0xpbmVzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL2FuaW1EaXN0cmlidXRlQ2FyZHNBcmNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbVNodWZmbGVDYXJkc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9hbmltU2VsZWN0Q2FyZFwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9hbmltUHJvY2Vzc1NpbmdsZVwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9hbmltUHJvY2Vzc0ZvcnR1bmVcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vYW5pbVByb2Nlc3NMb3ZlXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL2FuaW1Qcm9jZXNzWWVzTm9cIiAvPlxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL0dsb2JhbHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2VudW1zL1Rhcm90R2FtZVN0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2hlbHBlcnMvVGVtcGxhdGVIZWxwZXJcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL1Rhcm90R2FtZVwiIC8+XG5cbi8qKlxuICogQGJyaWVmIERvZXMgbWFpbiBpbml0aWFsaXphdGlvbnMgdG8gdGhlIGluaXRpYWwgc3RlcC5cbiAqIEBwYXJhbSBUYXJvdEdhbWUgZ2FtZSBUaGUgZ2FtZSBvYmplY3QuXG4gKiBAcmV0dXJuIFByb21pc2UgQSBwcm9taXNlIGZvciB3aGVuIHRoZSBmdW5jdGlvbiBpcyBkb25lLlxuICovXG5mdW5jdGlvbiBpbml0aWFsU3RlcChnYW1lOiBUYXJvdEdhbWUpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2U8dm9pZD4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCBjb25maWcgPSBnYW1lLmdldENvbmZpZygpO1xuICAgICAgICBjb25zdCBjb250YWluZXIgPSBnYW1lLmdldENvbnRhaW5lcigpO1xuICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcbiAgICAgICAgY29uc3QgY2FyZExhbmcgPSBjb25maWcuY2FyZC5DYXJkTGFuZztcblxuICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgY29uc3Qgc3RlcFRpdGxlQ29udGFpbmVyICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLXRpdGxlJyk7XG4gICAgICAgIGNvbnN0IHN0ZXBEZXNjQ29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1kZXNjJyk7XG4gICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1jb250Jyk7XG5cbiAgICAgICAgLy8gZW5zdXJlIHdlIGhhdmUgdGhlIHByb3BlciBjbGFzc1xuICAgICAgICBzdGVwQ29udGFpbmVyLmF0dHIoJ2NsYXNzJywgJ3Rhcm90LWdhbWUtc3RlcCB0YXJvdC1nYW1lLXN0ZXAtY2hvb3NlJyk7XG5cbiAgICAgICAgLy8gbG9hZCBtYWluIGNzc1xuICAgICAgICBpZiAoY2FyZC5tYWluX2Nzcykge1xuICAgICAgICAgICAgY29udGFpbmVyLnByZXBlbmQoJzxzdHlsZT4nICsgY2FyZC5tYWluX2NzcyArICc8L3N0eWxlPicpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gbG9hZCBiYWNrZ3JvdW5kXG4gICAgICAgIGNvbnN0IGxvYWRCYWNrZ3JvdW5kRm4gPSAoZm9yY2UgPSBmYWxzZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgaXNNb2JpbGUgPSBUZW1wbGF0ZUhlbHBlci5pc01vYmlsZSgpO1xuICAgICAgICAgICAgaWYgKGlzTW9iaWxlKSB7XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLmFkZENsYXNzKCd0YXJvdC1jYXJkLW1vYmlsZScpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ3Rhcm90LWNhcmQtbW9iaWxlJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoZ2FtZS5nZXRTdGVwKCkgPD0gVGFyb3RHYW1lU3RlcC5QT1NUX0NIT09TRV9DQVJEUyB8fCBmb3JjZSkge1xuICAgICAgICAgICAgICAgIGNvbnN0IHN0ZXAgPSAnY2hvb3NlJztcbiAgICAgICAgICAgICAgICBjb250YWluZXIuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgJ2JhY2tncm91bmQtY29sb3InOiBjYXJkWydzdGVwXycgKyBzdGVwICsgJ19iZ19jb2xvciddIHx8ICd0cmFuc3BhcmVudCcsXG4gICAgICAgICAgICAgICAgICAgICdiYWNrZ3JvdW5kLWltYWdlJzogVGVtcGxhdGVIZWxwZXIuY3NzVXJsKGNhcmRbaXNNb2JpbGUgPyAnc3RlcF8nICsgc3RlcCArICdfbW9iaWxlX2JnX2ltYWdlJyA6ICdzdGVwXycgKyBzdGVwICsgJ19iZ19pbWFnZSddLCBjb25maWcuY2FyZEltYWdlc1VybCkgfHwgJ3Vuc2V0JyxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgICAgbG9hZEJhY2tncm91bmRGbih0cnVlKTtcblxuICAgICAgICAvLyBlbnN1cmUgdGhlIGJhY2tncm91bmQgaW1hZ2UgaXMgdXBkYXRlZCB3aGVuIGdvaW5nIHRvIG1vYmlsZVxuICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlcldpbmRvd1Jlc2l6ZUV2ZW50KGxvYWRCYWNrZ3JvdW5kRm4sIGZhbHNlLCAwKTtcblxuICAgICAgICAvLyBlbnN1cmUgd2UgaGF2ZSB0aGUgY29ycmVjdCB0aXRsZSBhbmQgZGVzY3JpcHRpb24gdGhlcmVcbiAgICAgICAgYW5pbVNldFRleHQoc3RlcFRpdGxlQ29udGFpbmVyLCBjYXJkTGFuZy5zdGVwX2Nob29zZV90aXRsZSk7XG4gICAgICAgIGFuaW1TZXRUZXh0KHN0ZXBEZXNjQ29udGFpbmVyLCBjYXJkTGFuZy5zdGVwX2Nob29zZV9kZXNjcmlwdGlvbik7XG5cbiAgICAgICAgLy8gY2xlYXIgdGhlIGNhcmRzIGNvbnRhaW5lciBhbmQgaW5pdCB0aGUgY2FyZHNcbiAgICAgICAgc3RlcENvbnRDb250YWluZXIuaHRtbCgnJyk7XG5cbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBjb25maWcuY2FyZEl0ZW1zLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoVGVtcGxhdGVIZWxwZXIuYnVpbGRDYXJkSXRlbUh0bWwoY29uZmlnLCBpKSk7XG4gICAgICAgIH1cblxuICAgICAgICAvL1xuICAgICAgICBhbmltSW5pdGlhbFNob3dDYXJkcyhnYW1lKS50aGVuKHJlc29sdmUpO1xuICAgIH0pO1xufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL0dsb2JhbHNcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2VudW1zL1Rhcm90R2FtZVN0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL2hlbHBlcnMvVGVtcGxhdGVIZWxwZXJcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL1Rhcm90R2FtZVwiIC8+XG5cbi8qKlxuICogQGJyaWVmIFNob3cgdGhlIGNhcmRzIGFuZCBnZXQgcmVhZHkgdG8gc2h1ZmZsZSB0aGVtLlxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGZ1bmN0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIHNob3dDYXJkc1N0ZXAoZ2FtZTogVGFyb3RHYW1lKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29uZmlnID0gZ2FtZS5nZXRDb25maWcoKTtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcbiAgICAgICAgY29uc3QgY2FyZExhbmcgPSBjb25maWcuY2FyZC5DYXJkTGFuZztcblxuICAgICAgICBjb25zdCBzdGVwQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAnKTtcbiAgICAgICAgY29uc3Qgc3RlcENvbnRDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcblxuICAgICAgICAvL1xuICAgICAgICBjb25zdCBkaXN0cmlidXRpb25GbiA9IGdhbWUuZ2V0Q2FyZERpc3RyaWJ1dGlvbkFuaW1hdGlvbkZuKCk7XG5cbiAgICAgICAgLy9cbiAgICAgICAgY29uc3Qgc2h1ZmZsZUJ0bkNvbnRhaW5lciA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1zaHVmZmxlLWJ0bi1jb250XCI+PC9kaXY+Jyk7XG4gICAgICAgIGNvbnN0IHNodWZmbGVCdG4gPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtYnRuXCI+PC9kaXY+Jyk7XG4gICAgICAgIHNodWZmbGVCdG4udGV4dChjb25maWcudHIuc2h1ZmZsZV9idG5fdGV4dCk7XG4gICAgICAgIHNodWZmbGVCdG4uY3NzKCdvcGFjaXR5JywgMCk7XG4gICAgICAgIHNodWZmbGVCdG5Db250YWluZXIuYXBwZW5kKHNodWZmbGVCdG4pO1xuICAgICAgICBzdGVwQ29udENvbnRhaW5lci5maW5kKCcudGFyb3Qtc2h1ZmYtYnRuLWNvbnQnKS5yZW1vdmUoKTtcbiAgICAgICAgc3RlcENvbnRDb250YWluZXIuYXBwZW5kKHNodWZmbGVCdG5Db250YWluZXIpO1xuXG4gICAgICAgIC8vIGVuc3VyZSB0aGUgY2FyZHMgYXJlIHJlcGxhY2VkIGlmIHRoZSB3aW5kb3cgaXMgcmVzaXplZFxuICAgICAgICBjb25zdCBvblJlc2l6ZUZuID0gICgpID0+IHtcbiAgICAgICAgICAgIGlmIChnYW1lLmdldFN0ZXAoKSA8PSBUYXJvdEdhbWVTdGVwLlBPU1RfQ0hPT1NFX0NBUkRTKSB7XG4gICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIud2FpdEZpbmlzaEFuaW1hdGlvbk9uRWxlbWVudChnYW1lLmdldENvbnRhaW5lcigpKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgZGlzdHJpYnV0aW9uRm4uYW5pbWF0aW9uRm4oZ2FtZSwgZGlzdHJpYnV0aW9uRm4ub3JkZXJGbiwgMCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyV2luZG93UmVzaXplRXZlbnQob25SZXNpemVGbiwgZmFsc2UsIDApO1xuXG4gICAgICAgIC8vXG4gICAgICAgIGRpc3RyaWJ1dGlvbkZuLmFuaW1hdGlvbkZuKGdhbWUsIGRpc3RyaWJ1dGlvbkZuLm9yZGVyRm4pLnRoZW4oZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB3aW5kb3cuc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgc2h1ZmZsZUJ0bi5jc3MoJ29wYWNpdHknLCAxKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vZW51bXMvVGFyb3RHYW1lU3RlcFwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgU2h1ZmZsZSB0aGUgY2FyZHMgYWZ0ZXIgY2xpY2tpbmcgb24gdGhlIHNodWZmbGUgYnV0dG9uLlxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGZ1bmN0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIHNodWZmbGVDYXJkc1N0ZXAoZ2FtZTogVGFyb3RHYW1lKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcbiAgICAgICAgY29uc3Qgc3RlcENvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwJyk7XG4gICAgICAgIGNvbnN0IHN0ZXBDb250Q29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1jb250Jyk7XG4gICAgICAgIGNvbnN0IGNhcmRJdGVtRWxlbWVudHMgPSBjb250YWluZXIuZmluZCgnLnRhcm90LWNhcmQtaXRlbScpO1xuICAgICAgICBjb25zdCBzaHVmZmxlQnRuQ29udCA9IGNvbnRhaW5lci5maW5kKCcudGFyb3Qtc2h1ZmZsZS1idG4tY29udCcpO1xuICAgICAgICBjb25zdCBzaHVmZmxlQnRuID0gc2h1ZmZsZUJ0bkNvbnQuZmluZCgnLnRhcm90LWJ0bicpO1xuXG4gICAgICAgIC8vIGVuc3VyZSB0aGUgc2h1ZmZsZSBidXR0b24gaXMgcmlnaHRseSBwbGFjZWRcbiAgICAgICAgY29uc3Qgb25SZXNpemVGbiA9ICAoKSA9PiB7XG4gICAgICAgICAgICBpZiAoZ2FtZS5nZXRTdGVwKCkgPT09IFRhcm90R2FtZVN0ZXAuUkVBRFlfVE9fU0hVRkZMRSkge1xuICAgICAgICAgICAgICAgIGNvbnN0IGNiUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgnJyk7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2NIID0gc3RlcENvbnRDb250YWluZXIuaGVpZ2h0KCk7XG4gICAgICAgICAgICAgICAgY29uc3QgYkggPSBzaHVmZmxlQnRuQ29udC5oZWlnaHQoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBwYWRZID0gMjA7XG4gICAgICAgICAgICAgICAgY29uc3QgaW5pdGlhbFkgPSBwYXJzZUludChjYXJkSXRlbUVsZW1lbnRzLm5vdCgnLnRhcm90LWNhcmQtcGxhY2Vob2xkZXInKS5ub3QoJy5iaWcnKS5maXJzdCgpLmNzcygndG9wJykgfHwgJzYwJykgICsgY2JTLmhlaWdodCArIHBhZFk7XG4gICAgICAgICAgICAgICAgY29uc3QgcGlZID0gTWF0aC5taW4oaW5pdGlhbFkgKyA1MCwgKHNjSCAtIHBhZFkgKyBpbml0aWFsWSAtIGJIKSAvIDIpO1xuICAgICAgICAgICAgICAgIHNodWZmbGVCdG5Db250LmNzcygndG9wJywgcGlZICsgJ3B4Jyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyV2luZG93UmVzaXplRXZlbnQob25SZXNpemVGbiwgZmFsc2UsIDApO1xuICAgICAgICB3aW5kb3cuc2V0VGltZW91dChvblJlc2l6ZUZuKTtcblxuICAgICAgICAvL1xuICAgICAgICBjb25zdCByZWFsU2h1ZmZsZUNhcmRzID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBsZXQgYTogYW55W10gPSBbXTtcblxuICAgICAgICAgICAgLy8gZ2V0IGNhcmQgaW5mb1xuICAgICAgICAgICAgY2FyZEl0ZW1FbGVtZW50cy5lYWNoKGZ1bmN0aW9uKGs6IGFueSwgZWw6IGFueSkge1xuICAgICAgICAgICAgICAgIGVsID0gJChlbCk7XG4gICAgICAgICAgICAgICAgbGV0IGltZ0Zyb250ID0gZWwuZmluZCgnaW1nLnRhcm90LWNhcmQtaXRlbS1pbWctZnJvbnQnKS5maXJzdCgpO1xuICAgICAgICAgICAgICAgIGEucHVzaCh7XG4gICAgICAgICAgICAgICAgICAgIGlkOiBUZW1wbGF0ZUhlbHBlci5nZXRDYXJkSXRlbUluZGV4RnJvbUVsZW1lbnQoZWwpLFxuICAgICAgICAgICAgICAgICAgICBpbWc6IGltZ0Zyb250LmF0dHIoJ3NyYycpLFxuICAgICAgICAgICAgICAgICAgICBpbWdBbHQ6IGltZ0Zyb250LmF0dHIoJ2FsdCcpXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gc2h1ZmZsZSB0aGUgYXJyYXlcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSBhLmxlbmd0aCAtIDE7IGkgPiAwOyBpLS0pIHtcbiAgICAgICAgICAgICAgICBjb25zdCBqID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogKGkgKyAxKSk7XG4gICAgICAgICAgICAgICAgW2FbaV0sIGFbal1dID0gW2Fbal0sIGFbaV1dO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBhcHBseSB0aGUgYXJyYXlcbiAgICAgICAgICAgIGxldCBpID0gMDtcbiAgICAgICAgICAgIGNhcmRJdGVtRWxlbWVudHMuZWFjaChmdW5jdGlvbihrOiBhbnksIGVsOiBhbnkpIHtcbiAgICAgICAgICAgICAgICBlbCA9ICQoZWwpO1xuICAgICAgICAgICAgICAgIGxldCBpbWdGcm9udCA9IGVsLmZpbmQoJ2ltZy50YXJvdC1jYXJkLWl0ZW0taW1nLWZyb250JykuZmlyc3QoKTtcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5zZXRDYXJkSXRlbUZvckVsZW1lbnQoZWwsIGFbaV0uaWQpO1xuICAgICAgICAgICAgICAgIGltZ0Zyb250LmF0dHIoJ3NyYycsIGFbaV0uaW1nKTtcbiAgICAgICAgICAgICAgICBpbWdGcm9udC5hdHRyKCdhbHQnLCBhW2ldLmltZ0FsdCk7XG4gICAgICAgICAgICAgICAgaSsrO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy9cbiAgICAgICAgbGV0IHNodWZmbGluZyA9IGZhbHNlO1xuICAgICAgICBjb25zdCBzaHVmZmxlRm4gPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmIChzaHVmZmxpbmcpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBzaHVmZmxpbmcgPSB0cnVlO1xuICAgICAgICAgICAgc2h1ZmZsZUJ0bi5jc3MoJ29wYWNpdHknLCAwKTtcbiAgICAgICAgICAgIGFuaW1TaHVmZmxlQ2FyZHMoZ2FtZSkudGhlbihmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBzaHVmZmxlQnRuQ29udC5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLm9mZihUZW1wbGF0ZUhlbHBlci5FVkVOVF9HUk9VUCk7XG5cbiAgICAgICAgICAgICAgICAvLyBhY3R1YWwgc2h1ZmZsaW5nXG4gICAgICAgICAgICAgICAgcmVhbFNodWZmbGVDYXJkcygpO1xuXG4gICAgICAgICAgICAgICAgLy8gcmVkaXN0cmlidXRlXG4gICAgICAgICAgICAgICAgY29uc3QgZGlzdHJpYnV0aW9uRm4gPSBnYW1lLmdldENhcmREaXN0cmlidXRpb25BbmltYXRpb25GbigpO1xuICAgICAgICAgICAgICAgIGRpc3RyaWJ1dGlvbkZuLmFuaW1hdGlvbkZuKGdhbWUsIGRpc3RyaWJ1dGlvbkZuLm9yZGVyRm4pLnRoZW4ocmVzb2x2ZSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICBzaHVmZmxlQnRuLm9mZihUZW1wbGF0ZUhlbHBlci5FVkVOVF9HUk9VUCkub25lKCdjbGljaycgKyBUZW1wbGF0ZUhlbHBlci5FVkVOVF9HUk9VUCwgc2h1ZmZsZUZuKTtcbiAgICAgICAgY2FyZEl0ZW1FbGVtZW50cy5vZmYoVGVtcGxhdGVIZWxwZXIuRVZFTlRfR1JPVVApLm9uZSgnY2xpY2snICsgVGVtcGxhdGVIZWxwZXIuRVZFTlRfR1JPVVAsIHNodWZmbGVGbik7XG4gICAgfSk7XG59XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vZW51bXMvVGFyb3RHYW1lU3RlcFwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vVGFyb3RHYW1lXCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgTGV0cyB0aGUgdXNlciBjaG9vc2UgdGhlIGNhcmRzLlxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGZ1bmN0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIGNob29zZUNhcmRzU3RlcChnYW1lOiBUYXJvdEdhbWUpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2U8dm9pZD4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCBjb25maWcgPSBnYW1lLmdldENvbmZpZygpO1xuICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcbiAgICAgICAgY29uc3QgY2FyZExhbmcgPSBjb25maWcuY2FyZC5DYXJkTGFuZztcbiAgICAgICAgY29uc3QgY2FyZEl0ZW1zID0gY29uZmlnLmNhcmRJdGVtcztcbiAgICAgICAgY29uc3QgcGlDb3VudCA9ICtjYXJkLmNvdW50X3RvX3BpY2s7XG5cbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcbiAgICAgICAgY29uc3Qgc3RlcENvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwJyk7XG4gICAgICAgIGNvbnN0IHN0ZXBUaXRsZUNvbnRhaW5lciAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC10aXRsZScpO1xuICAgICAgICBjb25zdCBzdGVwRGVzY0NvbnRhaW5lciAgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtZGVzYycpO1xuICAgICAgICBjb25zdCBzdGVwQ29udENvbnRhaW5lciAgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtY29udCcpO1xuICAgICAgICBjb25zdCBjYXJkSXRlbUVsZW1lbnRzID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1jYXJkLWl0ZW0nKTtcblxuICAgICAgICAvL1xuICAgICAgICBjb25zdCBjaG9vc2VfbGluZXMgPSBjYXJkTGFuZy5zdGVwX2Nob29zZV9saW5lcy5zcGxpdCgvXFxuKy8pO1xuICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGNob29zZV9saW5lcy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgICAgY2hvb3NlX2xpbmVzW2ldID0gY2hvb3NlX2xpbmVzW2ldLnRyaW0oKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoIWNob29zZV9saW5lcy5sZW5ndGgpIHtcbiAgICAgICAgICAgIGNob29zZV9saW5lcy5wdXNoKGNhcmRMYW5nLnN0ZXBfY2hvb3NlX2Rlc2NyaXB0aW9uKTtcbiAgICAgICAgfVxuICAgICAgICBzdGVwRGVzY0NvbnRhaW5lci5jc3MoJ21pbi1oZWlnaHQnLCBzdGVwRGVzY0NvbnRhaW5lci5oZWlnaHQoKSArICdweCcpO1xuICAgICAgICBhbmltU2V0VGV4dChzdGVwRGVzY0NvbnRhaW5lciwgY2hvb3NlX2xpbmVzWzBdKTtcblxuICAgICAgICAvL1xuICAgICAgICBjb25zdCBpbml0UGxhY2Vob2xkZXJzID0gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHBpQ291bnQ7ICsraSkge1xuICAgICAgICAgICAgICAgIGNvbnN0IGVsID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LWNhcmQtcGxhY2Vob2xkZXJcIj48L2Rpdj4nKTtcbiAgICAgICAgICAgICAgICBsZXQgaW1nID0gJCgnPGltZyAvPicpO1xuICAgICAgICAgICAgICAgIGltZy5hdHRyKCdzcmMnLCBUZW1wbGF0ZUhlbHBlci5wcmVmaXhVcmwoY2FyZC5pdGVtX2Rpc2FibGVkX2JnX2ltYWdlLCBjb25maWcuY2FyZEltYWdlc1VybCkpO1xuICAgICAgICAgICAgICAgIGltZy5hdHRyKCdhbHQnLCAnQ2FyZCBwbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgICAgIGVsLmFwcGVuZChpbWcpO1xuICAgICAgICAgICAgICAgIHN0ZXBDb250Q29udGFpbmVyLmFwcGVuZChlbCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICAgIGluaXRQbGFjZWhvbGRlcnMoKTtcblxuICAgICAgICAvL1xuICAgICAgICBjb25zdCBwbGFjZWhvbGRlcnMgPSBzdGVwQ29udENvbnRhaW5lci5maW5kKCcudGFyb3QtY2FyZC1wbGFjZWhvbGRlcicpO1xuICAgICAgICBjb25zdCByZXBvc2l0aW9uUGxhY2Vob2xkZXJzQW5kU2VsZWN0ZWQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGNvbnN0IGlzTW9iaWxlID0gVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKTtcbiAgICAgICAgICAgIGNvbnN0IGNiUyA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtU2l6ZSgnJyk7XG4gICAgICAgICAgICBjb25zdCBzY1cgPSBzdGVwQ29udENvbnRhaW5lci53aWR0aCgpO1xuICAgICAgICAgICAgY29uc3Qgc2NIID0gc3RlcENvbnRDb250YWluZXIuaGVpZ2h0KCk7XG4gICAgICAgICAgICBjb25zdCBwUyA9IFRlbXBsYXRlSGVscGVyLmdldEVsZW1lbnRTaXplKCcudGFyb3QtZ2FtZSAudGFyb3QtY2FyZC1wbGFjZWhvbGRlcicpO1xuICAgICAgICAgICAgY29uc3QgcGlXID0gcFMud2lkdGg7XG4gICAgICAgICAgICBjb25zdCBwaUggPSBwUy5oZWlnaHQ7XG4gICAgICAgICAgICBjb25zdCBwaVdBbGwgPSBwaVcgKiBwaUNvdW50O1xuICAgICAgICAgICAgY29uc3QgcGlQYWRYID0gTWF0aC5taW4oMjAsIHBpQ291bnQgPT09IDEgPyAwIDogKHNjVyAtIHBpV0FsbCkgLyAocGlDb3VudCAtIDEpKTtcbiAgICAgICAgICAgIGNvbnN0IHBpWCA9ICgoc2NXIC0gcGlXQWxsIC0gcGlQYWRYICogKHBpQ291bnQgLSAxKSkgLyAyKTtcbiAgICAgICAgICAgIGNvbnN0IHBpRGVsdGEgPSBwaVcgKyBwaVBhZFg7XG4gICAgICAgICAgICBjb25zdCBwYWRZID0gMjA7XG4gICAgICAgICAgICBjb25zdCBpbml0aWFsWSA9IHBhcnNlSW50KGNhcmRJdGVtRWxlbWVudHMubm90KCcudGFyb3QtY2FyZC1wbGFjZWhvbGRlcicpLm5vdCgnLmJpZycpLmZpcnN0KCkuY3NzKCd0b3AnKSB8fCAnNjAnKSAgKyBjYlMuaGVpZ2h0ICsgcGFkWTtcbiAgICAgICAgICAgIGNvbnN0IHBpWSA9IGlzTW9iaWxlID9cbiAgICAgICAgICAgICAgICBpbml0aWFsWSA6XG4gICAgICAgICAgICAgICAgKHNjSCAtIHBhZFkgKyBpbml0aWFsWSAtIHBpSCkgLyAyXG4gICAgICAgICAgICA7XG4gICAgICAgICAgICBwbGFjZWhvbGRlcnMuZWFjaChmdW5jdGlvbihpOiBhbnksIGVsOiBhbnkpIHtcbiAgICAgICAgICAgICAgICBlbCA9ICQoZWwpO1xuICAgICAgICAgICAgICAgIGxldCBvZmYgPSB7XG4gICAgICAgICAgICAgICAgICAgIGxlZnQ6IHBpWCArIGkgKiBwaURlbHRhICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgdG9wOiBwaVkgKyAncHgnXG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICBlbC5jc3Mob2ZmKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgbGV0IHNlbGVjdGVkID0gY29udGFpbmVyLmZpbmQoJy50YXJvdC1jYXJkLWl0ZW0uc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgIHNlbGVjdGVkLmVhY2goZnVuY3Rpb24obzogYW55LCBlbDogYW55KSB7XG4gICAgICAgICAgICAgICAgZWwgPSAkKGVsKTtcbiAgICAgICAgICAgICAgICBjb25zdCBjYXJkX2l0ZW1faWQ6IGFueSA9IFRlbXBsYXRlSGVscGVyLmdldENhcmRJdGVtSW5kZXhGcm9tRWxlbWVudChlbCk7XG4gICAgICAgICAgICAgICAgaWYgKGNhcmRfaXRlbV9pZCA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGxldCBpID0gZ2FtZS5nZXRTZWxlY3RlZENhcmRJdGVtSWRzKCkuaW5kZXhPZigrY2FyZF9pdGVtX2lkKTtcbiAgICAgICAgICAgICAgICBpZiAoaSA9PT0gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBsZXQgb2ZmID0ge1xuICAgICAgICAgICAgICAgICAgICBsZWZ0OiBwaVggKyBpICogcGlEZWx0YSArICdweCcsXG4gICAgICAgICAgICAgICAgICAgIHRvcDogcGlZICsgJ3B4J1xuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgZWwuY3NzKG9mZik7XG4gICAgICAgICAgICB9KTtcblxuXG4gICAgICAgIH07XG4gICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KHJlcG9zaXRpb25QbGFjZWhvbGRlcnNBbmRTZWxlY3RlZCk7XG5cbiAgICAgICAgLy8gZW5zdXJlIHRoZSBwbGFjZWhvbGRlcnMgYW5kIGNhcmRzIGFyZSByZXBsYWNlZCBpZiB0aGUgd2luZG93IGlzIHJlc2l6ZWRcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIucmVnaXN0ZXJXaW5kb3dSZXNpemVFdmVudCgoKSA9PiB7XG4gICAgICAgICAgICBpZiAoZ2FtZS5nZXRTdGVwKCkgPD0gVGFyb3RHYW1lU3RlcC5QT1NUX0NIT09TRV9DQVJEUykge1xuICAgICAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLndhaXRGaW5pc2hBbmltYXRpb25PbkVsZW1lbnQoZ2FtZS5nZXRDb250YWluZXIoKSkudGhlbihmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVwb3NpdGlvblBsYWNlaG9sZGVyc0FuZFNlbGVjdGVkKCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sIGZhbHNlLCAwKTtcblxuICAgICAgICAvLyBIYW5kbGUgY2FyZCBjbGlja3NcbiAgICAgICAgY29uc3QgaXNTYWZhcmkgPSBUZW1wbGF0ZUhlbHBlci5pc1NhZmFyaSgpO1xuICAgICAgICBpZiAoaXNTYWZhcmkpIHtcbiAgICAgICAgICAgIFRlbXBsYXRlSGVscGVyLnJlZ2lzdGVyU2VsZWN0YWJsZUFuaW1hdGlvbnMoY2FyZEl0ZW1FbGVtZW50cyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hZGRDbGFzcygnc2VsZWN0YWJsZS1pdGVtcycpO1xuICAgICAgICB9XG4gICAgICAgIGxldCBkaXNhYmxlQ2xpY2sgPSBmYWxzZTtcbiAgICAgICAgY2FyZEl0ZW1FbGVtZW50cy5vZmYoVGVtcGxhdGVIZWxwZXIuRVZFTlRfR1JPVVApLm9uKCdjbGljaycgKyBUZW1wbGF0ZUhlbHBlci5FVkVOVF9HUk9VUCwgZnVuY3Rpb24odGhpczogYW55KSB7XG4gICAgICAgICAgICBsZXQgZWxlbWVudCA9ICQodGhpcyk7XG4gICAgICAgICAgICBsZXQgdG1wID0gVGVtcGxhdGVIZWxwZXIuZ2V0Q2FyZEl0ZW1JbmRleEZyb21FbGVtZW50KGVsZW1lbnQpO1xuICAgICAgICAgICAgaWYgKHRtcCA9PT0gbnVsbCB8fCBlbGVtZW50Lmhhc0NsYXNzKCdmcm9udCcpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbGV0IGNhcmRJdGVtSW5kZXg6IG51bWJlciA9ICt0bXA7XG4gICAgICAgICAgICBsZXQgcGxhY2Vob2xkZXIgPSAkKHBsYWNlaG9sZGVycy5nZXQoZ2FtZS5nZXRTZWxlY3RlZENhcmRJdGVtSWRzKCkubGVuZ3RoKSk7XG5cbiAgICAgICAgICAgIGlmIChkaXNhYmxlQ2xpY2spIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBkaXNhYmxlQ2xpY2sgPSB0cnVlO1xuICAgICAgICAgICAgaWYgKGlzU2FmYXJpKSB7XG4gICAgICAgICAgICAgICAgVGVtcGxhdGVIZWxwZXIudW5yZWdpc3RlclNlbGVjdGFibGVBbmltYXRpb25zKGNhcmRJdGVtRWxlbWVudHMpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBzdGVwQ29udENvbnRhaW5lci5yZW1vdmVDbGFzcygnc2VsZWN0YWJsZS1pdGVtcycpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBhbmltU2VsZWN0Q2FyZChnYW1lLCBlbGVtZW50LCBwbGFjZWhvbGRlcikudGhlbihmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBlbGVtZW50LmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgIGdhbWUuYWRkU2VsZWN0ZWRDYXJkSXRlbUlkKGNhcmRJdGVtSW5kZXgpO1xuXG4gICAgICAgICAgICAgICAgaWYgKGdhbWUuZ2V0U2VsZWN0ZWRDYXJkSXRlbUlkcygpLmxlbmd0aCA9PT0gcGlDb3VudCkge1xuICAgICAgICAgICAgICAgICAgICBjYXJkSXRlbUVsZW1lbnRzLm9mZihUZW1wbGF0ZUhlbHBlci5FVkVOVF9HUk9VUCk7XG4gICAgICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBkaXNhYmxlQ2xpY2sgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGlzU2FmYXJpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlclNlbGVjdGFibGVBbmltYXRpb25zKGNhcmRJdGVtRWxlbWVudHMpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RlcENvbnRDb250YWluZXIuYWRkQ2xhc3MoJ3NlbGVjdGFibGUtaXRlbXMnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBpZiAoZ2FtZS5nZXRTZWxlY3RlZENhcmRJdGVtSWRzKCkubGVuZ3RoIDwgY2hvb3NlX2xpbmVzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYW5pbVNldFRleHQoc3RlcERlc2NDb250YWluZXIsIGNob29zZV9saW5lc1tnYW1lLmdldFNlbGVjdGVkQ2FyZEl0ZW1JZHMoKS5sZW5ndGhdKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9KTtcbn1cbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9lbnVtcy9UYXJvdEdhbWVTdGVwXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9UYXJvdEdhbWVcIiAvPlxuXG4vKipcbiAqIEBicmllZiBTaG93cyB0aGUgcHJvY2Vzc2luZyBzdGVwLlxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cbiAqIEByZXR1cm4gUHJvbWlzZSBBIHByb21pc2UgZm9yIHdoZW4gdGhlIGZ1bmN0aW9uIGlzIGRvbmUuXG4gKi9cbmZ1bmN0aW9uIHByb2Nlc3NTdGVwKGdhbWU6IFRhcm90R2FtZSkge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZTx2b2lkPigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIGNvbnN0IGNvbmZpZyA9IGdhbWUuZ2V0Q29uZmlnKCk7XG4gICAgICAgIGNvbnN0IGNvbnRhaW5lciA9IGdhbWUuZ2V0Q29udGFpbmVyKCk7XG4gICAgICAgIGNvbnN0IGNhcmQgPSBjb25maWcuY2FyZC5DYXJkO1xuICAgICAgICBjb25zdCBjYXJkTGFuZyA9IGNvbmZpZy5jYXJkLkNhcmRMYW5nO1xuICAgICAgICBjb25zdCBjYXJkSXRlbXMgPSBjb25maWcuY2FyZEl0ZW1zO1xuXG4gICAgICAgIGNvbnRhaW5lci5zaWJsaW5ncygnLnRhcm90LWdhbWUtbWFpbi1kZXNjJykuc2xpZGVVcCgpO1xuXG4gICAgICAgIGNvbnN0IHN0ZXBDb250YWluZXIgPSBjb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcCcpO1xuICAgICAgICBjb25zdCBzdGVwVGl0bGVDb250YWluZXIgICAgPSBzdGVwQ29udGFpbmVyLmZpbmQoJy50YXJvdC1nYW1lLXN0ZXAtdGl0bGUnKTtcbiAgICAgICAgY29uc3Qgc3RlcERlc2NDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWRlc2MnKTtcbiAgICAgICAgY29uc3Qgc3RlcENvbnRDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcblxuICAgICAgICAvLyBlbnN1cmUgd2UgaGF2ZSB0aGUgcHJvcGVyIGNsYXNzXG4gICAgICAgIHN0ZXBDb250YWluZXIuYXR0cignY2xhc3MnLCAndGFyb3QtZ2FtZS1zdGVwIHRhcm90LWdhbWUtc3RlcC1pbnRlcnByZXRhdGlvbicpO1xuXG4gICAgICAgIC8vIGxvYWQgYmFja2dyb3VuZFxuICAgICAgICBjb25zdCBsb2FkQmFja2dyb3VuZEZuID0gKGZvcmNlID0gZmFsc2UpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGlzTW9iaWxlID0gVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKTtcbiAgICAgICAgICAgIGlmIChpc01vYmlsZSkge1xuICAgICAgICAgICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygndGFyb3QtY2FyZC1tb2JpbGUnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCd0YXJvdC1jYXJkLW1vYmlsZScpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKGdhbWUuZ2V0U3RlcCgpID09PSBUYXJvdEdhbWVTdGVwLlBST0NFU1NfU0VMRUNUSU9OIHx8IGZvcmNlKSB7XG4gICAgICAgICAgICAgICAgY29uc3Qgc3RlcCA9ICdpbnRlcnByZXRhdGlvbic7XG4gICAgICAgICAgICAgICAgY29udGFpbmVyLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICdiYWNrZ3JvdW5kLWNvbG9yJzogY2FyZFsnc3RlcF8nICsgc3RlcCArICdfYmdfY29sb3InXSB8fCAndHJhbnNwYXJlbnQnLFxuICAgICAgICAgICAgICAgICAgICAnYmFja2dyb3VuZC1pbWFnZSc6IFRlbXBsYXRlSGVscGVyLmNzc1VybChjYXJkW2lzTW9iaWxlID8gJ3N0ZXBfJyArIHN0ZXAgKyAnX21vYmlsZV9iZ19pbWFnZScgOiAnc3RlcF8nICsgc3RlcCArICdfYmdfaW1hZ2UnXSwgY29uZmlnLmNhcmRJbWFnZXNVcmwpIHx8XG4gICAgICAgICAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5jc3NVcmwoY2FyZFtpc01vYmlsZSA/ICdzdGVwX2Nob29zZV9tb2JpbGVfYmdfaW1hZ2UnIDogJ3N0ZXBfY2hvb3NlX2JnX2ltYWdlJ10sIGNvbmZpZy5jYXJkSW1hZ2VzVXJsKSB8fFxuICAgICAgICAgICAgICAgICAgICAgICAgJ3Vuc2V0JyxcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgICAgbG9hZEJhY2tncm91bmRGbih0cnVlKTtcblxuICAgICAgICAvLyBlbnN1cmUgdGhlIGJhY2tncm91bmQgaW1hZ2UgaXMgdXBkYXRlZCB3aGVuIGdvaW5nIHRvIG1vYmlsZVxuICAgICAgICBUZW1wbGF0ZUhlbHBlci5yZWdpc3RlcldpbmRvd1Jlc2l6ZUV2ZW50KGxvYWRCYWNrZ3JvdW5kRm4sIGZhbHNlLCAwKTtcblxuICAgICAgICAvLyBlbnN1cmUgd2UgaGF2ZSB0aGUgY29ycmVjdCB0aXRsZSBhbmQgZGVzY3JpcHRpb24gdGhlcmVcbiAgICAgICAgYW5pbVNldFRleHQoc3RlcFRpdGxlQ29udGFpbmVyLCBjYXJkTGFuZy5zdGVwX2ludGVycHJldGF0aW9uX3RpdGxlKTtcbiAgICAgICAgYW5pbVNldFRleHQoc3RlcERlc2NDb250YWluZXIsICcnKTtcblxuICAgICAgICAvL1xuICAgICAgICBsZXQgYW5pbUZuID0gYW5pbVByb2Nlc3NTaW5nbGU7XG4gICAgICAgIGxldCBkaXN0cmlidXRlT3JkZXJGbiA9IERpc3RyaWJ1dGVPcmRlci5vdXRGaXJzdERpc3RyaWJ1dGlvbjtcbiAgICAgICAgY29uc3QgZ2FtZVR5cGUgPSArY29uZmlnLmNhcmQuQ2FyZC5nYW1lX3R5cGU7XG4gICAgICAgIGlmIChnYW1lVHlwZSA9PT0gR2FtZVR5cGUuWUVTX05PKSB7XG4gICAgICAgICAgICBhbmltRm4gPSBhbmltUHJvY2Vzc1llc05vO1xuICAgICAgICB9IGVsc2UgaWYgKGdhbWVUeXBlID09PSBHYW1lVHlwZS5TSU5HTEUpIHtcbiAgICAgICAgICAgIGFuaW1GbiA9IGFuaW1Qcm9jZXNzU2luZ2xlO1xuICAgICAgICB9IGVsc2UgaWYgKGdhbWVUeXBlID09PSBHYW1lVHlwZS5GT1JUVU5FKSB7XG4gICAgICAgICAgICBhbmltRm4gPSBhbmltUHJvY2Vzc0ZvcnR1bmU7XG4gICAgICAgIH0gZWxzZSBpZiAoZ2FtZVR5cGUgPT09IEdhbWVUeXBlLkxPVkUpIHtcbiAgICAgICAgICAgIGFuaW1GbiA9IGFuaW1Qcm9jZXNzTG92ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vXG4gICAgICAgIGFuaW1GbihnYW1lKS50aGVuKHJlc29sdmUpO1xuICAgIH0pO1xufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL0dsb2JhbHNcIiAvPlxyXG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi4vZW51bXMvVGFyb3RHYW1lU3RlcFwiIC8+XHJcbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuLi9oZWxwZXJzL1RlbXBsYXRlSGVscGVyXCIgLz5cclxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4uL1Rhcm90R2FtZVwiIC8+XHJcblxyXG4vKipcclxuICogQGJyaWVmIFNob3dzIHRoZSByZXN1bHRzIHN0ZXBcclxuICogQHBhcmFtIFRhcm90R2FtZSBnYW1lIFRoZSBnYW1lIG9iamVjdC5cclxuICogQHJldHVybiBQcm9taXNlIEEgcHJvbWlzZSBmb3Igd2hlbiB0aGUgZnVuY3Rpb24gaXMgZG9uZS5cclxuICovXHJcbmZ1bmN0aW9uIHJlc3VsdFN0ZXAoZ2FtZTogVGFyb3RHYW1lKSB7XHJcbiAgICByZXR1cm4gbmV3IFByb21pc2U8dm9pZD4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xyXG4gICAgICAgIGNvbnN0IGNvbmZpZyA9IGdhbWUuZ2V0Q29uZmlnKCk7XHJcbiAgICAgICAgY29uc3QgY29udGFpbmVyID0gZ2FtZS5nZXRDb250YWluZXIoKTtcclxuICAgICAgICBjb25zdCBjYXJkID0gY29uZmlnLmNhcmQuQ2FyZDtcclxuICAgICAgICBjb25zdCBjYXJkTGFuZyA9IGNvbmZpZy5jYXJkLkNhcmRMYW5nO1xyXG4gICAgICAgIGNvbnN0IGNhcmRJdGVtcyA9IGNvbmZpZy5jYXJkSXRlbXM7XHJcblxyXG4gICAgICAgIGNvbnN0IHN0ZXBDb250YWluZXIgPSBjb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcCcpO1xyXG4gICAgICAgIGNvbnN0IHN0ZXBUaXRsZUNvbnRhaW5lciAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC10aXRsZScpO1xyXG4gICAgICAgIGNvbnN0IHN0ZXBEZXNjQ29udGFpbmVyICAgICA9IHN0ZXBDb250YWluZXIuZmluZCgnLnRhcm90LWdhbWUtc3RlcC1kZXNjJyk7XHJcbiAgICAgICAgY29uc3Qgc3RlcENvbnRDb250YWluZXIgICAgID0gc3RlcENvbnRhaW5lci5maW5kKCcudGFyb3QtZ2FtZS1zdGVwLWNvbnQnKTtcclxuXHJcbiAgICAgICAgLy8gZW5zdXJlIHdlIGhhdmUgdGhlIHByb3BlciBjbGFzc1xyXG4gICAgICAgIHN0ZXBDb250YWluZXIuYXR0cignY2xhc3MnLCAndGFyb3QtZ2FtZS1zdGVwIHRhcm90LWdhbWUtc3RlcC1yZXN1bHQnKTtcclxuXHJcbiAgICAgICAgLy8gbG9hZCBiYWNrZ3JvdW5kXHJcbiAgICAgICAgY29uc3QgbG9hZEJhY2tncm91bmRGbiA9IChmb3JjZSA9IGZhbHNlKSA9PiB7XHJcbiAgICAgICAgICAgIGNvbnN0IGlzTW9iaWxlID0gVGVtcGxhdGVIZWxwZXIuaXNNb2JpbGUoKTtcclxuICAgICAgICAgICAgaWYgKGlzTW9iaWxlKSB7XHJcbiAgICAgICAgICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ3Rhcm90LWNhcmQtbW9iaWxlJyk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ3Rhcm90LWNhcmQtbW9iaWxlJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYgKGdhbWUuZ2V0U3RlcCgpID09PSBUYXJvdEdhbWVTdGVwLlNIT1dfUkVTVUxUUyB8fCBmb3JjZSkge1xyXG4gICAgICAgICAgICAgICAgY29uc3Qgc3RlcCA9ICdyZXN1bHQnO1xyXG4gICAgICAgICAgICAgICAgY29udGFpbmVyLmNzcyh7XHJcbiAgICAgICAgICAgICAgICAgICAgJ2JhY2tncm91bmQtY29sb3InOiBjYXJkWydzdGVwXycgKyBzdGVwICsgJ19iZ19jb2xvciddIHx8ICd0cmFuc3BhcmVudCcsXHJcbiAgICAgICAgICAgICAgICAgICAgJ2JhY2tncm91bmQtaW1hZ2UnOiBUZW1wbGF0ZUhlbHBlci5jc3NVcmwoY2FyZFtpc01vYmlsZSA/ICdzdGVwXycgKyBzdGVwICsgJ19tb2JpbGVfYmdfaW1hZ2UnIDogJ3N0ZXBfJyArIHN0ZXAgKyAnX2JnX2ltYWdlJ10sIGNvbmZpZy5jYXJkSW1hZ2VzVXJsKSB8fCAndW5zZXQnLFxyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgICAgIGxvYWRCYWNrZ3JvdW5kRm4odHJ1ZSk7XHJcblxyXG4gICAgICAgIC8vIGVuc3VyZSB0aGUgYmFja2dyb3VuZCBpbWFnZSBpcyB1cGRhdGVkIHdoZW4gZ29pbmcgdG8gbW9iaWxlXHJcbiAgICAgICAgVGVtcGxhdGVIZWxwZXIucmVnaXN0ZXJXaW5kb3dSZXNpemVFdmVudChsb2FkQmFja2dyb3VuZEZuLCBmYWxzZSwgMCk7XHJcblxyXG4gICAgICAgIC8vIGVuc3VyZSB3ZSBoYXZlIHRoZSBjb3JyZWN0IHRpdGxlIGFuZCBkZXNjcmlwdGlvbiB0aGVyZVxyXG4gICAgICAgIGFuaW1TZXRUZXh0KHN0ZXBUaXRsZUNvbnRhaW5lciwgY2FyZExhbmcuc3RlcF9yZXN1bHRfdGl0bGUpO1xyXG4gICAgICAgIGFuaW1TZXRUZXh0KHN0ZXBEZXNjQ29udGFpbmVyLCBjYXJkTGFuZy5zdGVwX3Jlc3VsdF9kZXNjcmlwdGlvbik7XHJcblxyXG4gICAgICAgIC8vIGNsZWFyIHRoZSBjYXJkcyBjb250YWluZXIgYW5kIHNob3dzIHRoZSByZXN1bHRzXHJcbiAgICAgICAgc3RlcENvbnRDb250YWluZXIuaHRtbCgnJyk7XHJcblxyXG4gICAgICAgIGNvbnN0IHJlc3VsdCA9IGdhbWUuZ2V0UmVzdWx0KCk7XHJcbiAgICAgICAgY29uc3Qgc2VsZWN0ZWRDYXJkSXRlbUlkcyA9IGdhbWUuZ2V0U2VsZWN0ZWRDYXJkSXRlbUlkcygpO1xyXG5cclxuICAgICAgICBsZXQgaHRtbCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHRcIj48L2Rpdj4nKTtcclxuICAgICAgICBzdGVwQ29udENvbnRhaW5lci5hcHBlbmQoaHRtbCk7XHJcblxyXG4gICAgICAgIGxldCBtYWluSHRtbCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtY29udGVudFwiPjwvZGl2PicpO1xyXG4gICAgICAgIGh0bWwuYXBwZW5kKG1haW5IdG1sKTtcclxuICAgICAgICBsZXQgbWFpbkh0bWxGb3JDYXJkcyA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtY29udGVudC1jYXJkXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgbWFpbkh0bWxGb3JDYXJkcy5hcHBlbmQoJzxoMz4nICsgcmVzdWx0LnRyLmNhcmRfdGl0bGUgKyAnPC9oMz4nKTtcclxuICAgICAgICBsZXQgcmVzdWx0VGV4dENvdW50ID0gMDtcclxuICAgICAgICBmb3IgKGxldCBrID0gMDsgayA8IHNlbGVjdGVkQ2FyZEl0ZW1JZHMubGVuZ3RoOyArK2spIHtcclxuICAgICAgICAgICAgY29uc3QgaSA9ICtzZWxlY3RlZENhcmRJdGVtSWRzW2tdO1xyXG4gICAgICAgICAgICBjb25zdCBjYXJkSXRlbSA9IGNhcmRJdGVtc1tpXS5DYXJkSXRlbTtcclxuICAgICAgICAgICAgY29uc3QgY2FyZEl0ZW1MYW5nID0gY2FyZEl0ZW1zW2ldLkNhcmRJdGVtTGFuZztcclxuXHJcbiAgICAgICAgICAgIG1haW5IdG1sRm9yQ2FyZHMuYXBwZW5kKCc8ZGl2IGNsYXNzPVwibnVtZXJhdGVkLXRleHRcIj48c3BhbiBjbGFzcz1cInRleHQtbnVtXCI+JyArICgrK3Jlc3VsdFRleHRDb3VudCkgKyAnPC9zcGFuPjxwPicgKyBjYXJkSXRlbUxhbmcuZGVzY3JpcHRpb24gKyAnPC9wPjwvZGl2PicpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBtYWluSHRtbC5hcHBlbmQobWFpbkh0bWxGb3JDYXJkcyk7XHJcbiAgICAgICAgXHJcbiAgICAgICAgaWYgKHJlc3VsdC5lbWFpbF9mb3JtKSB7XHJcbiAgICAgICAgICAgIG1haW5IdG1sRm9yQ2FyZHMgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LWNvbnRlbnQtY2FyZCBjYXJkLWJsdXJcIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgbWFpbkh0bWwuYXBwZW5kKG1haW5IdG1sRm9yQ2FyZHMpO1xyXG5cdFx0XHRtYWluSHRtbEZvckNhcmRzLmFwcGVuZCgnPGgzPicgKyByZXN1bHQudHIucmVzdWx0X3RpdGxlICsgJzwvaDM+Jyk7XHJcbiAgICAgICAgICAgIG1haW5IdG1sRm9yQ2FyZHMuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidHh0LWJsdXJcIj4nK3Jlc3VsdC50ZXh0Kyc8L2Rpdj4nKTtcclxuICAgICAgICAgICAgbWFpbkh0bWwuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LWVtYWlsZm9ybVwiPicgKyByZXN1bHQuZW1haWxfZm9ybSArICc8L2Rpdj4nKTtcclxuXHRcdFx0bWFpbkh0bWwuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3RfZW1haWxmb3JtX2lkXCIgc3R5bGU9XCJkaXNwbGF5Om5vbmVcIj4nK3Jlc3VsdC5jYXJkX2lkKyc8L2Rpdj4nKTtcclxuXHRcdFx0Ly9iaW5kRW1haWxGb3JtQ2FyZCgpO1xyXG5cdFx0XHRcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgbWFpbkh0bWxGb3JDYXJkcyA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtY29udGVudC1jYXJkXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIG1haW5IdG1sLmFwcGVuZChtYWluSHRtbEZvckNhcmRzKTtcclxuXHRcdFx0bWFpbkh0bWxGb3JDYXJkcy5hcHBlbmQoJzxoMz4nICsgcmVzdWx0LnRyLnJlc3VsdF90aXRsZSArICc8L2gzPicpO1xyXG4gICAgICAgICAgICBtYWluSHRtbEZvckNhcmRzLmFwcGVuZChyZXN1bHQudGV4dCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAoY2FyZExhbmcuc3RlcF9yZXN1bHRfbmV4dCkge1xyXG4gICAgICAgICAgICBtYWluSHRtbEZvckNhcmRzLmFwcGVuZCgnPGgzIGNsYXNzPVwiY2FyZF9uZXh0X2RhdGFcIiBzdHlsZT1cIm1hcmdpbi10b3A6MTVweDtcIj4nICsgcmVzdWx0LnRyLm5leHRfdGl0bGUgKyAnPC9oMz4nKTtcclxuICAgICAgICAgICAgbWFpbkh0bWxGb3JDYXJkcy5hcHBlbmQoJzxwIGNsYXNzPVwiY2FyZF9uZXh0X2RhdGFcIj4nICsgY2FyZExhbmcuc3RlcF9yZXN1bHRfbmV4dCArICc8L3A+Jyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAocmVzdWx0LnJlZ2lzdGVyX2Zvcm0pIHtcclxuICAgICAgICAgICAgbGV0IGNsaWNrYWJsZVRpdGxlID0gJCgnPHNwYW4gY2xhc3M9XCJ0YXJvdC1yZXN1bHQtcmVnaXN0ZXItYnRuXCI+JyArIHJlc3VsdC50ci5pbnNfdGl0bGUgKyAnPC9zcGFuPicpO1xyXG4gICAgICAgICAgICBsZXQgY2xpY2thYmxlVGl0bGVDb250ID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1jb250ZW50LW5leHQtYnRuIGNhcmRfbmV4dF9kYXRhXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIGNsaWNrYWJsZVRpdGxlQ29udC5hcHBlbmQoY2xpY2thYmxlVGl0bGUpO1xyXG4gICAgICAgICAgICAvL21haW5IdG1sRm9yQ2FyZHMuYXBwZW5kKGNsaWNrYWJsZVRpdGxlQ29udCk7XHJcblxyXG4gICAgICAgICAgICBsZXQgcmVnaXN0ZXJGb3JtID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1jb250ZW50LWZvcm0gaGlkZGVuLWZvcm1cIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgcmVnaXN0ZXJGb3JtLmFwcGVuZCgnPGgzPicgKyByZXN1bHQudHIuaW5zX3RpdGxlICsgJzwvaDM+Jyk7XHJcbiAgICAgICAgICAgIHJlZ2lzdGVyRm9ybS5hcHBlbmQoJzxkaXY+JyArIHJlc3VsdC5yZWdpc3Rlcl9mb3JtICsgJzwvZGl2PicpO1xyXG4gICAgICAgICAgICBtYWluSHRtbEZvckNhcmRzLmFwcGVuZChyZWdpc3RlckZvcm0pO1xyXG5cclxuICAgICAgICAgICAgY2xpY2thYmxlVGl0bGUub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgc2Nyb2xsVG8gPSBjbGlja2FibGVUaXRsZS5vZmZzZXQoKS50b3AgLSAkKCdib2R5ID4gaGVhZGVyJykub3V0ZXJIZWlnaHQoKSAtIDIwO1xyXG4gICAgICAgICAgICAgICAgY2xpY2thYmxlVGl0bGVDb250LmFuaW1hdGUoeyBoZWlnaHQ6IDAsIG9wYWNpdHk6IDAsIG1hcmdpbjogMCB9LCBmdW5jdGlvbih0aGlzOiBhbnkpIHtcclxuICAgICAgICAgICAgICAgICAgICQodGhpcykuaGlkZSgpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAgICAgbGV0IHMxID0gJCgnaHRtbCcpLnNjcm9sbFRvcCgpO1xyXG4gICAgICAgICAgICAgICAgbGV0IHMyID0gJCgnYm9keScpLnNjcm9sbFRvcCgpO1xyXG4gICAgICAgICAgICAgICAgcmVnaXN0ZXJGb3JtLnJlbW92ZUNsYXNzKCdoaWRkZW4tZm9ybScpO1xyXG4gICAgICAgICAgICAgICAgbGV0IHRvRm9jdXMgPSAkKCcjVXNlckZpcnN0bmFtZScpO1xyXG4gICAgICAgICAgICAgICAgaWYgKCF0b0ZvY3VzLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgIHRvRm9jdXMgPSByZWdpc3RlckZvcm0uZmluZCgnaW5wdXQ6Zmlyc3QnKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICAgICAgICAgIHRvRm9jdXMuZm9jdXMoKTtcclxuICAgICAgICAgICAgICAgICAgICAkKCdodG1sLCBib2R5Jykuc3RvcCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoJ2h0bWwnKS5zY3JvbGxUb3AoczEpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHknKS5zY3JvbGxUb3AoczIpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoJ2h0bWwsIGJvZHknKS5hbmltYXRlKHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgc2Nyb2xsVG9wOiBzY3JvbGxUb1xyXG4gICAgICAgICAgICAgICAgICAgIH0sIDg1MCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgbWFpbkh0bWxGb3JDYXJkcy5hcHBlbmQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtY29udGVudC1uZXh0LWJ0blwiPjxhIGNsYXNzPVwidGFyb3QtcmVzdWx0LXJlZ2lzdGVyLWJ0blwiIGhyZWY9XCInICsgKHJlc3VsdC5uZXh0X2xpbmsgfHwgJy8nKSArICdcIj4nICsgcmVzdWx0LnRyLmluc190aXRsZSArICc8L2E+PC9kaXY+Jyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZiAocmVzdWx0Lm90aGVyX2dhbWVzLmxlbmd0aCkge1xyXG4gICAgICAgICAgICBtYWluSHRtbC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtb3RoZXItZ2FtZXMtaGVhZFwiIHN0eWxlPVwiY29sb3I6JyArIGNhcmQuZW1iZWRfaW1hZ2VfdGV4dF9jb2xvciArICdcIj4nICsgcmVzdWx0LnRyLm1haW5fb3RoZXIgKyAnPC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIGxldCBvdGhlckdhbWVzID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1vdGhlci1nYW1lc1wiPjwvZGl2PicpO1xyXG4gICAgICAgICAgICBtYWluSHRtbC5hcHBlbmQob3RoZXJHYW1lcyk7XHJcbiAgICAgICAgICAgIGZvciAobGV0IGsgPSAwOyBrIDwgcmVzdWx0Lm90aGVyX2dhbWVzLmxlbmd0aDsgKytrKSB7XHJcbiAgICAgICAgICAgICAgICBjb25zdCBvdGhlckdhbWUgPSByZXN1bHQub3RoZXJfZ2FtZXNba107XHJcblxyXG4gICAgICAgICAgICAgICAgY29uc3QgY29udCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtb3RoZXItZ2FtZS1jb250XCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgICAgICBvdGhlckdhbWVzLmFwcGVuZChjb250KTtcclxuXHJcbiAgICAgICAgICAgICAgICBsZXQgaW1nID0gJCgnPGltZyAvPicpO1xyXG4gICAgICAgICAgICAgICAgaW1nLmF0dHIoJ3NyYycsIFRlbXBsYXRlSGVscGVyLnByZWZpeFVybChvdGhlckdhbWUuZW1iZWRfaW1hZ2UsIGNvbmZpZy5jYXJkSW1hZ2VzVXJsKSk7XHJcbiAgICAgICAgICAgICAgICBpbWcuYXR0cignYWx0Jywgb3RoZXJHYW1lLm5hbWUpO1xyXG5cclxuICAgICAgICAgICAgICAgIGNvbnQuYXBwZW5kKGltZyk7XHJcbiAgICAgICAgICAgICAgICBjb250LmFwcGVuZCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1vdGhlci1nYW1lLXRpdGxlXCIgc3R5bGU9XCJjb2xvcjonICsgb3RoZXJHYW1lLmVtYmVkX2ltYWdlX3RleHRfY29sb3IgKyAnXCI+JyArIG90aGVyR2FtZS5uYW1lICsgJzwvZGl2PicpO1xyXG4gICAgICAgICAgICAgICAgY29udC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtb3RoZXItZ2FtZS1kZXNjXCI+JyArIG90aGVyR2FtZS5kZXNjcmlwdGlvbiArICc8L2Rpdj4nKTtcclxuICAgICAgICAgICAgICAgIGNvbnQuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LW90aGVyLWdhbWUtYnRuXCI+PGEgaHJlZj1cIicgKyBvdGhlckdhbWUubGluayArICdcIj4nICsgcmVzdWx0LnRyLnNlZV9nYW1lICsgJzwvYT48L2Rpdj4nKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgbGV0IHNpZGVIdG1sID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgaHRtbC5hcHBlbmQoc2lkZUh0bWwpO1xyXG5cclxuICAgICAgICBsZXQgc2lkZVJldkh0bWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtcmV2XCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgc2lkZVJldkh0bWwuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtdGl0bGVcIj4nICsgcmVzdWx0LnRyLnNpZGVfcmV2X3RpdGxlICsgJzwvZGl2PicpO1xyXG4gICAgICAgIHNpZGVSZXZIdG1sLmFwcGVuZCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLWRlc2NcIj4nICsgcmVzdWx0LnRyLnNpZGVfcmV2X2Rlc2MgKyAnPC9kaXY+Jyk7XHJcbiAgICAgICAgc2lkZUh0bWwuYXBwZW5kKHNpZGVSZXZIdG1sKTtcclxuXHJcbiAgICAgICAgbGV0IHNpZGVSZXZDYXJkc0h0bWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtcmV2LWNhcmRzXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgZm9yIChsZXQgayA9IDA7IGsgPCBzZWxlY3RlZENhcmRJdGVtSWRzLmxlbmd0aDsgKytrKSB7XHJcbiAgICAgICAgICAgIGNvbnN0IGkgPSArc2VsZWN0ZWRDYXJkSXRlbUlkc1trXTtcclxuICAgICAgICAgICAgY29uc3QgY2FyZEl0ZW0gPSBjYXJkSXRlbXNbaV0uQ2FyZEl0ZW07XHJcbiAgICAgICAgICAgIGNvbnN0IGNhcmRJdGVtTGFuZyA9IGNhcmRJdGVtc1tpXS5DYXJkSXRlbUxhbmc7XHJcbiAgICAgICAgICAgIGxldCBlbCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtc2lkZS1yZXYtY2FyZFwiIHRhYmluZGV4PVwiMFwiPjwvZGl2PicpO1xyXG5cclxuICAgICAgICAgICAgbGV0IGltZ0Zyb250ID0gJCgnPGltZyAvPicpO1xyXG4gICAgICAgICAgICBpbWdGcm9udC5hdHRyKCdzcmMnLCBUZW1wbGF0ZUhlbHBlci5wcmVmaXhVcmwoY2FyZEl0ZW0uaW1hZ2UsIGNvbmZpZy5jYXJkSXRlbUltYWdlc1VybCkpO1xyXG4gICAgICAgICAgICBpbWdGcm9udC5hdHRyKCdhbHQnLCBjYXJkSXRlbUxhbmcudGl0bGUpO1xyXG5cclxuICAgICAgICAgICAgZWwuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtcmV2LWNhcmQtdGl0bGVcIj4nICsgY2FyZEl0ZW1MYW5nLnRpdGxlICsgJzwvZGl2PicpO1xyXG4gICAgICAgICAgICBlbC5hcHBlbmQoJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLXJldi1jYXJkLWltYWdlXCI+PC9kaXY+JykuYXBwZW5kKGltZ0Zyb250KSk7XHJcbiAgICAgICAgICAgIGVsLmFwcGVuZCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLXJldi1jYXJkLWRlc2NyaXB0aW9uXCI+JyArIGNhcmRJdGVtTGFuZy5kZXNjcmlwdGlvbiArICc8L2Rpdj4nKTtcclxuXHJcbiAgICAgICAgICAgIHNpZGVSZXZDYXJkc0h0bWwuYXBwZW5kKGVsKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgc2lkZVJldkh0bWwuYXBwZW5kKHNpZGVSZXZDYXJkc0h0bWwpO1xyXG5cclxuICAgICAgICBsZXQgc2lkZVJldkh0bWxNb2JpbGUgPSBzaWRlUmV2SHRtbC5jbG9uZSgpO1xyXG4gICAgICAgIHNpZGVSZXZIdG1sTW9iaWxlLmFkZENsYXNzKCdtb2JpbGUnKTtcclxuICAgICAgICBodG1sLnByZXBlbmQoc2lkZVJldkh0bWxNb2JpbGUpO1xyXG5cclxuICAgICAgICBsZXQgc2lkZUV4cEh0bWwgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtZXhwXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgc2lkZUh0bWwuYXBwZW5kKHNpZGVFeHBIdG1sKTtcclxuICAgICAgICBzaWRlRXhwSHRtbC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtc2lkZS10aXRsZVwiPicgKyByZXN1bHQudHIuc2lkZV9leHBfdGl0bGUgKyAnPC9kaXY+Jyk7XHJcblxyXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgcmVzdWx0LnJlbGF0ZWRfZXhwZXJ0cy5sZW5ndGg7ICsraSkge1xyXG4gICAgICAgICAgICBjb25zdCBleHBlcnQgPSByZXN1bHQucmVsYXRlZF9leHBlcnRzW2ldO1xyXG4gICAgICAgICAgICBjb25zdCBlbCA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtc2lkZS1leHAtYm94XCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIHNpZGVFeHBIdG1sLmFwcGVuZChlbCk7XHJcblxyXG4gICAgICAgICAgICBjb25zdCBlbERpdjEgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtZXhwLWJveC1pbWdcIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgZWwuYXBwZW5kKGVsRGl2MSk7XHJcbiAgICAgICAgICAgIGVsRGl2MS5hcHBlbmQoJzxpbWcgY2xhc3M9XCJpbWctcmVzcG9uc2l2ZSBpbWctY2lyY2xlIGltZy1jb25cIiBhbHQ9XCInICsgZXhwZXJ0Lm5hbWUgKyAnIEltYWdlXCIgc3JjPVwiJyArIChleHBlcnQucHJvZmlsZV9pbWFnZSB8fCByZXN1bHQuZGVmYXVsdF9leHBlcnRfcHJvZmlsZV9pbWFnZSkgKyAnXCIgLz4nKTtcclxuICAgICAgICAgICAgZWxEaXYxLmFwcGVuZCgnPHNwYW4gY2xhc3M9XCJleHAtc3RhdHVzIGV4cC1zdGF0dXMtJyArIGV4cGVydC5zdGF0dXMgKyAnXCIgdGl0bGU9XCInICsgZXhwZXJ0LnN0YXR1cyArICdcIj48L3NwYW4+Jyk7XHJcblxyXG4gICAgICAgICAgICBjb25zdCBlbERpdjIgPSAkKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtZXhwLWJveC1pbmZvXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIGVsLmFwcGVuZChlbERpdjIpO1xyXG4gICAgICAgICAgICBlbERpdjIuYXBwZW5kKCc8ZGl2IGNsYXNzPVwidGFyb3QtcmVzdWx0LXNpZGUtZXhwLWJveC10aXRsZVwiPicgKyBleHBlcnQubmFtZSArICAnPHNwYW4gY2xhc3M9XCJleHAtcmF0aW5nXCI+PGkgY2xhc3M9XCJmYSBmYS1zdGFyXCI+PC9pPicgKyAoTWF0aC5yb3VuZChleHBlcnQucmF0aW5nKjEwKS8xMCkgKyAnPC9zcGFuPjwvZGl2PicpO1xyXG5cclxuICAgICAgICAgICAgY29uc3QgZWxDYXRzID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLWV4cC1ib3gtY2F0c1wiPjwvZGl2PicpO1xyXG4gICAgICAgICAgICBlbERpdjIuYXBwZW5kKGVsQ2F0cyk7XHJcbiAgICAgICAgICAgIGZvciAobGV0IGogPSAwOyBqIDwgTWF0aC5taW4oMywgZXhwZXJ0LmNhdGVnb3JpZXMubGVuZ3RoKTsgKytqKSB7XHJcbiAgICAgICAgICAgICAgICBlbENhdHMuYXBwZW5kKCc8c3Bhbj4nICsgZXhwZXJ0LmNhdGVnb3JpZXNbal0ubmFtZSArICc8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGNvbnN0IGVsRGl2MyA9ICQoJzxkaXYgY2xhc3M9XCJ0YXJvdC1yZXN1bHQtc2lkZS1leHAtYm94LWFjdGlvbnNcIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgZWwuYXBwZW5kKGVsRGl2Myk7XHJcbiAgICAgICAgICAgIGVsRGl2My5hcHBlbmQoJzxhIGhyZWY9XCInICsgZXhwZXJ0LmxpbmsgKyAnXCI+PHNwYW4gY2xhc3M9XCJleHAtdGVsXCI+PGk+PC9pPicgKyByZXN1bHQudHIudGVsICsgJzwvc3Bhbj48L2E+Jyk7XHJcbiAgICAgICAgICAgIGVsRGl2My5hcHBlbmQoJzxhIGhyZWY9XCInICsgZXhwZXJ0LmxpbmsgKyAnXCI+PHNwYW4gY2xhc3M9XCJleHAtZW1haWxcIj48aT48L2k+JyArIHJlc3VsdC50ci5lbWFpbCArICc8L3NwYW4+PC9hPicpO1xyXG5cclxuICAgICAgICAgICAgY29uc3QgZWxEZXNjID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLWV4cC1kZXNjXCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIHNpZGVFeHBIdG1sLmFwcGVuZChlbERlc2MpO1xyXG4gICAgICAgICAgICBlbERlc2MuaHRtbCgnPHNwYW4+JyArIHJlc3VsdC50ci5zaWRlX2V4cF9zZWVfdGl0bGUgKyAnPC9zcGFuPjxwPicgKyByZXN1bHQudHIuc2lkZV9leHBfc2VlX2Rlc2MgKyAnPC9wPicpO1xyXG5cclxuXHJcbiAgICAgICAgICAgIGNvbnN0IGVsQnRuID0gJCgnPGRpdiBjbGFzcz1cInRhcm90LXJlc3VsdC1zaWRlLWV4cC1idG5cIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgc2lkZUV4cEh0bWwuYXBwZW5kKGVsQnRuKTtcclxuICAgICAgICAgICAgZWxCdG4uaHRtbCgnPGEgaHJlZj1cIicgKyBleHBlcnQubGluayArICdcIj4nICsgcmVzdWx0LnRyLnNpZGVfZXhwX3NlZV9tb3JlICsgJyAnICsgZXhwZXJ0Lm5hbWUudG9VcHBlckNhc2UoKSArICc8L2E+Jyk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvL1xyXG4gICAgICAgIHJlc29sdmUoKTtcclxuICAgIH0pO1xyXG59XHJcbiIsIi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL2luaXRpYWxTdGVwXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL3Nob3dDYXJkc1N0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vc2h1ZmZsZUNhcmRzU3RlcFwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9jaG9vc2VDYXJkc1N0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vcHJvY2Vzc1N0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vcmVzdWx0U3RlcFwiIC8+XG4iLCIvLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9HbG9iYWxzXCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL2VudW1zL1Rhcm90R2FtZVN0ZXBcIiAvPlxuLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vaGVscGVycy9UZW1wbGF0ZUhlbHBlclwiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9hbmltYXRpb25zL2luZGV4XCIgLz5cbi8vLyA8cmVmZXJlbmNlIHBhdGg9XCIuL3N0ZXBzL2luZGV4XCIgLz5cblxuLyoqXG4gKiBAYnJpZWYgVGhpcyBpcyB0aGUgbWFpbiBjbGFzcyBmb3IgdGhlIGdhbWUuXG4gKi9cbmNsYXNzIFRhcm90R2FtZSB7XG4gICAgLy8vIFRoZSBjb25maWd1cmF0aW9uIHVzZWQgaW4gdGhpcyBnYW1lXG4gICAgcHJpdmF0ZSBjb25maWc6IGFueSA9IG51bGw7XG5cbiAgICAvLy8gVGhlIGpxdWVyeSBjb250YWluZXIgY29udGFpbmluZyB0aGUgZ2FtZVxuICAgIHByaXZhdGUgY29udGFpbmVyOiBhbnkgPSBudWxsO1xuXG4gICAgLy8vIFNldCB0byBcXGMgdHJ1ZSBpZiB0aGUgZ2FtZSBpcyByZWFkeSAoYWxsIGl0cyBhc3NldHMgYXJlIGxvYWRlZClcbiAgICBwcml2YXRlIGlzUmVhZHkgPSBmYWxzZTtcblxuICAgIC8vLyBTZXQgdG8gXFxjIHRydWUgaWYgdGhlIHdpbmRvdyBpcyBsb2FkZWQgKGkuZS4gd2luZG93Lm9uKCdsb2FkJykgaXMgY2FsbGVkKVxuICAgIHByaXZhdGUgaXNXaW5kb3dMb2FkZWQgPSBmYWxzZTtcblxuICAgIC8vLyBIb3cgbWFueSBpbWFnZXMgcmVtYWluIHRvIHByZWxvYWQgYmVmb3JlIGFzc3VtaW5nIHRoZSBnYW1lIGlzIHJlYWR5XG4gICAgcHJpdmF0ZSByZW1haW5pbmdJbWFnZXNUb1ByZWxvYWQgPSAwO1xuXG4gICAgLy8vIFJlc3VsdCBvYmplY3QgYWZ0ZXIgc2VsZWN0aW9uXG4gICAgcHJpdmF0ZSByZXN1bHQ6IGFueSA9IHt9O1xuXG4gICAgLy8vIENhcmQgSXRlbSBJZHMgdGhhdCBhcmUgc2VsZWN0ZWRcbiAgICBwcml2YXRlIHNlbGVjdGVkQ2FyZEl0ZW1JZHM6IG51bWJlcltdID0gW107XG5cbiAgICAvLy8gVGhlIGpxdWVyeSBzcGlubmVyIGVsZW1lbnRcbiAgICBwcml2YXRlIHNwaW5uZXJFbDogYW55ID0gbnVsbDtcblxuICAgIC8vLyBDdXJyZW50IHN0ZXBcbiAgICBwcml2YXRlIHN0ZXA6IFRhcm90R2FtZVN0ZXAgPSBUYXJvdEdhbWVTdGVwLk5PTkU7XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgVGhlIGNvbnN0cnVjdG9yIG9mIHRoaXMgY2xhc3MuXG4gICAgICogQHBhcmFtIEpRdWVyeXxIVE1MRWxlbWVudCBjb250YWluZXIgVGhlIGNvbnRhaW5lciBvZiB0aGUgdGFyb3QgZ2FtZS5cbiAgICAgKiBAcGFyYW0gYW55IGNvbmZpZyBUaGUgY29uZmlndXJhdGlvbiB0byB1c2UsIHNob3VsZCBjb250YWluIHRoZSBmb2xsb3dpbmcga2V5czpcbiAgICAgKiAgICAgICAgICAgICAgICAgICAgICBgY2FyZGAsIGBjYXJkSXRlbWAsIGBjYXJkSW1hZ2VzVXJsYCBhbmQgYGNhcmRJdGVtSW1hZ2VzVXJsYC5cbiAgICAgKi9cbiAgICBjb25zdHJ1Y3Rvcihjb250YWluZXI6IGFueSwgY29uZmlnOiBhbnkpIHtcbiAgICAgICAgdGhpcy5jb25maWcgPSBjb25maWc7XG4gICAgICAgIHRoaXMuY29udGFpbmVyID0gJChjb250YWluZXIpO1xuXG4gICAgICAgIC8vIHByZWxvYWQgaW1hZ2VzIHJlbGF0ZWQgdG8gdGhlIGNhcmQgbW9kZWxcbiAgICAgICAgaWYgKGNvbmZpZyAmJiBjb25maWcuY2FyZCAmJiBjb25maWcuY2FyZC5DYXJkKSB7XG4gICAgICAgICAgICB0aGlzLnByZWxvYWRJbWFnZXNGcm9tQ29uZmlnKGNvbmZpZy5jYXJkLkNhcmQsIDxzdHJpbmc+IGNvbmZpZy5jYXJkSW1hZ2VzVXJsKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIHByZWxvYWQgaW1hZ2VzIHJlbGF0ZWQgdG8gdGhlIGNhcmQgaXRlbSBtb2RlbHNcbiAgICAgICAgaWYgKGNvbmZpZyAmJiBjb25maWcuY2FyZEl0ZW1zKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBrIGluIGNvbmZpZy5jYXJkSXRlbXMpIHtcbiAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmNhcmRJdGVtc1trXSAmJiBjb25maWcuY2FyZEl0ZW1zW2tdLkNhcmRJdGVtKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMucHJlbG9hZEltYWdlc0Zyb21Db25maWcoY29uZmlnLmNhcmRJdGVtc1trXS5DYXJkSXRlbSwgPHN0cmluZz4gY29uZmlnLmNhcmRJdGVtSW1hZ2VzVXJsKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBzaG93IGxvYWRpbmcgc3Bpbm5lciBhbmQgd2FpdCBmb3IgdGhlIHdpbmRvdyB0byBsb2FkXG4gICAgICAgIHRoaXMuc2hvd1NwaW5uZXIoKTtcblxuICAgICAgICAvLyB3YWl0IGZvciB0aGUgd2luZG93IHRvIGxvYWRcbiAgICAgICAgLy90aGlzLmlzV2luZG93TG9hZGVkID0gdHJ1ZTsgLy8gYWN0dWFsbHksIGRvbid0IHdhaXQuIENvbW1lbnQgdGhpcyBhbmQgdW5jb21tZW50IG5leHQgbGluZXMgaWYgeW91IHdpc2ggdG8gd2FpdC5cbiAgICAgICAgJCh3aW5kb3cpLm9uKCdsb2FkJywgKCkgPT4ge1xuICAgICAgICAgICAgdGhpcy5pc1dpbmRvd0xvYWRlZCA9IHRydWU7XG4gICAgICAgICAgICB0aGlzLmNoZWNrUmVhZHkoKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIEFkZHMgdGhlIGdpdmVuIGNhcmQgaXRlbSBpZCB0byB0aGUgbGlzdCBvZiBzZWxlY3RlZCBjYXJkIGl0ZW0gaWRzLlxuICAgICAqIEBwYXJhbSBudW1iZXIgY2FyZEl0ZW1JZCBUaGUgY2FyZCBpdGVtIGlkIHRvIGFkZC5cbiAgICAgKi9cbiAgICBwdWJsaWMgYWRkU2VsZWN0ZWRDYXJkSXRlbUlkKGNhcmRJdGVtSWQ6IG51bWJlcikge1xuICAgICAgICB0aGlzLnNlbGVjdGVkQ2FyZEl0ZW1JZHMucHVzaChjYXJkSXRlbUlkKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgQ2hlY2tzIGlmIHRoZSBnYW1lIGlzIHJlYWR5IChpbiBwYXJ0aWN1bGFyLCBpZiBhbGwgaW1hZ2VzIGFyZSBsb2FkZWQpIGFuZCBzZXQgdGhlIGlzUmVhZHkgcHJpdmF0ZSBmaWVsZC5cbiAgICAgKi9cbiAgICBwcml2YXRlIGNoZWNrUmVhZHkoKSB7XG4gICAgICAgIGlmICh0aGlzLmlzUmVhZHkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLnJlbWFpbmluZ0ltYWdlc1RvUHJlbG9hZCA+IDAgfHwgIXRoaXMuaXNXaW5kb3dMb2FkZWQpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuaXNSZWFkeSA9IHRydWU7XG4gICAgICAgIHRoaXMuaGlkZVNwaW5uZXIoKTtcblxuICAgICAgICAvLyBpbml0IHRoZSBnYW1lXG4gICAgICAgIHRoaXMuaW5pdCgpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZXR1cm5zIHRoZSBjYXJkcyBkaXN0cmlidXRpb24gYW5pbWF0aW9uIGFuZCBvcmRlcmluZyBmdW5jdGlvbnMgYmFzZWQgb24gY3VycmVudCBnYW1lIGRpc3BsYXkgbW9kZS5cbiAgICAgKiBAcmV0dXJuIGFueSBUaGUgY2FyZHMgZGlzdHJpYnV0aW9uIGFuaW1hdGlvbiBhbmQgb3JkZXJpbmcgZnVuY3Rpb25zIGJhc2VkIG9uIGN1cnJlbnQgZ2FtZSBkaXNwbGF5IG1vZGVcbiAgICAgKi9cbiAgICBwdWJsaWMgZ2V0Q2FyZERpc3RyaWJ1dGlvbkFuaW1hdGlvbkZuKCkge1xuICAgICAgICBsZXQgYW5pbURpc3RyaWJ1dGVGbiA9IGFuaW1EaXN0cmlidXRlQ2FyZHNMaW5lO1xuICAgICAgICBsZXQgZGlzdHJpYnV0ZU9yZGVyRm4gPSBEaXN0cmlidXRlT3JkZXIub3V0Rmlyc3REaXN0cmlidXRpb247XG4gICAgICAgIGNvbnN0IGRpc3BsYXlNb2RlID0gK3RoaXMuY29uZmlnLmNhcmQuQ2FyZC5kaXNwbGF5X21vZGU7XG4gICAgICAgIGlmIChkaXNwbGF5TW9kZSA9PT0gRGlzcGxheU1vZGUuTElORSkge1xuICAgICAgICAgICAgYW5pbURpc3RyaWJ1dGVGbiA9IGFuaW1EaXN0cmlidXRlQ2FyZHNMaW5lO1xuICAgICAgICB9IGVsc2UgaWYgKGRpc3BsYXlNb2RlID09PSBEaXNwbGF5TW9kZS5UV09fTElORVMpIHtcbiAgICAgICAgICAgIGFuaW1EaXN0cmlidXRlRm4gPSBhbmltRGlzdHJpYnV0ZUNhcmRzVHdvTGluZXM7XG4gICAgICAgIH0gZWxzZSBpZiAoZGlzcGxheU1vZGUgPT09IERpc3BsYXlNb2RlLlNLRVdFRF9MSU5FKSB7XG4gICAgICAgICAgICBhbmltRGlzdHJpYnV0ZUZuID0gYW5pbURpc3RyaWJ1dGVDYXJkc1NrZXdlZExpbmU7XG4gICAgICAgIH0gZWxzZSBpZiAoZGlzcGxheU1vZGUgPT09IERpc3BsYXlNb2RlLkFSQ19MSU5FKSB7XG4gICAgICAgICAgICBhbmltRGlzdHJpYnV0ZUZuID0gYW5pbURpc3RyaWJ1dGVDYXJkc0FyYztcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgYW5pbWF0aW9uRm46IGFuaW1EaXN0cmlidXRlRm4sXG4gICAgICAgICAgICBvcmRlckZuOiBkaXN0cmlidXRlT3JkZXJGblxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBSZXR1cm5zIGEgcmVmZXJlbmNlIHRvIHRoZSBnYW1lIGNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuICAgICAqIEByZXR1cm4gSlF1ZXJ5IEEgcmVmZXJlbmNlIHRvIHRoZSBnYW1lIGNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuICAgICAqL1xuICAgIHB1YmxpYyBnZXRDb25maWcoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmNvbmZpZztcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgUmV0dXJucyB0aGUganF1ZXJ5IGNvbnRhaW5lciBjb250YWluaW5nIHRoaXMgZ2FtZS5cbiAgICAgKiBAcmV0dXJuIEpRdWVyeSBUaGUgY29udGFpbmVyIGNvbnRhaW5pbmcgdGhpcyBnYW1lLlxuICAgICAqL1xuICAgIHB1YmxpYyBnZXRDb250YWluZXIoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmNvbnRhaW5lcjtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgUmV0dXJucyB0aGUgc2VsZWN0aW9uIHJlc3VsdCB0aGF0IHdhcyBxdWVyaWVkIGZyb20gYmFja2VuZC5cbiAgICAgKiBAcmV0dXJuIGFueSBUaGUgc2VsZWN0aW9uIHJlc3VsdCB0aGF0IHdhcyBxdWVyaWVkIGZyb20gYmFja2VuZC5cbiAgICAgKi9cbiAgICBwdWJsaWMgZ2V0UmVzdWx0KCkge1xuICAgICAgICByZXR1cm4gdGhpcy5yZXN1bHQ7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFJldHVybnMgdGhlIHNlbGVjdGVkIGNhcmRzIGluIGN1cnJlbnQgZ2FtZS5cbiAgICAgKiBAcmV0dXJuIG51bWJlcltdIFRoZSBzZWxlY3RlZCBjYXJkcyBpbiBjdXJyZW50IGdhbWUuXG4gICAgICovXG4gICAgcHVibGljIGdldFNlbGVjdGVkQ2FyZEl0ZW1JZHMoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLnNlbGVjdGVkQ2FyZEl0ZW1JZHM7XG4gICAgfVxuXG4gICAgcHVibGljIGdldFN0ZXAoKTogVGFyb3RHYW1lU3RlcCB7XG4gICAgICAgIHJldHVybiB0aGlzLnN0ZXA7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIEhpZGVzIHRoZSBsb2FkaW5nIHNwaW5uZXIuXG4gICAgICovXG4gICAgcHVibGljIGhpZGVTcGlubmVyKCkge1xuICAgICAgICBpZiAoIXRoaXMuc3Bpbm5lckVsKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5zcGlubmVyRWwucmVtb3ZlKCk7XG4gICAgICAgIHRoaXMuc3Bpbm5lckVsID0gbnVsbDtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgSW5pdGlhdGVzIHRoZSBnYW1lLlxuICAgICAqL1xuICAgIHByaXZhdGUgYXN5bmMgaW5pdCgpIHtcbiAgICAgICAgdGhpcy5zZWxlY3RlZENhcmRJdGVtSWRzID0gW107XG4gICAgICAgIHRoaXMuc3RlcCA9IFRhcm90R2FtZVN0ZXAuTk9ORTtcbiAgICAgICAgYXdhaXQgaW5pdGlhbFN0ZXAodGhpcyk7XG4gICAgICAgIHRoaXMuc3RlcCA9IFRhcm90R2FtZVN0ZXAuSU5JVElBVEVEX0dBTUU7XG4gICAgICAgIGF3YWl0IHNob3dDYXJkc1N0ZXAodGhpcyk7XG4gICAgICAgIHRoaXMuc3RlcCA9IFRhcm90R2FtZVN0ZXAuUkVBRFlfVE9fU0hVRkZMRTtcbiAgICAgICAgYXdhaXQgc2h1ZmZsZUNhcmRzU3RlcCh0aGlzKTtcbiAgICAgICAgdGhpcy5zdGVwID0gVGFyb3RHYW1lU3RlcC5DSE9PU0VfQ0FSRFM7XG4gICAgICAgIGF3YWl0IGNob29zZUNhcmRzU3RlcCh0aGlzKTtcbiAgICAgICAgdGhpcy5zdGVwID0gVGFyb3RHYW1lU3RlcC5QT1NUX0NIT09TRV9DQVJEUztcbiAgICAgICAgYXdhaXQgdGhpcy5xdWVyeVByb2Nlc3NTZWxlY3Rpb24oKTtcbiAgICAgICAgdGhpcy5zdGVwID0gVGFyb3RHYW1lU3RlcC5QUk9DRVNTX1NFTEVDVElPTjtcbiAgICAgICAgYXdhaXQgcHJvY2Vzc1N0ZXAodGhpcyk7XG4gICAgICAgIHRoaXMuc3RlcCA9IFRhcm90R2FtZVN0ZXAuU0hPV19SRVNVTFRTO1xuICAgICAgICBhd2FpdCByZXN1bHRTdGVwKHRoaXMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBicmllZiBFdmVudCBjYWxsZWQgd2hlbiBvbmUgc2luZ2xlIGltYWdlIGltYWdlIGlzIGRvbmUgcHJlbG9hZGluZy5cbiAgICAgKi9cbiAgICBwcml2YXRlIG9uUHJlbG9hZEltYWdlRmluaXNoKCkge1xuICAgICAgICB0aGlzLnJlbWFpbmluZ0ltYWdlc1RvUHJlbG9hZC0tO1xuICAgICAgICB0aGlzLmNoZWNrUmVhZHkoKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgUHJlbG9hZCBhbGwgaW1hZ2VzIGluIHRoZSBnaXZlbiBkYXRhIChpLmUuIGFueSB2YWx1ZSB3aXRoIGEga2V5IGVuZGluZyB3aXRoIHRoZSBgJ2ltYWdlJ2ApLlxuICAgICAqIEBwYXJhbSBhbnkgZGF0YSBUaGUgdXJsIG9mIHRoZSBpbWFnZSB0byBwcmVsb2FkLlxuICAgICAqIEBwYXJhbSBjYWxsYWJsZXxudWxsIGxvYWRDYWxsYmFjayAgICBUaGUgZnVuY3Rpb24gdG8gY2FsbCB3aGVuIHRoZSBpbWFnZSBpcyBkb25lIHByZWxvYWRpbmcuXG4gICAgICogQHBhcmFtIGNhbGxhYmxlfG51bGwgZXJyb3JDYWxsYmFjayAgIFRoZSBmdW5jdGlvbiB0byBjYWxsIHdoZW4gdGhlcmUgd2FzIGFuIGVycm9yIHByZWxvYWRpbmcgdGhlIGltYWdlLlxuICAgICAqL1xuICAgIHByaXZhdGUgcHJlbG9hZEltYWdlc0Zyb21Db25maWcoZGF0YTogYW55LCB1cmxQcmVmOiBzdHJpbmcpIHtcbiAgICAgICAgZm9yIChsZXQgayBpbiBkYXRhKSB7XG4gICAgICAgICAgICBpZiAoay5lbmRzV2l0aCgnaW1hZ2UnKSAmJiBkYXRhW2tdKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5yZW1haW5pbmdJbWFnZXNUb1ByZWxvYWQrKztcbiAgICAgICAgICAgICAgICBUZW1wbGF0ZUhlbHBlci5wcmVsb2FkSW1hZ2UodXJsUHJlZiArICg8c3RyaW5nPiBkYXRhW2tdKSwgdGhpcy5vblByZWxvYWRJbWFnZUZpbmlzaC5iaW5kKHRoaXMpLCB0aGlzLm9uUHJlbG9hZEltYWdlRmluaXNoLmJpbmQodGhpcykpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIFF1ZXJpZXMgdGhlIGJhY2tlbmQgd2l0aCBjdXJyZW50IHNlbGVjdGlvbiBhbmQgdXBkYXRlcyBnYW1lIGRhdGEuXG4gICAgICovXG4gICAgcHJpdmF0ZSBxdWVyeVByb2Nlc3NTZWxlY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZTx2b2lkPigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgICAgICBjb25zdCB0aGF0ID0gdGhpcztcbiAgICAgICAgICAgIGxldCByZWFsX2NhcmRfaXRlbV9pZHMgPSBbXTtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgdGhpcy5zZWxlY3RlZENhcmRJdGVtSWRzLmxlbmd0aDsgKytpKSB7XG4gICAgICAgICAgICAgICAgcmVhbF9jYXJkX2l0ZW1faWRzLnB1c2godGhpcy5jb25maWcuY2FyZEl0ZW1zW3RoaXMuc2VsZWN0ZWRDYXJkSXRlbUlkc1tpXV0uQ2FyZEl0ZW0uY2FyZF9pdGVtX2lkKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiAnL2NhcmRzL3Byb2Nlc3Nfc2VsZWN0aW9uJyxcbiAgICAgICAgICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgICAgIGNhcmRfaWQ6IHRoaXMuY29uZmlnLmNhcmQuQ2FyZC5jYXJkX2lkLFxuICAgICAgICAgICAgICAgICAgICBsYW5nX2lkOiB0aGlzLmNvbmZpZy5jYXJkLkNhcmRMYW5nLmxhbmdfaWQsXG4gICAgICAgICAgICAgICAgICAgIGNhcmRfaXRlbV9pZHM6IHJlYWxfY2FyZF9pdGVtX2lkc1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJ1xuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihkYXRhOiBhbnkpIHtcbiAgICAgICAgICAgICAgICBpZiAoIWRhdGEgfHwgKHR5cGVvZiBkYXRhLmVycm9yICE9PSAndW5kZWZpbmVkJyAmJiBkYXRhLmVycm9yKSkge1xuICAgICAgICAgICAgICAgICAgICByZWplY3QoKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB0aGF0LnJlc3VsdCA9IGRhdGE7XG4gICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgfSkuZmFpbChmdW5jdGlvbihqcVhIUjogYW55LCB0ZXh0U3RhdHVzOiBzdHJpbmcsIGVycm9yVGhyb3duOiBhbnkpIHtcbiAgICAgICAgICAgICAgICByZWplY3QoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgU2hvd3MgdGhlIGxvYWRpbmcgc3Bpbm5lci5cbiAgICAgKi9cbiAgICBwdWJsaWMgc2hvd1NwaW5uZXIoKSB7XG4gICAgICAgIHRoaXMuaGlkZVNwaW5uZXIoKTtcbiAgICAgICAgdGhpcy5zcGlubmVyRWwgPSAkKCc8ZGl2IGNsYXNzPVwiY2FyZHMtc3Bpbm5lci1jb250YWluZXJcIj48ZGl2IGNsYXNzPVwiY2FyZHMtc3Bpbm5lclwiPkxvYWRpbmcgLi4uPC9kaXY+PC9kaXY+Jyk7XG4gICAgICAgIHRoaXMuY29udGFpbmVyLmFwcGVuZCh0aGlzLnNwaW5uZXJFbCk7XG4gICAgfVxufVxuIiwiLy8vIDxyZWZlcmVuY2UgcGF0aD1cIi4vR2xvYmFsc1wiIC8+XG4vLy8gPHJlZmVyZW5jZSBwYXRoPVwiLi9UYXJvdEdhbWVcIiAvPlxuXG5pZiAodHlwZW9mICg8YW55PndpbmRvdykuVGFyb3RDb25maWcgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgJCgoPGFueT53aW5kb3cpLlRhcm90Q29uZmlnLnNlbGVjdG9yKS5lYWNoKGZ1bmN0aW9uKGk6IGFueSwgdjogYW55KSB7XG4gICAgICAgIG5ldyBUYXJvdEdhbWUoJCh2KSwgKDxhbnk+IHdpbmRvdykuVGFyb3RDb25maWcpO1xuICAgIH0pO1xufVxuIiwiZW51bSBEaXNwbGF5TW9kZSB7XG4gICAgLy8vXG4gICAgTElORSA9IDEsXG5cbiAgICAvLy9cbiAgICBUV09fTElORVMgPSAyLFxuXG4gICAgLy8vXG4gICAgU0tFV0VEX0xJTkUgPSAzLFxuXG4gICAgLy8vXG4gICAgQVJDX0xJTkUgPSA0LFxufVxuIiwiZW51bSBHYW1lVHlwZSB7XG4gICAgLy8vXG4gICAgWUVTX05PID0gMSxcblxuICAgIC8vL1xuICAgIFNJTkdMRSA9IDIsXG5cbiAgICAvLy9cbiAgICBGT1JUVU5FID0gMyxcblxuICAgIC8vL1xuICAgIExPVkUgPSA0LFxufVxuIiwiLyoqXG4gKiBAYnJpZWYgQSBoZWxwZXIgd2l0aCBtaXNjZWxsYW5lb3VzIG1ldGhvZHMgdG8gZGVmaW5lIGNhcmQgZGlzdHJpYnV0aW9uIG9yZGVyLlxuICovXG5jbGFzcyBEaXN0cmlidXRlT3JkZXIge1xuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIENvbXB1dGVzIGRpc3RyaWJ1dGlvbiBkZWxheSBzbyB0aGF0IHRoZSBjYXJkcyBhcmUgZGlzdHJpYnV0ZWQgb25lIGFmdGVyIGFub3RoZXIuXG4gICAgICogQHBhcmFtIG51bWJlciBiYXNlRGVsYXkgIEJhc2UgZGlzdHJpYnV0aW9uIGRlbGF5IGluIG1pbGxpc2Vjb25kcy5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGkgICAgICAgICAgVGhlIGluZGV4IG9mIHRoZSBjYXJkIHRvIGNvbXB1dGUgdGhlIGRlbGF5IGZvci5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGNvdW50ICAgICAgSG93IG1hbnkgY2FyZHMgdGhlcmUgYXJlLlxuICAgICAqIEByZXR1cm4gbnVtYmVyIFRoZSBjb21wdXRlZCBkZWxheS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIG9yZGVyZWREaXN0cmlidXRpb24oYmFzZURlbGF5OiBudW1iZXIsIGk6IG51bWJlciwgY291bnQ6IG51bWJlcikge1xuICAgICAgICByZXR1cm4gaSAqIGJhc2VEZWxheTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAYnJpZWYgQ29tcHV0ZXMgZGlzdHJpYnV0aW9uIGRlbGF5IHNvIHRoYXQgdGhlIGZpcnN0IGFuZCBsYXN0IGNhcmQgYXJlIGRpc3RyaWJ1dGVkIGZpcnN0IGFuZCBjZW50cmFsIGNhcmRzIGxhc3QuXG4gICAgICogQHBhcmFtIG51bWJlciBiYXNlRGVsYXkgIEJhc2UgZGlzdHJpYnV0aW9uIGRlbGF5IGluIG1pbGxpc2Vjb25kcy5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGkgICAgICAgICAgVGhlIGluZGV4IG9mIHRoZSBjYXJkIHRvIGNvbXB1dGUgdGhlIGRlbGF5IGZvci5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGNvdW50ICAgICAgSG93IG1hbnkgY2FyZHMgdGhlcmUgYXJlLlxuICAgICAqIEByZXR1cm4gbnVtYmVyIFRoZSBjb21wdXRlZCBkZWxheS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIG91dEZpcnN0RGlzdHJpYnV0aW9uKGJhc2VEZWxheTogbnVtYmVyLCBpOiBudW1iZXIsIGNvdW50OiBudW1iZXIpIHtcbiAgICAgICAgcmV0dXJuIE1hdGguYWJzKGkgLSAoaSA+IGNvdW50IC8gMiA/IGNvdW50IC0gMSA6IDApKSAqIDIgKiBiYXNlRGVsYXk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGJyaWVmIENvbXB1dGVzIGRpc3RyaWJ1dGlvbiBkZWxheSBzbyB0aGF0IGFsbCB0aGUgY2FyZHMgYXJlIGRpc3RyaWJ1dGVkIGF0IHRoZSBzYW1lIHRpbWUuXG4gICAgICogQHBhcmFtIG51bWJlciBiYXNlRGVsYXkgIEJhc2UgZGlzdHJpYnV0aW9uIGRlbGF5IGluIG1pbGxpc2Vjb25kcy5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGkgICAgICAgICAgVGhlIGluZGV4IG9mIHRoZSBjYXJkIHRvIGNvbXB1dGUgdGhlIGRlbGF5IGZvci5cbiAgICAgKiBAcGFyYW0gbnVtYmVyIGNvdW50ICAgICAgSG93IG1hbnkgY2FyZHMgdGhlcmUgYXJlLlxuICAgICAqIEByZXR1cm4gbnVtYmVyIFRoZSBjb21wdXRlZCBkZWxheS5cbiAgICAgKi9cbiAgICBwdWJsaWMgc3RhdGljIHN5bmNocm9uaW91c0Rpc3RyaWJ1dGlvbihiYXNlRGVsYXk6IG51bWJlciwgaTogbnVtYmVyLCBjb3VudDogbnVtYmVyKSB7XG4gICAgICAgIHJldHVybiAwO1xuICAgIH1cbn1cbiJdfQ==
