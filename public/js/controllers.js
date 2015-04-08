var appControllers = angular.module('appControllers', []);

appControllers.controller('crenelController', ['$scope', '$window', '$modal', '$translate',
    'Crenel', 'Subscription',
    function($scope, $window, $modal, $translate,
            Crenel, Subscription) {
        $scope.changeLanguage = function(key) {
            $translate.use(key);
        };

        $scope.now = function() {
            return moment().format('YYYY-MM-DD');
        };

        $scope.modal = null;
        $scope.has_more = true;
        $scope.user_signup = {
            crenel_id: null,
            lastname: null,
            firstname: null,
            email: null,
            family: {
                with_someone: false,
                child_firstname1: null,
                child_birthday1: null,
                child_firstname2: null,
                child_birthday2: null,
                child_firstname3: null,
                child_birthday3: null,
                child_firstname4: null,
                child_birthday4: null
            }
        };
        $scope.user_withdraw = {
            crenel_id: null,
            email: null,
            reason: null
        };

        $scope.months = Crenel.fetch_months(function(data) {
            $scope.current_date = data[data.length - 1].date;
        });

        $scope.subscriptions = [];

        $scope.refresh = function() {
            $scope.months = Crenel.fetch_months({date: $scope.current_date});
        };

        $scope.fetchMore = function() {
            Crenel.fetch_more({date: $scope.current_date}, function(data) {
                $scope.months.push(data);
                $scope.current_date = data.date;
            });
        };

        $scope.showDetail = function(crenel) {
            Crenel.fetch_crenel({id: crenel.id}, function(data) {
                $scope.crenel = data;
                $scope.user_signup.crenel_id = crenel.id;
                $scope.user_withdraw.crenel_id = crenel.id;

                if (0 === data.length) {
                    return;
                }

               $scope.user_signup = {
                    crenel_id: crenel.id,
                    lastname: null,
                    firstname: null,
                    email: null,
                    family: {
                        with_someone: false,
                        child_firstname1: null,
                        child_birthday1: null,
                        child_firstname2: null,
                        child_birthday2: null,
                        child_firstname3: null,
                        child_birthday3: null,
                        child_firstname4: null,
                        child_birthday4: null
                    }
                };

                Subscription.fetch_subscriptions({id: crenel.id}, function(data) {
                    $scope.subscriptions = data;
                    $scope.modal = $modal({scope: $scope, template: 'partials/crenel_modal.html', show: false});

                    $scope.modal.$promise.then(function() {
                        $scope.modal.show();
                    });
                });
            });
        };

        $scope.signup = function() {
            Subscription.signup($scope.user_signup, function(data) {
                $scope.user_signup.email = null;
                $scope.modal.hide();
                $window.alert(data.error);
                $scope.refresh();
            });
        };

        $scope.withdraw = function() {
            Subscription.withdraw($scope.user_withdraw, function() {
                $scope.user_withdraw.email = null;
                $scope.user_withdraw.reason = null;
                $scope.modal.hide();

                // show dialog confirmation
                $scope.modal = $modal({scope: $scope, template: 'partials/withdraw_confirmation.html', show: false});
                $scope.modal.$promise.then(function() {
                    $scope.modal.show();
                });
            });
        };

        $scope.isActive = function(crenel) {
            if (1 === crenel.status) {
                return true;
            }

            return false;
        };

        $scope.isBookable = function(crenel) {
            if (parseInt(crenel.places_subscribed) < parseInt(crenel.places)) {
                return true;
            }

            return false;
        };

        $scope.isPostponed = function(crenel) {
            if (2 === crenel.status) {
                return true;
            }

            return false;
        };

        $scope.isCancelled = function(crenel) {
            if (3 === crenel.status) {
                return true;
            }

            return false;
        };

        $scope.isFamilyEvent = function(crenel) {
            if ('1' === crenel.type) {
                return true;
            }

            return false;
        };

        $scope.getSubscriptionStatus = function(status) {
            if ('1' === status) {
                return 'Inscrit(e)';
            }

            return 'En attente de validation';
        };

        $scope.getEventColor = function(crenel) {
            var color = 'place ';

            switch (crenel.type) {
                case '1':
                    color += 'event-family';
                    break;
                case '2':
                    color += 'event-sport';
                    break;
                case '3':
                    color += 'event-formation';
                    break;
                default:
                    color += 'event-standard';
                    break;
            }

            return color;
        };

        $scope.getEventInfoColor = function(crenel) {
            var color = 'glyphicon glyphicon-volume-up event-type-color ';

            switch (crenel.type) {
                case '1':
                    color += 'event-family-color';
                    break;
                case '2':
                    color += 'event-sport-color';
                    break;
                case '3':
                    color += 'event-formation-color';
                    break;
                default:
                    color += 'event-standard-color';
                    break;
            }

            return color;
        };
    }
]);