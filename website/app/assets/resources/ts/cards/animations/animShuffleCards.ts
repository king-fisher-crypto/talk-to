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
function animShuffleCards(
    game: TarotGame,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const stepContainer = container.find('.tarot-game-step');
            const stepContContainer     = stepContainer.find('.tarot-game-step-cont');

            const cardItemElements = container.find('.tarot-card-item');

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                cardItemElements.each((i: any, el: any) => {
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
            let remains = 0;
            let nextAnimation = 0;

            //
            let animations: any[] = [];
            const loadNextAnimation = function() {
                remains--;
                if (remains <= 0) {
                    remains = cardItemElements.length;
                    if (nextAnimation === animations.length) {
                        TemplateHelper.cancelAnimationOnElement(container);
                    } else {
                        nextAnimation++;
                        animations[nextAnimation - 1]();
                    }
                }
            };

            //
            const delayAnim = function(delay: number) {
                return function() {
                    remains = 0;
                    window.setTimeout(loadNextAnimation, delay);
                };
            };

            //
            const gatherCenterSplittedAnim = function() {
                //
                const padding = 30;
                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cS = TemplateHelper.getCardItemSize('big');
                const ciCenterX = ((scW - cS.width) / 2);
                const ciCenterX1 = Math.max(ciCenterX - cS.width + 20, (scW / 2 - cS.width + padding) / 2);
                const ciCenterX2 = Math.min(ciCenterX + cS.width - 20, (3 * scW / 2 - cS.width - padding) / 2);
                const ciCenterY = ((scH - cS.height) / 2) - (TemplateHelper.isMobile() ? 50 : 45);

                const halfCount = Math.ceil(cardItemElements.length / 2);
                let baseDelay = delay / cardItemElements.length;
                let delta = 3.5;
                let deltaDegY = 1;
                cardItemElements.each((i: any, el: any) => {
                    el = $(el);
                    el.stop(true, true);
                    TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                    const dir = (i < halfCount ? -1 : 1);
                    const rotY = (20 + deltaDegY * i / 2);
                    let ci = i;
                    if (i < halfCount) {
                        el.css({
                            transform: 'rotateZ(20deg) rotateY(-' + rotY + 'deg) rotateX(-5deg)'
                        });
                    } else {
                        ci-= halfCount;
                        el.css({
                            transform: 'rotateZ(-20deg) rotateY(' + rotY + 'deg) rotateX(-5deg)'
                        });
                    }
                    el.delay(baseDelay * ci).promise().then(() => {
                        el.addClass('big');
                        el.removeClass('small');
                        el.animate({
                            left: ((i < halfCount ? ciCenterX1 : ciCenterX2) + dir * ci * delta) + 'px',
                            top: ciCenterY + 'px',
                        }, {
                            ...options,
                            duration: delay,
                            easing: easing,
                            always: function() {
                                el.css({ 'z-index': ci + 101 });
                                loadNextAnimation();
                            }
                        });
                    });
                });
            };

            //
            const gatherCenterAnim = function() {
                //
                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cS = TemplateHelper.getCardItemSize('big');
                const ciCenterX = ((scW - cS.width) / 2);
                const ciCenterY = ((scH - cS.height) / 2) - (TemplateHelper.isMobile() ? 50 : 45);

                const halfCount = Math.ceil(cardItemElements.length / 2);
                let baseDelay = delay * 3 / cardItemElements.length;
                let delta = 0;
                cardItemElements.each((i: any, el: any) => {
                    el = $(el);
                    el.stop(true, true);
                    TemplateHelper.updateTransitionDelay(el, ['transform'], delay);
                    el.delay(baseDelay * (i < halfCount ? i : i - halfCount)).promise().then(() => {
                        el.addClass('big');
                        el.removeClass('small');
                        el.css({
                            transform: '',
                        });
                        el.animate({
                            left: (ciCenterX + (i * delta)) + 'px',
                            top: ciCenterY + 'px',
                        }, {
                            ...options,
                            duration: delay,
                            easing: easing,
                            always: function() {
                                el.css({ 'z-index': 101 });
                                loadNextAnimation();
                            }
                        });
                    });
                });
            };

            //
            for (let i = 0; i < 2; ++i) {
                animations.push(gatherCenterSplittedAnim);
                animations.push(delayAnim(delay / 4));
                animations.push(gatherCenterAnim);
                animations.push(delayAnim(200));
            }
            loadNextAnimation();
        });
    });
}

