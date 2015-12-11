angular.module('app.home', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('home', {
                url: '/home',
                templateUrl: 'modules/home/home.tpl.html',
                controller: 'HomeCtrl',
                data: {
                    pageTitle: 'Home'
                }
            });
    })

    .controller('HomeCtrl', ['$scope', function ($scope) {

    }]);