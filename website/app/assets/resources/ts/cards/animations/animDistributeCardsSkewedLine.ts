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
function animDistributeCardsSkewedLine(
    game: TarotGame,
    distributeOrderFn: any,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, {
        transform: 'rotateZ(20deg)'
    }, null, false, true, 50, 50);
}

