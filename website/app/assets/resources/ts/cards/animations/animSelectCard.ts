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
function animSelectCard(
    game: TarotGame,
    element: any,
    placeholder: any,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const stepContainer = container.find('.tarot-game-step');
            const stepContContainer = stepContainer.find('.tarot-game-step-cont');

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                element.stop(true, true);
                resolve();
            });

            //
            element.css({
                'z-index': 101,
            });

            //
            let animations: any[] = [];
            let nextAnimation = 0;
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
            const displaySelectedCardAnim = function() {
                //
                const scW = stepContContainer.width();
                const scH = stepContContainer.height();
                const cS = TemplateHelper.getCardItemSize('big');
                const ciW = cS.width;
                const ciH = cS.height;
                const ciCenterX = ((scW - ciW) / 2);
                const ciCenterY = ((element.offset().top - ciH) / 2);

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
                }, {
                    ...options,
                    duration: delay,
                    easing: easing,
                    always: loadNextAnimation
                });
            };

            //
            const flipSelectedCardAnim = function() {
                element.stop(true, true);
                element.addClass('front');
                element.addClass('highlight');
                loadNextAnimation();
            };

            //
            const sendToPlaceholderCardAnim = function() {
                let pOff = placeholder.offset();
                element.stop(true, true);
                element.removeClass('big');
                element.addClass('small');
                element.animate({
                    left: placeholder.css('left'),
                    top: placeholder.css('top'),
                }, {
                    ...options,
                    duration: delay,
                    easing: easing,
                    always: function() {

                        element.removeClass('highlight');
                        element.css({
                            'z-index': Math.min(99, 1 + (placeholder.siblings('.selected').length)),
                        });
                        placeholder.hide();
                        loadNextAnimation();
                    }
                });
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

