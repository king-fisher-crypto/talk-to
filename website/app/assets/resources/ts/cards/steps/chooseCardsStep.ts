/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Lets the user choose the cards.
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function chooseCardsStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const config = game.getConfig();
        const card = config.card.Card;
        const cardLang = config.card.CardLang;
        const cardItems = config.cardItems;
        const piCount = +card.count_to_pick;

        const container = game.getContainer();
        const stepContainer = container.find('.tarot-game-step');
        const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
        const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');
        const cardItemElements = container.find('.tarot-card-item');

        //
        const choose_lines = cardLang.step_choose_lines.split(/\n+/);
        for (let i = 0; i < choose_lines.length; ++i) {
            choose_lines[i] = choose_lines[i].trim();
        }
        if (!choose_lines.length) {
            choose_lines.push(cardLang.step_choose_description);
        }
        stepDescContainer.css('min-height', stepDescContainer.height() + 'px');
        animSetText(stepDescContainer, choose_lines[0]);

        //
        const initPlaceholders = function() {
            for (let i = 0; i < piCount; ++i) {
                const el = $('<div class="tarot-card-placeholder"></div>');
                let img = $('<img />');
                img.attr('src', TemplateHelper.prefixUrl(card.item_disabled_bg_image, config.cardImagesUrl));
                img.attr('alt', 'Card placeholder');
                el.append(img);
                stepContContainer.append(el);
            }
        };
        initPlaceholders();

        //
        const placeholders = stepContContainer.find('.tarot-card-placeholder');
        const repositionPlaceholdersAndSelected = function() {
            const isMobile = TemplateHelper.isMobile();
            const cbS = TemplateHelper.getCardItemSize('');
            const scW = stepContContainer.width();
            const scH = stepContContainer.height();
            const pS = TemplateHelper.getElementSize('.tarot-game .tarot-card-placeholder');
            const piW = pS.width;
            const piH = pS.height;
            const piWAll = piW * piCount;
            const piPadX = Math.min(20, piCount === 1 ? 0 : (scW - piWAll) / (piCount - 1));
            const piX = ((scW - piWAll - piPadX * (piCount - 1)) / 2);
            const piDelta = piW + piPadX;
            const padY = 20;
            const initialY = parseInt(cardItemElements.not('.tarot-card-placeholder').not('.big').first().css('top') || '60')  + cbS.height + padY;
            const piY = isMobile ?
                initialY :
                (scH - padY + initialY - piH) / 2
            ;
            placeholders.each(function(i: any, el: any) {
                el = $(el);
                let off = {
                    left: piX + i * piDelta + 'px',
                    top: piY + 'px'
                };
                el.css(off);
            });
            let selected = container.find('.tarot-card-item.selected');
            selected.each(function(o: any, el: any) {
                el = $(el);
                const card_item_id: any = TemplateHelper.getCardItemIndexFromElement(el);
                if (card_item_id === null) {
                    return;
                }
                let i = game.getSelectedCardItemIds().indexOf(+card_item_id);
                if (i === -1) {
                    return;
                }
                let off = {
                    left: piX + i * piDelta + 'px',
                    top: piY + 'px'
                };
                el.css(off);
            });


        };
        window.setTimeout(repositionPlaceholdersAndSelected);

        // ensure the placeholders and cards are replaced if the window is resized
        TemplateHelper.registerWindowResizeEvent(() => {
            if (game.getStep() <= TarotGameStep.POST_CHOOSE_CARDS) {
                TemplateHelper.waitFinishAnimationOnElement(game.getContainer()).then(function() {
                    repositionPlaceholdersAndSelected();
                });
            }
        }, false, 0);

        // Handle card clicks
        const isSafari = TemplateHelper.isSafari();
        if (isSafari) {
            TemplateHelper.registerSelectableAnimations(cardItemElements);
        } else {
            stepContContainer.addClass('selectable-items');
        }
        let disableClick = false;
        cardItemElements.off(TemplateHelper.EVENT_GROUP).on('click' + TemplateHelper.EVENT_GROUP, function(this: any) {
            let element = $(this);
            let tmp = TemplateHelper.getCardItemIndexFromElement(element);
            if (tmp === null || element.hasClass('front')) {
                return;
            }
            let cardItemIndex: number = +tmp;
            let placeholder = $(placeholders.get(game.getSelectedCardItemIds().length));

            if (disableClick) {
                return;
            }
            disableClick = true;
            if (isSafari) {
                TemplateHelper.unregisterSelectableAnimations(cardItemElements);
            } else {
                stepContContainer.removeClass('selectable-items');
            }

            animSelectCard(game, element, placeholder).then(function() {
                element.addClass('selected');
                game.addSelectedCardItemId(cardItemIndex);

                if (game.getSelectedCardItemIds().length === piCount) {
                    cardItemElements.off(TemplateHelper.EVENT_GROUP);
                    resolve();
                } else {
                    disableClick = false;
                    if (isSafari) {
                        TemplateHelper.registerSelectableAnimations(cardItemElements);
                    } else {
                        stepContContainer.addClass('selectable-items');
                    }
                    if (game.getSelectedCardItemIds().length < choose_lines.length) {
                        animSetText(stepDescContainer, choose_lines[game.getSelectedCardItemIds().length]);
                    }
                }
            });
        });
    });
}
