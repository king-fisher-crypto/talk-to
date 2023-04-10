;(function($){
$.fn.datepicker.dates['fr'] = {
days: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
daysShort: ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
daysMin: ["dim", "lun", "ma", "me", "jeu", "ven", "sam"],
months: ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
monthsShort: ["janv.", "févr.", "mars", "avril", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc."],
today: "Aujourd'hui",
monthsTitle: "Mois",
clear: "Effacer",
weekStart: 1,
format: "dd/mm/yyyy"
};
}(jQuery));


var nx_datepicker = {
    init: function(){

        $('.date-picker').datepicker(
            {
               language: 'fr',
autoclose: true,
todayHighlight: true
            }
        );
    }
}

$(document).ready(function(){nx_datepicker.init();});