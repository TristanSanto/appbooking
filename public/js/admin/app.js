var appBooking = angular.module('appBooking', [
    'ngRoute',
    'ngSanitize',
    'mgcrea.ngStrap',
    'pascalprecht.translate',
    'appControllers',
    'appServices'
]);

appBooking.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/crenels', {
                templateUrl: 'partials/admin/crenel.html',
                controller: 'crenelController'
            }).
            otherwise({
                redirectTo: '/crenels'
            });
    }
]).config(['$datepickerProvider',
    function($datepickerProvider) {
        angular.extend($datepickerProvider.defaults, {
            dateFormat: 'dd/MM/yyyy',
            autoclose: true
        });
    }
]).config(['$timepickerProvider',
    function($timepickerProvider) {
        angular.extend($timepickerProvider.defaults, {
            timeFormat: 'HH:mm',
            timeType: 'string',
             minuteStep: 5
        });
    }
]);

appBooking.config(function ($modalProvider) {
    angular.extend($modalProvider.defaults, {
        backdrop: 'static'
    });
});

appBooking.config(function ($translateProvider) {
  $translateProvider.translations('fr', {
    BUTTON_LANG_EN: 'anglais',
    BUTTON_LANG_FR: 'français',
    NO_SCHEDULED_EVENT: "Pas d'événement planifié pour ce mois-ci",
    FULL: "Close",
    PLACES_LEFT: "place(s) libres",
    VIEW: "VOIR",
    ATTEND: "ASSISTER",
    POSTPONED: "REPORTE",
    CANCELLED: "ANNULER",
    PREVIOUS_MONTH: "PREVIOUS MONTH",
    NEXT_MONTH: "MOIS SUIVANT",
    PRACTICAL_INFORMATION: "Informations pratiques",
    TIMESLOT: "Horaires",
    LOCATION: "Lieu",
    LOGOUT: "DECONNEXION"
  });

  $translateProvider.translations('en', {
    BUTTON_LANG_EN: 'english',
    BUTTON_LANG_FR: 'french',
    NO_SCHEDULED_EVENT: 'No scheduled event for this month',
    FULL: "Full",
    PLACES_LEFT: "place(s) left",
    VIEW: "VIEW",
    ATTEND: "ATTEND",
    POSTPONED: "POSTPONED",
    CANCELLED: "CANCELLED",
    PREVIOUS_MONTH: "PREVIOUS MONTH",
    NEXT_MONTH: "NEXT MONTH",
    PRACTICAL_INFORMATION: "Pratical Information",
    TIMESLOT: "Timeslot",
    LOCATION: "Location",
    LOGOUT: "DECONNEXION"
  });

  $translateProvider.preferredLanguage('fr');
});