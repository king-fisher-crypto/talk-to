/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Show the cards and get ready to shuffle them.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function showCardsStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const config = game.getConfig();
        const container = game.getContainer();
        const cardLang = config.card.CardLang;

        const stepContainer = container.find('.tarot-game-step');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');

        //
        const distributionFn = game.getCardDistributionAnimationFn();

        //
        const shuffleBtnContainer = $('<div class="tarot-shuffle-btn-cont"></div>');
        const shuffleBtn = $('<div class="tarot-btn"></div>');
        shuffleBtn.text(config.tr.shuffle_btn_text);
        shuffleBtn.css('opacity', 0);
        shuffleBtnContainer.append(shuffleBtn);
        stepContContainer.find('.tarot-shuff-btn-cont').remove();
        stepContContainer.append(shuffleBtnContainer);

        // ensure the cards are replaced if the window is resized
        const onResizeFn =  () => {
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS) {
                TemplateHelper.waitFinishAnimationOnElement(game.getContainer()).then(() => {
                    distributionFn.animationFn(game, distributionFn.orderFn, 0);
                });
            }
        };
        TemplateHelper.registerWindowResizeEvent(onResizeFn, false, 0);

        //
        distributionFn.animationFn(game, distributionFn.orderFn).then(function() {
            window.setTimeout(() => {
                shuffleBtn.css('opacity', 1);
                resolve();
            });
        });
    });
}
