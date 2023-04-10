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
function animProcessYesNo(
    game: TarotGame,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT * 2,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const result = game.getResult();
            const config = game.getConfig();
            const card = config.card.Card;
            const cardLang = config.card.CardLang;
            const cardItems = config.cardItems;
            const stepContainer = container.find('.tarot-game-step');
            const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
            const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
            const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
            const selectedCards = game.getSelectedCardItemIds();
            const cardItemElements: any[] = [];
            const FINAL_CARD_SIZE = 'small';

            //
            let countYes = 0;
            let countNo = 0;
            for (let i = 0; i < result.card_item_answer.length; ++i) {
                const r = result.card_item_answer[i];
                if (r) {
                    countYes++;
                } else {
                    countNo++;
                }
            }

            // clear the cards container and place elements
            stepContContainer.html('');

            //
            const yesCont = $('<div class="tarot-game-interpretation-yes-text">' + result.tr.yes + '</div>');
            stepContContainer.append(yesCont);
            const noCont = $('<div class="tarot-game-interpretation-no-text">' + result.tr.no + '</div>');
            stepContContainer.append(noCont);

            const scale = $('<div class="tarot-game-interpretation-scale"></div>');
            stepContContainer.append(scale);

            const scaleBar = $('<div class="tarot-game-interpretation-scale-bar"></div>');
            scale.append(scaleBar);

            const scaleBase = $('<div class="tarot-game-interpretation-scale-base"></div>');
            scale.append(scaleBase);

            const scaleInd = $('<div class="tarot-game-interpretation-scale-ind"></div>');
            scale.append(scaleInd);

            const scaleContL = $('<div class="tarot-game-interpretation-scale-contl"></div>');
            scale.append(scaleContL);

            const scaleContR = $('<div class="tarot-game-interpretation-scale-contr"></div>');
            scale.append(scaleContR);


            // function to rotate the scale (p in range -1 .. 1)
            const scaleContTopAtO = parseInt(scaleContL.css('top'));
            const setScaleValue = function(p: number) {
                const MAX_ROT_IND = 45;
                scaleInd.css({ transform: 'rotateZ(' + (-p * MAX_ROT_IND) + 'deg)' });

                const MAX_ROT = 30;
                scaleBar.css({ transform: 'rotateZ(' + (p * MAX_ROT) + 'deg)' });

                const radius = TemplateHelper.isMobile() ? (192 * 0.6) : 192;
                const alpha = p * MAX_ROT * Math.PI / 180;
                const y = Math.sin(alpha) * radius;
                const x = (Math.cos(alpha) - 1) * radius;
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
            const computePositionOnScale = function(scale: any, cardEl: any, cardIndex: number, cardsCount: number) {
                const cs = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                let top = scale.offset().top + scale.height() - scale.parent().parent().offset().top - cs.height;
                let left = scale.offset().left + scale.width() - scale.parent().parent().offset().left - cs.width;
                return {
                    top: top + 'px',
                    left: left + 'px',
                };
            };

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });

            // add cards on a line
            for (let i = 0; i < selectedCards.length; ++i) {
                const el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
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
            const repositionCards = function(force = false) {
                if (game.getStep() !== TarotGameStep.PROCESS_SELECTION && !force) {
                    return;
                }
                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cbS = TemplateHelper.getCardItemSize(FINAL_CARD_SIZE);
                const padW = Math.min(20, scW / cardItemElements.length - cbS.width);
                const actualW = cbS.width + padW;
                const ciCenterX = (scW - actualW * cardItemElements.length + padW) / 2;
                const ciCenterY = 20;
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = cardItemElements[i];
                    let centerOnScale = null;
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
                const showDelay = delay;

                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cbS = TemplateHelper.getCardItemSize('big');
                const ciCenterX = (scW - cbS.width) / 2;
                const ciCenterY = (scH - cbS.height) / 2;

                const element = cardItemElements[nextCardIndex];
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
                }, {
                    ...options,
                    duration: showDelay,
                    easing: easing,
                    always: loadNextAnimation
                });
            };

            //
            let countPlacedYes = 0;
            let countPlacedNo = 0;
            const positionCardAnim = function() {
                const elAnswer = !!result.card_item_answer[nextCardIndex];
                const el = cardItemElements[nextCardIndex];
                const scaleCont = elAnswer ? scaleContR : scaleContL;
                el.stop(true, true);
                TemplateHelper.updateTransitionDelay(el, ['transform'], delay, 'cubic-bezier(.6,-0.61,.44,.98)', -1);
                let css = computePositionOnScale(scaleCont, el, elAnswer ? countPlacedYes : countPlacedNo, elAnswer ? countYes : countNo);
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
                            el.remove();
                            scaleCont.append(el);
                            if (elAnswer) {
                                countPlacedYes++;
                            } else {
                                countPlacedNo++;
                            }
                            loadNextAnimation();
                        }
                    });
                });
            };

            //
            const rotateScaleAnim = function() {
                TemplateHelper.updateTransitionDelay(scaleInd, ['transform'], delay);
                TemplateHelper.updateTransitionDelay(scaleBar, ['transform'], delay);
                TemplateHelper.updateTransitionDelay(scaleContL, ['transform', 'top'], delay);
                TemplateHelper.updateTransitionDelay(scaleContR, ['transform', 'top'], delay);

                setScaleValue((countPlacedYes - countPlacedNo) / (countYes + countNo));
                window.setTimeout(loadNextAnimation, delay);
            };

            //
            const nextCardFn = function() {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };

            //
            const showResult = function() {
                const showDelay = 2000;

                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cbS = TemplateHelper.getElementSize('.tarot-game-interpretation-yes-text.big');
                const ciCenterX = (scW - cbS.width) / 2;
                const ciCenterY = (scH - cbS.height) / 2;

                animSetText(stepTitleContainer, result.tr.answer_is);

                const element = countYes > countNo ? yesCont : noCont;
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
            for (let i = 0; i < cardItemElements.length; ++i) {
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

