angular.module('app.thankyou', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('thankyou', {
                url: '/thankyou',
                templateUrl: 'modules/thankyou/thankyou.tpl.html',
                controller: 'ThankyouCtrl',
                data: {
                    pageTitle: 'DEX Trader - Thank you'
                }
            });
    })

    .controller('ThankyouCtrl', ['$scope', function ($scope) {

    }]);
