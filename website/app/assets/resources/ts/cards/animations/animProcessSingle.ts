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
function animProcessSingle(
    game: TarotGame,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT * 2,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const config = game.getConfig();
            const card = config.card.Card;
            const cardLang = config.card.CardLang;
            const cardItems = config.cardItems;
            const stepContainer = container.find('.tarot-game-step');
            const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
            const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
            const selectedCards = game.getSelectedCardItemIds();
            const cardItemElements: any[] = [];
            const FINAL_CARD_SIZE = 'wheelsize';

            // clear the cards container and place elements
            stepContContainer.html('');

            const wheel = $('<div class="tarot-game-interpretation-round1"></div>');
            stepContContainer.append(wheel);

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });

            // add cards with 0 opacity
            for (let i = 0; i < selectedCards.length; ++i) {
                const el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
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
            const computeCardPosition = function (index: number) {
                const cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                const centerX = wheel.width() / 2;
                const centerY = wheel.height() / 2;
                const radius = wheel.width() / 2 - (TemplateHelper.isMobile() ? 10 : 50);
                const count = cardItemElements.length;

                const theta = -Math.PI * 2 * index / count;
                const x0 = Math.sin(theta) * radius - cs.width / 2;
                const y0 = -Math.cos(theta) * radius -cs.height / 2;

                let actualRotation = theta * 180 / Math.PI;
                if (actualRotation) {
                    while (actualRotation > -150) {
                        actualRotation-= 360;
                    }
                }

                return {
                    transform: 'rotateZ(' + actualRotation + 'deg)',
                    left: (centerX + x0) + 'px',
                    top: (centerY + y0) + 'px',
                };
            };

            // reposition cards
            const repositionCards = function(force = false) {
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                const scW = wheel.width();
                const scH = wheel.height();
                const cbS = TemplateHelper.getCardItemSize('big');
                const ciCenterX = (scW - cbS.width) / 2;
                const ciCenterY = (scH - cbS.height) / 2;
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = cardItemElements[i];
                    if (el.hasClass('big')) {
                        el.css({
                            left: ciCenterX + 'px',
                            top: ciCenterY + 'px',
                        });
                    } else {
                        el.css(computeCardPosition(i));
                    }
                }
            };
            repositionCards(true);
            TemplateHelper.registerWindowResizeEvent(repositionCards, false, 0);

            // animations
            let nextCardIndex = 0;
            let nextAnimation = 0;
            let animations: any[] = [];
            const loadNextAnimation = function() {
                if (nextAnimation === animations.length) {
                    TemplateHelper.cancelAnimationOnElement(container);
                } else {
                    nextAnimation++;
                    animations[nextAnimation - 1]();
                }
            };

            //
            const delayAnim = function(delay: number) {
                return function() {
                    window.setTimeout(loadNextAnimation, delay);
                };
            };

            //
            const showCardAnim = function() {
                const cardItemLang = cardItems[selectedCards[nextCardIndex]].CardItemLang;
                const keyword = game.getResult().selected_keywords[nextCardIndex] || cardItemLang.title;
                animSetText(stepDescContainer, cardLang.step_interpretation_description.replace('##title##', cardItemLang.title).replace('##keyword##', keyword));

                const el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['opacity'], delay);
                el.css({
                    opacity: 1
                });
                window.setTimeout(loadNextAnimation, delay);
            };

            //
            const positionCardAnim = function() {
                const el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                let css = computeCardPosition(nextCardIndex);
                let anim = { left: css.left, top: css.top };
                delete css.left;
                delete css.top;
                el.css(css);
                el.delay(delay * 0.2).promise().then(() => {
                    el.removeClass('big');
                    el.addClass(FINAL_CARD_SIZE);
                    el.animate(anim, {
                        ...options,
                        duration: delay,
                        easing: easing,
                        always: function() {
                            el.css({ 'z-index': 1 });
                            loadNextAnimation();
                        }
                    });
                });
            };

            //
            const rotateWheelAnim = function() {
                animSetText(stepDescContainer, game.getResult().tr.wait_please);

                const el = wheel;
                const delay = 6000;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                el.css({
                    transform: 'rotateZ(-360deg)'
                });
                window.setTimeout(loadNextAnimation, delay);
            };

            //
            const nextCardFn = function() {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };

            //
            for (let i = 0; i < cardItemElements.length; ++i) {
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

