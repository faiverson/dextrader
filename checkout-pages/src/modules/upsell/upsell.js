angular.module('app.upsell', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('upsell', {
                url: '/upsell',
                templateUrl: 'modules/upsell/upsell.tpl.html',
                controller: 'UpsellCtrl',
                data: {
                    pageTitle: 'DEX Trader - Up sell'
                }
            });
    })

    .controller('UpsellCtrl', ['$scope', function ($scope) {

    }]);
