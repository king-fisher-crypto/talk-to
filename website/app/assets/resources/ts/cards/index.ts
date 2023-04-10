/// <reference path="./Globals" />
/// <reference path="./TarotGame" />

if (typeof (<any>window).TarotConfig !== 'undefined') {
    $((<any>window).TarotConfig.selector).each(function(i: any, v: any) {
        new TarotGame($(v), (<any> window).TarotConfig);
    });
}
