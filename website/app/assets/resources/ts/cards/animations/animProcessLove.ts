/// <reference path="../Globals" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Animate process for "love" game type.
 * @param TarotGame game The game object.
 * @param int delay The animation delay.
 * @param string|boolean easing Default animation easing to be passed to jquery.
 * @param any options Extra options to pass to jquery.
 * @return Promise A promise for when the animation is done.
 */
function animProcessLove(
    game: TarotGame,
    delay: number = TemplateHelper.ANIMATION_DELAY_DEFAULT,
    easing: string|boolean = TemplateHelper.ANIMATION_EASING_DEFAULT,
    options: any = {}
) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();

        // cancel animations on container first
        TemplateHelper.cancelAnimationOnElement(container).then(() => {
            const config = game.getConfig();
            const card = config.card.Card;
            const cardLang = config.card.CardLang;
            const cardItems = config.cardItems;
            const stepContainer = container.find('.tarot-game-step');
            const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
            const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
            const selectedCards = game.getSelectedCardItemIds();
            const cardItemElements: any[] = [];
            const FINAL_CARD_SIZE = '';

            // clear the cards container and place elements
            stepContContainer.html('');

            const flexcont = $('<div class="tarot-game-interpretation-flexcont"></div>');
            stepContContainer.append(flexcont);

            //
            TemplateHelper.registerAnimationOnElement(container, function() {
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = $(cardItemElements[i]);
                    el.stop(true, true);
                }
                resolve();
            });

            // add cards
            for (let i = 0; i < selectedCards.length; ++i) {
                const el = TemplateHelper.buildCardItemHtml(config, selectedCards[i]);
                el.css({
                    opacity: 1,
                    'z-index': 101,
                });
                el.removeClass('big');
                flexcont.append(el);
                cardItemElements.push(el);
            }

            // animations
            let nextCardIndex = 0;
            let nextAnimation = 0;
            let animations: any[] = [];
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
            const flipCardAnim = function() {
                const el = cardItemElements[nextCardIndex];
                el.stop(true, true);
                el.toggleClass('front');
                window.setTimeout(loadNextAnimation, 0);
            };

            //
            const flipAllCardsAnim = function() {
                for (let i = 0; i < cardItemElements.length; ++i) {
                    const el = cardItemElements[i];
                    el.stop(true, true);
                    el.toggleClass('front');
                }
                window.setTimeout(loadNextAnimation, 0);
            };

            //
            const resetNextCardFn = function() {
                nextCardIndex = 0;
                window.setTimeout(loadNextAnimation);
            };

            //
            const nextCardFn = function() {
                nextCardIndex++;
                window.setTimeout(loadNextAnimation);
            };

            //
            for (let j = 0; j < 2; ++j) {
                animations.push(resetNextCardFn);
                for (let i = 0; i < cardItemElements.length; ++i) {
                    animations.push(flipCardAnim);
                    animations.push(delayAnim(delay));
                    animations.push(flipCardAnim);
                    animations.push(nextCardFn);
                }
                animations.push(flipAllCardsAnim);
                animations.push(delayAnim(delay));
                animations.push(flipAllCardsAnim);
                animations.push(delayAnim(delay));
            }
            loadNextAnimation();
        });
    });
}

