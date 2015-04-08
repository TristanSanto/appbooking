var appServices = angular.module('appServices', ['ngResource']);

appServices.factory('Crenel', ['$resource',
    function ($resource) {
        return $resource('/crenel/:verb', {}, {
            fetch_crenel: {method: 'GET', params: {verb: 'fetch_crenel'}},        
            fetch_months: {method: 'GET', params: {verb: 'fetch_months'}, isArray: true},
            fetch_more: {method: 'GET', params: {verb: 'fetch_more'}}      
        });
    }
]);

appServices.factory('Subscription', ['$resource',
    function ($resource) {
        return $resource('/subscription/:verb', {}, {    
            fetch_subscriptions: {method: 'GET', params: {verb: 'fetch_subscriptions'}, isArray: true},
            signup: {method: 'POST', params: {verb: 'signup'}},
            withdraw: {method: 'POST', params: {verb: 'withdraw'}}
        });
    }
]);