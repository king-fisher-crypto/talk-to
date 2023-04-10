/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Shows the processing step.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function processStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const config = game.getConfig();
        const container = game.getContainer();
        const card = config.card.Card;
        const cardLang = config.card.CardLang;
        const cardItems = config.cardItems;

        container.siblings('.tarot-game-main-desc').slideUp();

        const stepContainer = container.find('.tarot-game-step');
        const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
        const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');

        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-interpretation');

        // load background
        const loadBackgroundFn = (force = false) => {
            const isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            } else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() === TarotGameStep.PROCESS_SELECTION || force) {
                const step = 'interpretation';
                container.css({
                    'background-color': card['step_' + step + '_bg_color'] || 'transparent',
                    'background-image': TemplateHelper.cssUrl(card[isMobile ? 'step_' + step + '_mobile_bg_image' : 'step_' + step + '_bg_image'], config.cardImagesUrl) ||
                        TemplateHelper.cssUrl(card[isMobile ? 'step_choose_mobile_bg_image' : 'step_choose_bg_image'], config.cardImagesUrl) ||
                        'unset',
                });
            }
        };
        loadBackgroundFn(true);

        // ensure the background image is updated when going to mobile
        TemplateHelper.registerWindowResizeEvent(loadBackgroundFn, false, 0);

        // ensure we have the correct title and description there
        animSetText(stepTitleContainer, cardLang.step_interpretation_title);
        animSetText(stepDescContainer, '');

        //
        let animFn = animProcessSingle;
        let distributeOrderFn = DistributeOrder.outFirstDistribution;
        const gameType = +config.card.Card.game_type;
        if (gameType === GameType.YES_NO) {
            animFn = animProcessYesNo;
        } else if (gameType === GameType.SINGLE) {
            animFn = animProcessSingle;
        } else if (gameType === GameType.FORTUNE) {
            animFn = animProcessFortune;
        } else if (gameType === GameType.LOVE) {
            animFn = animProcessLove;
        }

        //
        animFn(game).then(resolve);
    });
}
