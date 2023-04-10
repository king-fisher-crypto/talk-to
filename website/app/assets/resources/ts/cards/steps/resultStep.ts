/// <reference path="../Globals" />
/// <reference path="../enums/TarotGameStep" />
/// <reference path="../helpers/TemplateHelper" />
/// <reference path="../TarotGame" />

/**
 * @brief Shows the results step
 * @param TarotGame game The game object.
 * @return Promise A promise for when the function is done.
 */
function resultStep(game: TarotGame) {
    return new Promise<void>((resolve, reject) => {
        const config = game.getConfig();
        const container = game.getContainer();
        const card = config.card.Card;
        const cardLang = config.card.CardLang;
        const cardItems = config.cardItems;

        const stepContainer = container.find('.tarot-game-step');
        const stepTitleContainer    = stepContainer.find('.tarot-game-step-title');
        const stepDescContainer     = stepContainer.find('.tarot-game-step-desc');
        const stepContContainer     = stepContainer.find('.tarot-game-step-cont');

        // ensure we have the proper class
        stepContainer.attr('class', 'tarot-game-step tarot-game-step-result');

        // load background
        const loadBackgroundFn = (force = false) => {
            const isMobile = TemplateHelper.isMobile();
            if (isMobile) {
                $('body').addClass('tarot-card-mobile');
            } else {
                $('body').removeClass('tarot-card-mobile');
            }
            if (game.getStep() === TarotGameStep.SHOW_RESULTS || force) {
                const step = 'result';
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
        animSetText(stepTitleContainer, cardLang.step_result_title);
        animSetText(stepDescContainer, cardLang.step_result_description);

        // clear the cards container and shows the results
        stepContContainer.html('');

        const result = game.getResult();
        const selectedCardItemIds = game.getSelectedCardItemIds();

        let html = $('<div class="tarot-result"></div>');
        stepContContainer.append(html);

        let mainHtml = $('<div class="tarot-result-content"></div>');
        html.append(mainHtml);
        let mainHtmlForCards = $('<div class="tarot-result-content-card"></div>');
        mainHtmlForCards.append('<h3>' + result.tr.card_title + '</h3>');
        let resultTextCount = 0;
        for (let k = 0; k < selectedCardItemIds.length; ++k) {
            const i = +selectedCardItemIds[k];
            const cardItem = cardItems[i].CardItem;
            const cardItemLang = cardItems[i].CardItemLang;

            mainHtmlForCards.append('<div class="numerated-text"><span class="text-num">' + (++resultTextCount) + '</span><p>' + cardItemLang.description + '</p></div>');
        }
        mainHtml.append(mainHtmlForCards);
        
        if (result.email_form) {
            mainHtmlForCards = $('<div class="tarot-result-content-card card-blur"></div>');
            mainHtml.append(mainHtmlForCards);
			mainHtmlForCards.append('<h3>' + result.tr.result_title + '</h3>');
            mainHtmlForCards.append('<div class="txt-blur">'+result.text+'</div>');
            mainHtml.append('<div class="tarot-result-emailform">' + result.email_form + '</div>');
			mainHtml.append('<div class="tarot_emailform_id" style="display:none">'+result.card_id+'</div>');
			//bindEmailFormCard();
			
        }else{
            mainHtmlForCards = $('<div class="tarot-result-content-card"></div>');
            mainHtml.append(mainHtmlForCards);
			mainHtmlForCards.append('<h3>' + result.tr.result_title + '</h3>');
            mainHtmlForCards.append(result.text);
        }

        if (cardLang.step_result_next) {
            mainHtmlForCards.append('<h3 class="card_next_data" style="margin-top:15px;">' + result.tr.next_title + '</h3>');
            mainHtmlForCards.append('<p class="card_next_data">' + cardLang.step_result_next + '</p>');
        }

        if (result.register_form) {
            let clickableTitle = $('<span class="tarot-result-register-btn">' + result.tr.ins_title + '</span>');
            let clickableTitleCont = $('<div class="tarot-result-content-next-btn card_next_data"></div>');
            clickableTitleCont.append(clickableTitle);
            //mainHtmlForCards.append(clickableTitleCont);

            let registerForm = $('<div class="tarot-result-content-form hidden-form"></div>');
            registerForm.append('<h3>' + result.tr.ins_title + '</h3>');
            registerForm.append('<div>' + result.register_form + '</div>');
            mainHtmlForCards.append(registerForm);

            clickableTitle.on('click', function() {
                let scrollTo = clickableTitle.offset().top - $('body > header').outerHeight() - 20;
                clickableTitleCont.animate({ height: 0, opacity: 0, margin: 0 }, function(this: any) {
                   $(this).hide();
                });

                let s1 = $('html').scrollTop();
                let s2 = $('body').scrollTop();
                registerForm.removeClass('hidden-form');
                let toFocus = $('#UserFirstname');
                if (!toFocus.length) {
                    toFocus = registerForm.find('input:first');
                }
                window.setTimeout(function() {
                    toFocus.focus();
                    $('html, body').stop();
                    $('html').scrollTop(s1);
                    $('body').scrollTop(s2);
                    $('html, body').animate({
                        scrollTop: scrollTo
                    }, 850);
                });
            });
        } else {
            mainHtmlForCards.append('<div class="tarot-result-content-next-btn"><a class="tarot-result-register-btn" href="' + (result.next_link || '/') + '">' + result.tr.ins_title + '</a></div>');
        }

        if (result.other_games.length) {
            mainHtml.append('<div class="tarot-result-other-games-head" style="color:' + card.embed_image_text_color + '">' + result.tr.main_other + '</div>');
            let otherGames = $('<div class="tarot-result-other-games"></div>');
            mainHtml.append(otherGames);
            for (let k = 0; k < result.other_games.length; ++k) {
                const otherGame = result.other_games[k];

                const cont = $('<div class="tarot-result-other-game-cont"></div>');
                otherGames.append(cont);

                let img = $('<img />');
                img.attr('src', TemplateHelper.prefixUrl(otherGame.embed_image, config.cardImagesUrl));
                img.attr('alt', otherGame.name);

                cont.append(img);
                cont.append('<div class="tarot-result-other-game-title" style="color:' + otherGame.embed_image_text_color + '">' + otherGame.name + '</div>');
                cont.append('<div class="tarot-result-other-game-desc">' + otherGame.description + '</div>');
                cont.append('<div class="tarot-result-other-game-btn"><a href="' + otherGame.link + '">' + result.tr.see_game + '</a></div>');
            }
        }

        let sideHtml = $('<div class="tarot-result-side"></div>');
        html.append(sideHtml);

        let sideRevHtml = $('<div class="tarot-result-side-rev"></div>');
        sideRevHtml.append('<div class="tarot-result-side-title">' + result.tr.side_rev_title + '</div>');
        sideRevHtml.append('<div class="tarot-result-side-desc">' + result.tr.side_rev_desc + '</div>');
        sideHtml.append(sideRevHtml);

        let sideRevCardsHtml = $('<div class="tarot-result-side-rev-cards"></div>');
        for (let k = 0; k < selectedCardItemIds.length; ++k) {
            const i = +selectedCardItemIds[k];
            const cardItem = cardItems[i].CardItem;
            const cardItemLang = cardItems[i].CardItemLang;
            let el = $('<div class="tarot-result-side-rev-card" tabindex="0"></div>');

            let imgFront = $('<img />');
            imgFront.attr('src', TemplateHelper.prefixUrl(cardItem.image, config.cardItemImagesUrl));
            imgFront.attr('alt', cardItemLang.title);

            el.append('<div class="tarot-result-side-rev-card-title">' + cardItemLang.title + '</div>');
            el.append($('<div class="tarot-result-side-rev-card-image"></div>').append(imgFront));
            el.append('<div class="tarot-result-side-rev-card-description">' + cardItemLang.description + '</div>');

            sideRevCardsHtml.append(el);
        }
        sideRevHtml.append(sideRevCardsHtml);

        let sideRevHtmlMobile = sideRevHtml.clone();
        sideRevHtmlMobile.addClass('mobile');
        html.prepend(sideRevHtmlMobile);

        let sideExpHtml = $('<div class="tarot-result-side-exp"></div>');
        sideHtml.append(sideExpHtml);
        sideExpHtml.append('<div class="tarot-result-side-title">' + result.tr.side_exp_title + '</div>');

        for (let i = 0; i < result.related_experts.length; ++i) {
            const expert = result.related_experts[i];
            const el = $('<div class="tarot-result-side-exp-box"></div>');
            sideExpHtml.append(el);

            const elDiv1 = $('<div class="tarot-result-side-exp-box-img"></div>');
            el.append(elDiv1);
            elDiv1.append('<img class="img-responsive img-circle img-con" alt="' + expert.name + ' Image" src="' + (expert.profile_image || result.default_expert_profile_image) + '" />');
            elDiv1.append('<span class="exp-status exp-status-' + expert.status + '" title="' + expert.status + '"></span>');

            const elDiv2 = $('<div class="tarot-result-side-exp-box-info"></div>');
            el.append(elDiv2);
            elDiv2.append('<div class="tarot-result-side-exp-box-title">' + expert.name +  '<span class="exp-rating"><i class="fa fa-star"></i>' + (Math.round(expert.rating*10)/10) + '</span></div>');

            const elCats = $('<div class="tarot-result-side-exp-box-cats"></div>');
            elDiv2.append(elCats);
            for (let j = 0; j < Math.min(3, expert.categories.length); ++j) {
                elCats.append('<span>' + expert.categories[j].name + '</span>');
            }

            const elDiv3 = $('<div class="tarot-result-side-exp-box-actions"></div>');
            el.append(elDiv3);
            elDiv3.append('<a href="' + expert.link + '"><span class="exp-tel"><i></i>' + result.tr.tel + '</span></a>');
            elDiv3.append('<a href="' + expert.link + '"><span class="exp-email"><i></i>' + result.tr.email + '</span></a>');

            const elDesc = $('<div class="tarot-result-side-exp-desc"></div>');
            sideExpHtml.append(elDesc);
            elDesc.html('<span>' + result.tr.side_exp_see_title + '</span><p>' + result.tr.side_exp_see_desc + '</p>');


            const elBtn = $('<div class="tarot-result-side-exp-btn"></div>');
            sideExpHtml.append(elBtn);
            elBtn.html('<a href="' + expert.link + '">' + result.tr.side_exp_see_more + ' ' + expert.name.toUpperCase() + '</a>');
        }

        //
        resolve();
    });
}
