angular.module('app.downsell', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('downsell', {
                url: '/downsell',
                templateUrl: 'modules/downsell/downsell.tpl.html',
                controller: 'DownsellCtrl',
                data: {
                    pageTitle: 'DEX Trader - Down sell'
                }
            });
    })

    .controller('DownsellCtrl', ['$scope', function ($scope) {

    }]);