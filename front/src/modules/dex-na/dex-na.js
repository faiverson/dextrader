angular.module('app.dex-na', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_na', {
                url: '/dexna',
                templateUrl: 'modules/dex-na/dex-na.tpl.html',
                data: {
                    pageTitle: 'Dex NA'
                }
            })
            .state('dex_na.coming_soon', {
                url: '/coming-soon',
                templateUrl: 'modules/dex-na/dex-na.coming-soon.tpl.html',
                controller: 'ComingSoonCtrl',
                data: {
                    pageTitle: 'Dex NA - Coming Soon'
                }
            });
    })

    .controller('ComingSoonCtrl', ['$scope', function ($scope) {

    }]);