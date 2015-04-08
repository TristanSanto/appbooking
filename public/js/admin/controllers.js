var appControllers = angular.module('appControllers', []);

appControllers.controller('crenelController', ['$scope', '$window', '$modal', '$translate',
        'Crenel', 'CrenelAdmin', 'SubscriptionAdmin', 'UserAdmin',
    function ($scope, $window, $modal, $translate,
            Crenel, CrenelAdmin, SubscriptionAdmin, UserAdmin) {
        $scope.changeLanguage = function(key) {
            $translate.use(key);
        };

        $scope.now = function () {
            return moment().format('YYYY-MM-DD');
        };
        $scope.event_types = {
            'Standard': '0',
            'Family': '1',
            'Sport': '2',
            'Formation': '3'
        };

        $scope.modal = null;
        $scope.modal2 = null;
        $scope.subscription = null;
        $scope.has_more = true;
        $scope.is_updating = false;
        $scope.crenel_places_min = 1;

        $scope.user = UserAdmin.fetch({id: 1}, function () {
            $scope.user.password = null;
        });

        $scope.updateUser = function () {
            UserAdmin.update({user: $scope.user}, function () {
                $window.alert("Les informations ont bien été mises à jour.");
            });
        };

        $scope.dd_subscription = [
            {
              "text": "Inscrire",
              "click": "subscribeParticipant(subscription)"
            },
            {
              "text": "Désinscrire",
              "click": "unsubscribeParticipant(subscription)"
            },
            {
              "text": "Noter l'absence",
              "click": "setParticipantAbsence(subscription)"
            }
        ];

        $scope.subscriptions = [];

        $scope.months = Crenel.fetch_months(function (data) {
            $scope.current_date = data[data.length - 1].date;
            $scope.origin_date = moment($scope.current_date).subtract('months', 2).format('YYYY-MM-DD');
        });

        /* Managing crenels */
        $scope.refresh = function () {
            $scope.months = Crenel.fetch_months({date: $scope.current_date});
        };

        $scope.fetchLess = function () {
            Crenel.fetch_less({date: $scope.origin_date}, function (data) {
                $scope.months.splice(0, 0, data);
                $scope.origin_date = data.date;
                console.log(data);
            });
        };

        $scope.fetchMore = function () {
            Crenel.fetch_more({date: $scope.current_date}, function (data) {
                $scope.months.push(data);
                $scope.current_date = data.date;
                console.log(data);
            });
        };

        $scope.createCrenel = function (current_date) {
            $scope.form_title = 'CREER UN NOUVEAU CRENEAU';
            $scope.is_updating = false;

            $scope.crenel = {
                name: null,
                type: $scope.event_types[0],
                date: moment(current_date).format('YYYY-MM-DD'),
                time_begin: moment().format('HH:00'),
                time_end: moment().add('hours', 2).format('HH:00'),
                resume: '',
                places: 100
            };

            $scope.subscriptions = [];

            $scope.modal = $modal({scope: $scope, template: 'partials/admin/crenel_modal.html', show: false});

            $scope.modal.$promise.then(function () {
                $scope.modal.show();
            });
        };

        $scope.updateCrenel = function (crenel) {
            $scope.form_title = 'METTRE A JOUR UN CRENEAU EXISTANT';
            $scope.is_updating = true;

            Crenel.fetch_crenel({id: crenel.id}, function (data) {
                $scope.crenel = data;

                if (0 === data.length) {
                    return;
                }

                $scope.crenel_places_min = parseInt($scope.crenel.places_subscribed);

                SubscriptionAdmin.fetch_subscriptions({id: crenel.id}, function (data) {
                    $scope.subscriptions = data;
                    $scope.modal = $modal({scope: $scope, template: 'partials/admin/crenel_modal.html', show: false});

                    $scope.modal.$promise.then(function () {
                        $scope.modal.show();
                    });
                });
            });
        };

        $scope.duplicateCrenel = function () {
            CrenelAdmin.duplicate($scope.crenel, function () {
                $scope.modal.hide();
                $scope.refresh();
            });
        };

        $scope.submitCrenel = function () {
            $scope.crenel.date = moment($scope.crenel.date).format('YYYY-MM-DD');

            if (undefined !== $scope.crenel.id) {
                if (2 === parseInt($scope.crenel.status)) {
                    CrenelAdmin.postpone($scope.crenel, function () {
                        if (null !== $scope.crenel.postpone_id) {
                            $scope.modal.hide();
                            return;
                        }

                        $scope.crenel.status = 1;
                        $scope.crenel.reason = null;
                        $scope.crenel.postpone_date = $scope.crenel.date;
                        $scope.crenel.postpone_time_begin = $scope.crenel.time_begin;
                        $scope.crenel.postpone_time_end = $scope.crenel.time_end;

                        CrenelAdmin.update($scope.crenel, function () {
                            $scope.modal.hide();
                            $scope.refresh();
                        });
                    });
                }
                else {
                    $scope.crenel.postpone_date = $scope.crenel.date;
                    $scope.crenel.postpone_time_begin = $scope.crenel.time_begin;
                    $scope.crenel.postpone_time_end = $scope.crenel.time_end;

                    CrenelAdmin.update($scope.crenel, function () {
                        $scope.modal.hide();
                        $scope.refresh();
                    });
                }
            } else {
                CrenelAdmin.create($scope.crenel, function () {
                    $scope.modal.hide();
                    $scope.refresh();
                });
            }
        };

        $scope.deleteCrenel = function () {
            $scope.modal2 = $modal({scope: $scope, template: 'partials/admin/delete_modal.html', show: false});
            $scope.modal2.$promise.then(function () {
                $scope.modal2.show();
            });
        };

        $scope.deleteDefinitelyCrenel = function () {
            if (undefined !== $scope.crenel.id) {
                CrenelAdmin.delete({id: $scope.crenel.id, reason: $scope.crenel.reason}, function () {
                    $scope.modal2.hide();
                    $scope.modal.hide();
                    $scope.refresh();
                    $window.alert("L'événement - " + $scope.crenel.name + ' -  a bien été supprimé.');
                });
            }
        };

        /* Managing subscriptions */
        $scope.subscribeParticipant = function (subscription) {
            if ('1' === subscription.status) {
                $window.alert('Désolé mais ' + subscription.firstname + ' ' + subscription.lastname +
                        ' est déjà inscrit à cet événement.');
                return;
            }

            SubscriptionAdmin.subscribe(subscription, function () {
                // TODO: refresh modal
                $window.alert(subscription.firstname + ' ' + subscription.lastname + ' a bien été inscrit(e).');
            });
        };

        $scope.unsubscribeParticipant = function (subscription) {
            if ('2' === subscription.status || '3' === subscription.status || '4' === subscription.status) {
                $window.alert('Désolé mais ' + subscription.firstname + ' ' + subscription.lastname +
                        ' a déjà été déscrit(e).');
                return;
            }

            $scope.subscription = subscription;

            // enter a reason
            $scope.modal2 = $modal({scope: $scope, template: 'partials/admin/subscription_modal.html', show: false});
            $scope.modal2.$promise.then(function () {
                $scope.modal2.show();
            });
        };

        $scope.unsubscribeDefinitelyParticipant = function () {
           SubscriptionAdmin.unsubscribe($scope.subscription, function () {
                // TODO: refresh modal
                $scope.modal2.hide();
                $window.alert($scope.subscription.firstname + ' ' + $scope.subscription.lastname + ' a bien été désinscrit(e).');
            });
        };

        $scope.setParticipantAbsence = function (subscription) {
            if ('4' === subscription.status) {
                $window.alert('Désolé mais ' + subscription.firstname + ' ' + subscription.lastname +
                        ' est déjà noté comme absent(e).');
                return;
            }

            SubscriptionAdmin.absent(subscription, function () {
                // TODO: refresh modal
                $window.alert(subscription.firstname + ' ' + subscription.lastname + ' est bien noté comme absent(e).');
            });
        };

        $scope.checkPostponed = function (crenel) {
            if (undefined === crenel.reason) {
                return false;
            }

            if (1 < crenel.status && (null === crenel.reason || '' === crenel.reason.trim())) {
                return true;
            }

            return false;
        };

        $scope.isPostponed = function (crenel) {
            if (2 === crenel.status) {
                return true;
            }

            return false;
        };

        $scope.isCancelled = function (crenel) {
            if (3 === crenel.status) {
                return true;
            }

            return false;
        };

        $scope.isFamilyEvent = function (crenel) {
            if ('1' === crenel.type) {
                return true;
            }

            return false;
        };

        $scope.getSubscriptionStatus = function (status) {
            if ('1' === status) {
                return 'Inscrit(e)';
            }

            if ('2' === status) {
                return 'Désinscrit(e)';
            }

            if ('3' === status) {
                return 'Désinscrit(e) Admin';
            }

            if ('4' === status) {
                return 'Absent';
            }

            return 'En attente de validation';
        };

        $scope.getEventColor = function (crenel) {
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
    }
]);