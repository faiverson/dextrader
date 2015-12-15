angular.module('app.dex-fx', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_fx', {
                url: '/dexfx',
                templateUrl: 'modules/dex-fx/dex-fx.tpl.html',
                data: {
                    pageTitle: 'Dex FX'
                }
            })
            .state('dex_fx.coming_soon', {
                url: '/coming-soon',
                templateUrl: 'modules/dex-fx/dex-fx.coming-soon.tpl.html',
                controller: 'ComingSoonFXCtrl',
                data: {
                    pageTitle: 'Dex FX - Coming Soon'
                }
            });
    })

    .controller('ComingSoonFXCtrl', ['$scope', function ($scope) {

    }]);