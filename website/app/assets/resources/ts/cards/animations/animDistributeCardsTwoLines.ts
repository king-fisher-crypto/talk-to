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
function animDistributeCardsTwoLines(
    game: TarotGame,
    distributeOrderFn: any,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    const isMobile = TemplateHelper.isMobile();
    const BASE_Y = isMobile ? 0 : -140;
    const BASE_DELTA_Y = isMobile ? -50 : -100;
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, {}, function(y: number, i: number, count: number) {
        return y + BASE_Y - Math.abs(count / 2 - i) * BASE_DELTA_Y * 2 / count;
    });
}

