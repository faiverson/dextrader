angular.module('app.dashboard', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dashboard', {
                url: '/dashboard',
                templateUrl: 'modules/dashboard/dashboard.tpl.html',
                controller: 'DashboardController',
                data: {
                    pageTitle: 'Dashboard'
                }
            });
    })
    .controller('DashboardController', ['$scope', function ($scope) {

    }]);