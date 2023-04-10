/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Does main initializations to the initial step.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function initialStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const config = game.getConfig();
        const container = game.getContainer();
        const card = config.card.Card;
        const cardLang = config.card.CardLang;

        const stepContainer = container.find('.tarot-game-step');
        const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
        const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');

        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-choose');

        // load main css
        if (card.main_css) {
            container.prepend('<style>' + card.main_css + '</style>');
        }

        // load background
        const loadBackgroundFn = (force = false) => {
            const isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            } else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS || force) {
                const step = 'choose';
                container.css({
                    'background-color': card['step_' + step + '_bg_color'] || 'transparent',
                    'background-image': TemplateHelper.cssUrl(card[isMobile ? 'step_' + step + '_mobile_bg_image' : 'step_' + step + '_bg_image'], config.cardImagesUrl) || 'unset',
                });
            }
        };
        loadBackgroundFn(true);

        // ensure the background image is updated when going to mobile
        TemplateHelper.registerWindowResizeEvent(loadBackgroundFn, false, 0);

        // ensure we have the correct title and description there
        animSetText(stepTitleContainer, cardLang.step_choose_title);
        animSetText(stepDescContainer, cardLang.step_choose_description);

        // clear the cards container and init the cards
        stepContContainer.html('');

        for (let i = 0; i < config.cardItems.length; ++i) {
            stepContContainer.append(TemplateHelper.buildCardItemHtml(config, i));
        }

        //
        animInitialShowCards(game).then(resolve);
    });
}
