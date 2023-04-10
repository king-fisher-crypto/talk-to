/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Shuffle the cards after clicking on the shuffle button.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function shuffleCardsStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const container = game.getContainer();
        const stepContainer = container.find('.tarot-game-step');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
        const cardItemElements = container.find('.tarot-card-item');
        const shuffleBtnCont = container.find('.tarot-shuffle-btn-cont');
        const shuffleBtn = shuffleBtnCont.find('.tarot-btn');

        // ensure the shuffle button is rightly placed
        const onResizeFn =  () => {
            if (game.getStep() === TarotGameStep.READY_TO_SHUFFLE) {
                const cbS = TemplateHelper.getCardItemSize('');
                const scH = stepContContainer.height();
                const bH = shuffleBtnCont.height();
                const padY = 20;
                const initialY = parseInt(cardItemElements.not('.tarot-card-placeholder').not('.big').first().css('top') || '60')  + cbS.height + padY;
                const piY = Math.min(initialY + 50, (scH - padY + initialY - bH) / 2);
                shuffleBtnCont.css('top', piY + 'px');
            }
        };
        TemplateHelper.registerWindowResizeEvent(onResizeFn, false, 0);
        window.setTimeout(onResizeFn);

        //
        const realShuffleCards = function() {
            let a: any[] = [];

            // get card info
            cardItemElements.each(function(k: any, el: any) {
                el = $(el);
                let imgFront = el.find('img.tarot-card-item-img-front').first();
                a.push({
                    id: TemplateHelper.getCardItemIndexFromElement(el),
                    img: imgFront.attr('src'),
                    imgAlt: imgFront.attr('alt')
                });
            });

            // shuffle the array
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }

            // apply the array
            let i = 0;
            cardItemElements.each(function(k: any, el: any) {
                el = $(el);
                let imgFront = el.find('img.tarot-card-item-img-front').first();
                TemplateHelper.setCardItemForElement(el, a[i].id);
                imgFront.attr('src', a[i].img);
                imgFront.attr('alt', a[i].imgAlt);
                i++;
            });
        };

        //
        let shuffling = false;
        const shuffleFn = function() {
            if (shuffling) {
                return;
            }
            shuffling = true;
            shuffleBtn.css('opacity', 0);
            animShuffleCards(game).then(function() {
                shuffleBtnCont.remove();
                cardItemElements.off(TemplateHelper.EVENT_GROUP);

                // actual shuffling
                realShuffleCards();

                // redistribute
                const distributionFn = game.getCardDistributionAnimationFn();
                distributionFn.animationFn(game, distributionFn.orderFn).then(resolve);
            });
        };

        shuffleBtn.off(TemplateHelper.EVENT_GROUP).one('click' + TemplateHelper.EVENT_GROUP, shuffleFn);
        cardItemElements.off(TemplateHelper.EVENT_GROUP).one('click' + TemplateHelper.EVENT_GROUP, shuffleFn);
    });
}
