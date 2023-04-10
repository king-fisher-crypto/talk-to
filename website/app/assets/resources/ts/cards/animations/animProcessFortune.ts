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
function animProcessFortune(
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
            const FINAL_CARD_SIZE = 'wheelsize2';

            // clear the cards container and place elements
            stepContContainer.html('');

            const wheel = $('<div class="tarot-game-interpretation-round2"></div>');
            stepContContainer.append(wheel);
            animSetText(stepDescContainer, game.getResult().tr.wait_please);

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });

            // function to compute position of a card on a circle
            const computeCardPosition = function (index: number, cs: any, radius: number, centerOnElement: any = null, dx = 0, dy = 0) {
                if (centerOnElement === null) {
                    centerOnElement = wheel;
                }
                const centerX = centerOnElement.width() / 2;
                const centerY = centerOnElement.height() / 2;
                const count = selectedCards.length;

                const theta = -Math.PI * 2 * index / count;
                const x0 = Math.sin(theta) * radius - cs.width / 2;
                const y0 = -Math.cos(theta) * radius - cs.height / 2;

                let actualRotation = theta * 180 / Math.PI;
                if (actualRotation) {
                    while (actualRotation > -150) {
                        actualRotation-= 360;
                    }
                }

                return {
                    transform: 'rotateZ(' + actualRotation + 'deg)',
                    left: (dx + centerX + x0) + 'px',
                    top: (dy + centerY + y0) + 'px',
                };
            };

            // add wheel cards
            const wheelCards: any[] = [];
            for (let i = 0; i < selectedCards.length; ++i) {
                const el = $('<div class="tarot-game-interpretation-round2-card"></div>');
                const cs = TemplateHelper.getElementSize('.tarot-game-interpretation-round2-card');
                const css = computeCardPosition(i, cs, cs.height / 2);
                el.css(css);
                wheel.append(el);
                wheelCards.push(el);
            }

            // add cards with 0 opacity
            for (let i = 0; i < selectedCards.length; ++i) {
                const el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
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
            const repositionCards = function(force = false) {
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                for (let i = 0; i < wheelCards.length; ++i) {
                    const el = wheelCards[i];
                    const cs = TemplateHelper.getElementSize('.tarot-game-interpretation-round2-card');
                    const css = computeCardPosition(i, cs, cs.height / 2);
                    el.css(css);
                }
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = cardItemElements[i];
                    const cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                    const css = computeCardPosition(i, cs, wheel.width() / 2 + (TemplateHelper.isMobile() ? 0 : 10), stepContContainer);
                    delete css['transform'];
                    el.css(css);
                    el.css(css);
                }
            };
            repositionCards(true);
            TemplateHelper.registerWindowResizeEvent(repositionCards, false, 0);

            // animations
            let nextCardIndex = 0;
            let destinationIndex = 0;
            let nextAnimation = 0;
            let doneIndexes: number[] = [];
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
            const positionCardAnim = function() {
                const el = wheelCards[wheelCards.length - 1 - nextCardIndex];
                const cel = cardItemElements[destinationIndex];
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                el.css({
                    'transform': 'rotateZ(360deg)'
                });
                const cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                const css = computeCardPosition(destinationIndex, cs, wheel.width() / 2 + (TemplateHelper.isMobile() ? 0 : 10), null, 0, 18);
                let anim = { left: css.left, top: css.top };
                delete css.left;
                delete css.top;
                el.animate(anim, {
                    ...options,
                    duration: delay,
                    easing: easing,
                    always: function() {
                        cel.css({
                            'opacity': 1
                        });
                        el.css({
                            'opacity': 0,
                        });
                        el.css({ 'z-index': 1 });
                        loadNextAnimation();
                    }
                });
            };

            //
            const rotateWheelAnim = function() {
                const el = wheel;
                const delay = 6000;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                el.css({
                    transform: 'rotateZ(-720deg)'
                });
                window.setTimeout(loadNextAnimation, delay);
            };

            //
            const nextCardFn = function() {
                doneIndexes.push(destinationIndex);
                nextCardIndex++;

                let init = destinationIndex;
                destinationIndex = init;
                let count = wheelCards.length;
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
            for (let i = 0; i < cardItemElements.length; ++i) {
                animations.push(positionCardAnim);
                animations.push(delayAnim(200));
                animations.push(nextCardFn);
            }
            animations.push(rotateWheelAnim);
            loadNextAnimation();
        });
    });
}

