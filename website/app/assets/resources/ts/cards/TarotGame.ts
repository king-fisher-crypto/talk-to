/// <reference path="./Globals" />
/// <reference path="./enums/TarotGameStep" />
/// <reference path="./helpers/TemplateHelper" />
/// <reference path="./animations/index" />
/// <reference path="./steps/index" />

/**
 * @brief This is the main class for the game.
 */
class TarotGame {
    /// The configuration used in this game
    private config: any = null;

    /// The jquery container containing the game
    private container: any = null;

    /// Set to \c true if the game is ready (all its assets are loaded)
    private isReady = false;

    /// Set to \c true if the window is loaded (i.e. window.on('load') is called)
    private isWindowLoaded = false;

    /// How many images remain to preload before assuming the game is ready
    private remainingImagesToPreload = 0;

    /// Result object after selection
    private result: any = {};

    /// Card Item Ids that are selected
    private selectedCardItemIds: number[] = [];

    /// The jquery spinner element
    private spinnerEl: any = null;

    /// Current step
    private step: TarotGameStep = TarotGameStep.NONE;

    /**
     * @brief The constructor of this class.
     * @param JQuery|HTMLElement container The container of the tarot game.
     * @param any config The configuration to use, should contain the following keys:
     *                      `card`, `cardItem`, `cardImagesUrl` and `cardItemImagesUrl`.
     */
    constructor(container: any, config: any) {
        this.config = config;
        this.container = $(container);

        // preload images related to the card model
        if (config && config.card && config.card.Card) {
            this.preloadImagesFromConfig(config.card.Card, <string> config.cardImagesUrl);
        }

        // preload images related to the card item models
        if (config && config.cardItems) {
            for (let k in config.cardItems) {
                if (config.cardItems[k] && config.cardItems[k].CardItem) {
                    this.preloadImagesFromConfig(config.cardItems[k].CardItem, <string> config.cardItemImagesUrl);
                }
            }
        }

        // show loading spinner and wait for the window to load
        this.showSpinner();

        // wait for the window to load
        //this.isWindowLoaded = true; // actually, don't wait. Comment this and uncomment next lines if you wish to wait.
        $(window).on('load', () => {
            this.isWindowLoaded = true;
            this.checkReady();
        });
    }

    /**
     * @brief Adds the given card item id to the list of selected card item ids.
     * @param number cardItemId The card item id to add.
     */
    public addSelectedCardItemId(cardItemId: number) {
        this.selectedCardItemIds.push(cardItemId);
    }

    /**
     * @brief Checks if the game is ready (in particular, if all images are loaded) and set the isReady private field.
     */
    private checkReady() {
        if (this.isReady) {
            return;
        }

        if (this.remainingImagesToPreload > 0 || !this.isWindowLoaded) {
            return;
        }

        this.isReady = true;
        this.hideSpinner();

        // init the game
        this.init();
    }

    /**
     * @brief Returns the cards distribution animation and ordering functions based on current game display mode.
     * @return any The cards distribution animation and ordering functions based on current game display mode
     */
    public getCardDistributionAnimationFn() {
        let animDistributeFn = animDistributeCardsLine;
        let distributeOrderFn = DistributeOrder.outFirstDistribution;
        const displayMode = +this.config.card.Card.display_mode;
        if (displayMode === DisplayMode.LINE) {
            animDistributeFn = animDistributeCardsLine;
        } else if (displayMode === DisplayMode.TWO_LINES) {
            animDistributeFn = animDistributeCardsTwoLines;
        } else if (displayMode === DisplayMode.SKEWED_LINE) {
            animDistributeFn = animDistributeCardsSkewedLine;
        } else if (displayMode === DisplayMode.ARC_LINE) {
            animDistributeFn = animDistributeCardsArc;
        }
        return {
            animationFn: animDistributeFn,
            orderFn: distributeOrderFn
        };
    }

    /**
     * @brief Returns a reference to the game configuration object.
     * @return JQuery A reference to the game configuration object.
     */
    public getConfig() {
        return this.config;
    }

    /**
     * @brief Returns the jquery container containing this game.
     * @return JQuery The container containing this game.
     */
    public getContainer() {
        return this.container;
    }

    /**
     * @brief Returns the selection result that was queried from backend.
     * @return any The selection result that was queried from backend.
     */
    public getResult() {
        return this.result;
    }

    /**
     * @brief Returns the selected cards in current game.
     * @return number[] The selected cards in current game.
     */
    public getSelectedCardItemIds() {
        return this.selectedCardItemIds;
    }

    public getStep(): TarotGameStep {
        return this.step;
    }

    /**
     * @brief Hides the loading spinner.
     */
    public hideSpinner() {
        if (!this.spinnerEl) {
            return;
        }
        this.spinnerEl.remove();
        this.spinnerEl = null;
    }

    /**
     * @brief Initiates the game.
     */
    private async init() {
        this.selectedCardItemIds = [];
        this.step = TarotGameStep.NONE;
        await initialStep(this);
        this.step = TarotGameStep.INITIATED_GAME;
        await showCardsStep(this);
        this.step = TarotGameStep.READY_TO_SHUFFLE;
        await shuffleCardsStep(this);
        this.step = TarotGameStep.CHOOSE_CARDS;
        await chooseCardsStep(this);
        this.step = TarotGameStep.POST_CHOOSE_CARDS;
        await this.queryProcessSelection();
        this.step = TarotGameStep.PROCESS_SELECTION;
        await processStep(this);
        this.step = TarotGameStep.SHOW_RESULTS;
        await resultStep(this);
    }

    /**
     * @brief Event called when one single image image is done preloading.
     */
    private onPreloadImageFinish() {
        this.remainingImagesToPreload--;
        this.checkReady();
    }

    /**
     * @brief Preload all images in the given data (i.e. any value with a key ending with the `'image'`).
     * @param any data The url of the image to preload.
     * @param callable|null loadCallback    The function to call when the image is done preloading.
     * @param callable|null errorCallback   The function to call when there was an error preloading the image.
     */
    private preloadImagesFromConfig(data: any, urlPref: string) {
        for (let k in data) {
            if (k.endsWith('image') && data[k]) {
                this.remainingImagesToPreload++;
                TemplateHelper.preloadImage(urlPref + (<string> data[k]), this.onPreloadImageFinish.bind(this), this.onPreloadImageFinish.bind(this));
            }
        }
    }

    /**
     * @brief Queries the backend with current selection and updates game data.
     */
    private queryProcessSelection() {
        return new Promise<void>((resolve, reject) => {
            const that = this;
            let real_card_item_ids = [];
            for (let i = 0; i < this.selectedCardItemIds.length; ++i) {
                real_card_item_ids.push(this.config.cardItems[this.selectedCardItemIds[i]].CardItem.card_item_id);
            }
            $.ajax({
                url: '/cards/process_selection',
                method: 'POST',
                data: {
                    card_id: this.config.card.Card.card_id,
                    lang_id: this.config.card.CardLang.lang_id,
                    card_item_ids: real_card_item_ids
                },
                dataType: 'json'
            }).done(function(data: any) {
                if (!data || (typeof data.error !== 'undefined' && data.error)) {
                    reject();
                    return;
                }
                that.result = data;
                resolve();
            }).fail(function(jqXHR: any, textStatus: string, errorThrown: any) {
                reject();
            });
        });
    }

    /**
     * @brief Shows the loading spinner.
     */
    public showSpinner() {
        this.hideSpinner();
        this.spinnerEl = $('<div class="cards-spinner-container"><div class="cards-spinner">Loading ...</div></div>');
        this.container.append(this.spinnerEl);
    }
}
