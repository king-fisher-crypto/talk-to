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
function animInitialShowCards(
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
            const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
            const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
            const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
            const isMobile = TemplateHelper.isMobile();

            const cardItemElements = container.find('.tarot-card-item');

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                cardItemElements.each((i: any, el: any) => {
                    el = $(el);
                    el.stop(true, true);
                });
                resolve();
            });

            // center
            const scW = stepContContainer.width();
            const scH = stepContContainer.height();
            const ciW = cardItemElements.first().outerWidth();
            const ciH = cardItemElements.first().outerHeight();
            const ciCenterX = ((scW - ciW) / 2);
            const ciCenterY = ((scH - ciH) / 2) - (isMobile ? 50 : 45);

            //
            cardItemElements.css({
                left: ciCenterX + 'px',
                top: ciCenterY + 'px',
                'z-index': 1,
            });

            // animate to final positions
            const delta = 5;
            let k = -Math.round(cardItemElements.length / 2);
            let remains = cardItemElements.length;
            cardItemElements.each((i: any, el: any) => {
                el = $(el);
                el.stop(true, true);
                el.css({
                    opacity: 1
                });
                el.animate({
                    left: (ciCenterX + k * delta) + 'px',
                    top: (ciCenterY + k * delta) + 'px',
                }, {
                    ...options,
                    duration: delay,
                    easing: easing,
                    queue: false,
                    always: function() {
                        remains--;
                        if (!remains) {
                            TemplateHelper.cancelAnimationOnElement(container);
                        }
                    }
                });
                k++;
            });
        });
    });
}

