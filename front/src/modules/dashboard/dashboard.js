angular.module('app.dashboard', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dashboard', {
                url: '/dashboard',
                templateUrl: 'modules/dashboard/dashboard.tpl.html',
                controller: 'DashboardCtrl',
                data: {
                    pageTitle: 'Dashboard',
                    bodyClass: 'page-dashboard'
                }
            });
    })

    .controller('DashboardCtrl', ['$scope', function ($scope) {

    }]);