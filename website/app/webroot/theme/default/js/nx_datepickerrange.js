var nx_datepickerrange = {
    monthFrench : ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],

    init: function(){

        $('.date-range').daterangepicker(
            {
                opens: (App.isRTL() ? 'left' : 'right'),
                ranges: {
                    'Aujourd\'hui': ['today', 'today'],
                    'Hier': ['yesterday', 'yesterday'],
                    'Les 7 derniers jours': [Date.today().add({
                        days: -6
                    }), 'today'],
                    'Les 29 derniers jours': [Date.today().add({
                        days: -29
                    }), 'today'],
                    'Ce mois-ci': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
                    'Le mois dernier': [Date.today().moveToFirstDayOfMonth().add({
                        months: -1
                    }), Date.today().moveToFirstDayOfMonth().add({
                        days: -1
                    })]
                },
                format: 'dd-MM-yyyy',
                separator: ' au ',
                locale: {
                    applyLabel: 'Appliquer',
                    fromLabel: 'Du',
                    toLabel: 'Au',
                    customRangeLabel: 'Personnaliser',
                    daysOfWeek: ["D", "L", "Ma", "Me", "J", "V", "S"],
                    monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                    monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Déc"],
                    firstDay: 1
                },
                buttonClasses: ['green'],
                startDate: Date.today().add({
                    days: -29
                }),
                endDate: Date.today()
            },
            function (start, end) {
                $('#form-date-range').submit();
            }
        );
    }
}

$(document).ready(function(){nx_datepickerrange.init();});