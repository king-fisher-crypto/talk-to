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
function animDistributeCardsLine(
    game: TarotGame,
    distributeOrderFn: any,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {},
    extraTransformations: any = {},
    yComputeFn: any = null,
    orderFirst: boolean = false,
    preserveZindex: boolean = true,
    padding: number = 0,
    paddingMobile: number = 0,
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const stepContainer = container.find('.tarot-game-step');
            const stepContContainer = stepContainer.find('.tarot-game-step-cont');

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
            const isMobile = TemplateHelper.isMobile();
            const scW = stepContContainer.width();
            const scH = stepContContainer.height();
            const cS = TemplateHelper.getCardItemSize();
            const ciW = cS.width;
            const ciH = cS.height;
            const ciCenterX = ((scW - ciW) / 2);
            const ciCenterY = ((scH - ciH) / 2);

            //
            cardItemElements.not('.front').css({
                'z-index': 101,
            });

            //
            if (TemplateHelper.isMobile()) {
                padding = paddingMobile;
            }
            let delta = (scW - ciW - padding) / (cardItemElements.length - 1);
            let baseDelay = delay / cardItemElements.length;
            let remains = cardItemElements.length;

            let order: any[] = [];

            if (orderFirst) {
                cardItemElements.each((i: any, el: any) => {
                    el = $(el);
                    const o = distributeOrderFn(baseDelay, +i, cardItemElements.length);
                    order.push([i, o]);
                });
                order.sort(function(a, b){return a[1] - b[1]});
            }

            for (let i = 0; i < cardItemElements.length; ++i) {
                const o = orderFirst ? order[i] : [i, distributeOrderFn(baseDelay, +i, cardItemElements.length)];

                let el = $(cardItemElements.get(o[0]));

                // do not distribute front cards
                if (el.hasClass('front')) {
                    remains--;
                    if (!remains) {
                        TemplateHelper.cancelAnimationOnElement(container);
                    }
                    continue;
                }

                //
                el.stop(true, true);
                el.delay(o[1]).promise().then(() => {
                    el.removeClass('big');
                    el.removeClass('small');
                    if (extraTransformations && (typeof(extraTransformations) === 'function')) {
                        el.css(extraTransformations(i, cardItemElements.length));
                    } else {
                        el.css(extraTransformations);
                    }
                    const y = isMobile ? 10 : 55;
                    const yNoComputeFn = isMobile ? y : y - 65;
                    el.animate({
                        left: (padding / 2 + i * delta) + 'px',
                        top: (yComputeFn ? yComputeFn(y, i, cardItemElements.length) : yNoComputeFn) + 'px',
                    }, {
                        ...options,
                        duration: delay,
                        easing: easing,
                        always: function() {
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
                        }
                    });
                });
            };
        });
    });
}

