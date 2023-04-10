/// <reference path="../Globals" />

/**
 * @brief A helper with miscellaneous methods used in templates.
 */
class TemplateHelper {
    /// Default delay for debouncing
    public static readonly ANIMATION_DELAY_DEBOUNCE = 500;

    /// Default delay for animations
    public static readonly ANIMATION_DELAY_DEFAULT = 500;

    /// Default delay for text animations
    public static readonly ANIMATION_DELAY_TEXT = TemplateHelper.ANIMATION_DELAY_DEFAULT;

    /// Default easing for animations
    public static readonly ANIMATION_EASING_DEFAULT = 'swing';

    /// Default easing for text animations
    public static readonly ANIMATION_EASING_TEXT = TemplateHelper.ANIMATION_EASING_DEFAULT;

    ///
    public static readonly EVENT_GROUP = '.tarot';

    /// callbacks to cancel animations
    private static animationCancelCallbacks: any = {
        length: 0
    };

    /// used for caching information within this class
    private static cache: any = {};

    /**
     * @brief Builds a card item html element and returns it.
     * @param any config Current game configuration.
     * @param number itemIndex The config of the card item that is being generated.
     * @return JQuery The built card item html element.
     */
    public static buildCardItemHtml(config: any, itemIndex: number) {
        const card = config.card.Card;
        const cardItemConfig = config.cardItems[itemIndex];
        const cardItem = cardItemConfig.CardItem;
        const cardItemLang = cardItemConfig.CardItemLang;
        const el = $('<div class="tarot-card-item big" role="button" tabindex="0"></div>');

        // set css if given
        el.attr('style', card.item_css || '');

        //
        el.css({
            opacity: 0,
            'background-color': card.item_bg_color || 'transparent'
        });

        // inner
        const elCont = $('<div class="tarot-card-item-inner" role="button" tabindex="0"></div>');
        el.append(elCont);

        // back
        let imgBack = $('<img />');
        imgBack.addClass('tarot-card-item-img-back');
        imgBack.attr('src', TemplateHelper.prefixUrl(card.item_bg_image, config.cardImagesUrl));
        imgBack.attr('alt', 'Card Image');
        elCont.append(imgBack);

        // front
        let imgFront = $('<img />');
        imgFront.addClass('tarot-card-item-img-front');
        imgFront.attr('src', TemplateHelper.prefixUrl(cardItem.image, config.cardItemImagesUrl));
        imgFront.attr('alt', cardItemLang.title);
        elCont.append(imgFront);

        //
        TemplateHelper.setCardItemForElement(el, itemIndex);

        return el;
    }

    /**
     * @brief Cancels an animation by calling its callback (nothing is done if the animation was not found).
     * @param number id An id designating the animation to cancel.
     * @return Promise The return value from the animation callback (should be a promise) or a promise that resolves immediately if the animation was not found.
     */
    public static cancelAnimation(id: number) {
        let r: any = null;
        if (id in TemplateHelper.animationCancelCallbacks) {
            const callback = TemplateHelper.animationCancelCallbacks[id];
            delete TemplateHelper.animationCancelCallbacks[id];
            r = callback();
        }
        if (r) {
            return r;
        } else {
            return new Promise<void>((resolve, reject) => {
                resolve();
            });
        }
    }

    /**
     * @brief Cancels the registered animation on the given \a element (if any) by calling the appropriate callback.
     * @param JQuery element The element to cancel the animation for.
     * @return Promise The return value from the animation callback (should be a promise) or a promise that resolves immediately if the animation was not found.
     */
    public static cancelAnimationOnElement(element: any) {
        const animationId = element.data('_animationId');
        element.removeData('_animationId');
        return TemplateHelper.cancelAnimation(+animationId);
    }

    /**
     * @brief Builds and returns a url css property value.
     * @param string url    The url to prefix and to convert to css url.
     * @param string pref   The prefix to prepend to the url.
     * @return string The url css property value corresponding to given properties.
     */
    public static cssUrl(url: string, pref: string = '') {
        url = this.prefixUrl(url, pref);
        if (!url) {
            return '';
        }
        return 'url(\'' + url + '\')';
    }

