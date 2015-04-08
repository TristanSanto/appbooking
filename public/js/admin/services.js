var appServices = angular.module('appServices', ['ngResource']);

appServices.factory('Crenel', ['$resource',
    function ($resource) {
        return $resource('/crenel/:verb', {}, {
            fetch_crenel: {method: 'GET', params: {verb: 'fetch_crenel'}},
            fetch_months: {method: 'GET', params: {verb: 'fetch_months'}, isArray: true},
            fetch_more: {method: 'GET', params: {verb: 'fetch_more'}},
            fetch_less: {method: 'GET', params: {verb: 'fetch_less'}}
        });
    }
]);

appServices.factory('CrenelAdmin', ['$resource',
    function ($resource) {
        return $resource('/admin_crenel/:verb', {}, {
            create: {method: 'POST', params: {verb: 'create'}},
            update: {method: 'POST', params: {verb: 'update'}},
            duplicate: {method: 'POST', params: {verb: 'duplicate'}},
            delete: {method: 'GET', params: {verb: 'delete'}},
            postpone: {method: 'POST', params: {verb: 'postpone'}}
        });
    }
]);

appServices.factory('SubscriptionAdmin', ['$resource',
    function ($resource) {
        return $resource('/admin_subscription/:verb', {}, {
            fetch_subscriptions: {method: 'GET', params: {verb: 'fetch_subscriptions'}, isArray: true},
            subscribe: {method: 'POST', params: {verb: 'subscribe'}},
            unsubscribe: {method: 'POST', params: {verb: 'unsubscribe'}},
            absent: {method: 'POST', params: {verb: 'absent'}},
            move: {method: 'POST', params: {verb: 'move'}}
        });
    }
]);

appServices.factory('UserAdmin', ['$resource',
    function ($resource) {
        return $resource('/admin_user/:verb', {}, {
            fetch: {method: 'GET', params: {verb: 'fetch'}},
            update: {method: 'POST', params: {verb: 'update'}}
        });
    }
]);