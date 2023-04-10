/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Distribute the cards on a arc
 * @param TarotGame game The game object.
 * @param any distributeOrderFn The distribution order function to use.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animDistributeCardsArc(
    game: TarotGame,
    distributeOrderFn: any,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    const BASE_DEG = 50;
    const BASE_Y0 = -20;
    const BASE_Y = 300 * Math.cos(BASE_DEG);
    return animDistributeCardsLine(game, distributeOrderFn, delay, easing, options, function(i: number, count: number) {
        const d = ((i - count / 2) * BASE_DEG / count);
        return {
            transform: 'rotateZ(' + d  + 'deg)'
        };
    }, function(y: number, i: number, count: number) {
        const d = ((i - count / 2) * BASE_DEG / count) * Math.PI / 180;
        return y + BASE_Y0 + Math.sin(Math.abs(d*d)) * BASE_Y - (TemplateHelper.isMobile() ? 20 : 0);
    }, false, true, 50, 50);
}