    /**
     * @brief Returns a card item that corresponds to the given element from the given array.
     * @param JQuery element The element to get the card item for.
     * @param any[] cardItems An array containing card items.
     * @return any|null The card item that corresponds to the given element or \c null if not found.
     */
    public static getCardItemFromElement(element: any, cardItems: any[]) {
        let i = TemplateHelper.getCardItemIndexFromElement(element);
        if (i === null) {
            return null;
        }
        if (typeof cardItems[i] === 'undefined') {
            return null;
        }
        return cardItems[i];
    }

    /**
     * @brief Returns a card item index that corresponds to the given element from the given array.
     * @param JQuery element The element to get the card item for.
     * @return number|null The card item index that corresponds to the given element or \c null if not found.
     */
    public static getCardItemIndexFromElement(element: any) {
        let i: any = element.data('_card_item_index');
        if (typeof i === 'undefined') {
            return null;
        }
        return +i;
    }

    /**
     * @brief Gets card item size.
     * @param string className The class name to read css for.
     * @return any An object with `width` and `height` keys corresponding to the card size.
     */
    public static getCardItemSize(className: string = '') {
        if (className && className.charAt(0) != '.') {
            className = '.' + className;
        }
        const p = TemplateHelper.isMobile() ? '.tarot-card-mobile ' : '';
        const style = {
            ...TemplateHelper.getStyle(p + '.tarot-game .tarot-card-item'),
            ...TemplateHelper.getStyle(p + '.tarot-game .tarot-card-item' + className)
        };
        return {
            width: parseFloat(style.width),
            height: parseFloat(style.height)
        };
    }

    /**
     * @brief Gets an element size.
     * @param string className The class name to read css for.
     * @param boolean mobileCheck Should we check for mobile.
     * @return any An object with `width` and `height` keys corresponding to the card size.
     */
    public static getElementSize(className: string, mobileCheck: boolean = true) {
        const p = mobileCheck && TemplateHelper.isMobile() ? '.tarot-card-mobile ' : '';
        const style = {
            ...TemplateHelper.getStyle(p + className)
        };
        return {
            width: parseFloat(style.width),
            height: parseFloat(style.height)
        };
    }

    /**
     * @brief Reads css style for given class.
     * @param string className The class name to read css for.
     * @return any The read style.
     */
    public static getStyle(className: string) {
        if (typeof TemplateHelper.cache.style === 'undefined') {
            TemplateHelper.cache.style = {};
        }
        if (typeof TemplateHelper.cache.style[className] !== 'undefined') {
            return TemplateHelper.cache.style[className];
        }
        let styleSheets: any = window.document.styleSheets;
        let cssText: string = '';
        for (let i = 0; i < styleSheets.length; i++){
            let classes: any = styleSheets[i].rules || styleSheets[i].cssRules;
            if (!classes) {
                continue;
            }
            for (let x = 0; x < classes.length; x++) {
                if (classes[x].selectorText === className) {
                    cssText += classes[x].cssText || classes[x].style.cssText;
                }
            }
        }

        let r: any = {};
        let split = cssText.split(/[\{\};]+/);
        for (let i = 0; i < split.length; ++i) {
            let v = split[i].split(':', 2);
            if (v.length !== 2) {
                continue;
            }
            v[0] = v[0].trim();
            v[1] = v[1].trim();
            r[v[0]] = v[1];
        }
        TemplateHelper.cache.style[className] = r;
        return r;
    }

    /**
     * @brief Returns \c true if the user is viewing the page in mobile, \c false otherwise.
     * @return boolean \c true if the user is viewing the page in mobile, \c false otherwise.
     */
    public static isMobile() {
        return $('body').width() < 980;
    }

    /**
     * @brief Returns \c true if the user is viewing the page in safari, \c false otherwise.
     * @return boolean \c true if the user is viewing the page in safari, \c false otherwise.
     */
    public static isSafari() {
        let ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('safari') != -1) {
            if (ua.indexOf('chrome') === -1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @brief Returns a prefixed url based on the given parameters.
     * @param string url The url to prefix.
     * @param string pref The prefix to prepend to the url.
     * @return string The prefixed url.
     */
    public static prefixUrl(url: string, pref: string = '') {
        if (!url) {
            return '';
        }
        return pref + url;
    }

    /**
     * @brief Preload one single image.
     * @param string url The url of the image to preload.
     * @param callable|null loadCallback    The function to call when the image is done preloading.
     * @param callable|null errorCallback   The function to call when there was an error preloading the image.
     */
    public static preloadImage(url: string, loadCallBack: any = null, errorCallBack: any = null) {
        let img = new Image();
        if (loadCallBack) {
            img.onload = loadCallBack;
        }
        if (errorCallBack) {
            img.onerror = errorCallBack;
        }
        img.src = url;
    }

    /**
     * @brief Registers the animation cancelation callback and returns the registration id.
     * @param callable callback The animation cancelation callback.
     * @return number The animation registration id.
     */
    public static registerAnimation(callback: any): number {
        TemplateHelper.animationCancelCallbacks.length += 1;
        const id = TemplateHelper.animationCancelCallbacks.length;
        TemplateHelper.animationCancelCallbacks[id] = callback;
        return id;
    }

    /**
     * @brief Registers the animation cancelation callback on the given element and returns the registration id.
     * @param JQuery element The element to register the animation for.
     * @param callable callback The animation cancelation callback.
     * @return number The animation registration id.
     */
    public static registerAnimationOnElement(element: any, callback: any) {
        const id = TemplateHelper.registerAnimation(callback);
        element.data('_animationId', id);
        return id;
    }

    /**
     * @brief Registers the selectable animation on hover for given card elements.
     * @param JQuery elements The elements to register the animation for.
     */
    public static registerSelectableAnimations(elements: any) {
        elements.each(function(this: any) {
            const e = $(this);
            let rz_orig = e.get(0).style.transform;
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
            const stepFn = function(this: any, now: any, fx: any) {
                let sc = 1 + 0.2 * now / 100;
                let ty = -8 * now / 100;
                let rz = rz_orig * (100 - now) / 100;
                $(this).css('transform', 'scale(' + sc + ') translateY(' + ty + '%) rotateZ(' + rz + 'deg)');
            };
            e.on('mouseenter.tarot-cards-selectable', function() {
                e.stop().animate({
                    borderSpacing: 100
                }, {
                    step: stepFn,
                    duration: 500,
                });
            });
            e.on('mouseleave.tarot-cards-selectable', function() {
                e.stop().animate({
                    borderSpacing: 0
                }, {
                    step: stepFn,
                    duration: 500,
                });
            });
        });
    }

    /**
     * @brief Registers the callback to be called when the window is resized.
     * @param callable callback The method to call when the window is resized.
     * @param boolean once      Set to true so that the event is only called once.
     * @param number debounce   Minimum delay for the event to be called.
     */
    public static registerWindowResizeEvent(callback: any, once = false, debounce = TemplateHelper.ANIMATION_DELAY_DEBOUNCE) {
        let scheduledEvent: any = null;
        const fn = () => {
            if (scheduledEvent) {
                return;
            }
            scheduledEvent = window.setTimeout(() => {
                scheduledEvent = null;
                callback();
            }, debounce);
        };
        if (once) {
            $(window).one('resize orientationchange', fn);
        } else {
            $(window).on('resize orientationchange', fn);
        }
    }

    /**
     * @brief Saves the given \a cardItemIndex in the given \a element to be retrieved with getCardItemFromElement().
     * @param JQuery element The element to get the card item for.
     * @param number cardItemIndex The index to save.
     */
    public static setCardItemForElement(element: any, cardItemIndex: number) {
        element.data('_card_item_index', cardItemIndex);
    }

    /**
     * @brief Unregisters the animation callback (nothing is done if the animation was not found).
     * @param number id An id designating the animation to unregister.
     */
    public static unregisterAnimation(id: number) {
        if (id in TemplateHelper.animationCancelCallbacks) {
            delete TemplateHelper.animationCancelCallbacks[id];
        }
    }

    /**
     * @brief Unregisters the registered animation on the given \a element (if any).
     * @param JQuery element The element to cancel the animation for.
     */
    public static unregisterAnimationOnElement(element: any) {
        const animationId = element.data('_animationId');
        element.removeData('_animationId');
        if (animationId) {
            TemplateHelper.unregisterAnimation(+animationId);
        }
    }

    /**
     * @brief Unregisters the selectable animation on hover for given card elements.
     * @param JQuery elements The elements to unregister the animation for.
     */
    public static unregisterSelectableAnimations(elements: any) {
        elements.each(function(this: any) {
            const e = $(this);
            e.css('transition', '');
            e.off('.tarot-cards-selectable');
        });
    }

    /**
     * @brief Updates an element transition css property to have the given delay.
     * @param JQuery element The element to process.
     * @param string[] properties List of property names to match.
     * @param number|null newDelayMs The new delay in milliseconds.
     * @param string|null newFunction The new transition function.
     * @param number|null index The index of the transition to change (if there are many).
     */
    public static updateTransitionDelay(element: any, properties: string[]|true, newDelayMs: number|null = null, newFunction: string|null = null, index: number|null = null) {
        const ns = (newDelayMs === null ? 0 : newDelayMs / 1000.0) + 's';
        const oldTransitionProperties = element.css('transition-property');
        const oldTransitionDurations = element.css('transition-duration');
        const oldTransitionFunctions = element.css('transition-timing-function');
        const split = oldTransitionProperties.split(/\s*,\s*/);
        const split2 = oldTransitionDurations.split(/\s*,\s*/);
        const split3 = oldTransitionFunctions.split(/\s*,\s*/);
        if (newDelayMs !== null && split.length !== split2.length) {
            return;
        }
        if (newFunction !== null && split.length !== split3.length) {
            return;
        }

        if (index !== null && index < 0) {
            let maxIndex = 0;
            for (let i = 0; i < split.length; ++i) {
                let v = split[i];
                if (properties === true) {
                    maxIndex++;
                } else {
                    for (let j = 0; j < (<any[]>properties).length; j++) {
                        if (v.toLowerCase().indexOf((<any[]>properties)[j].toLowerCase()) !== -1) {
                            maxIndex++;
                            break;
                        }
                    }
                }
            }
            index = maxIndex + index;
        }

        let newTransitionDurations: string[] = [];
        let newTransitionFunctions: string[] = [];
        let change = false;
        let currentIndex = -1;
        for (let i = 0; i < split.length; ++i) {
            let v = split[i];
            let v2 = split2[i];
            let v3 = split3[i];
            let found = false;
            if (properties === true) {
                found = true;
            } else {
                for (let j = 0; j < (<any[]>properties).length; j++) {
                    if (v.toLowerCase().indexOf((<any[]>properties)[j].toLowerCase()) !== -1) {
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
    }

    /**
     * @brief Waits for the animation to finish on the given element.
     * @param JQuery element The element to wait for.
     * @param number maxWait Maximum delay to wait before failure.
     * @return Promise A promise for when the animation is done on the element.
     */
    public static waitFinishAnimationOnElement(element: any, maxWait: number = 60000) {
        return new Promise<void>((resolve, reject) => {
            const animationId = element.data('_animationId');
            if (animationId) {
                const delay = 200;
                if (maxWait <= 0) {
                    reject();
                } else {
                    window.setTimeout(function() {
                        TemplateHelper.waitFinishAnimationOnElement(element, maxWait - delay).then(resolve).catch(reject);
                    }, delay);
                }
            } else {
                resolve();
            }
        });
    }
}
