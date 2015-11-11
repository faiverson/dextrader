angular.module('app.home', ['ui.router', 'datatables', 'datatables.bootstrap'])
    .config(function config( $stateProvider ) {
        $stateProvider
            .state('home', {
                url: '/home',
                templateUrl: 'home/home.tpl.html',
                controller: 'HomeCtrl',
                data: {
                    pageTitle: 'Home'
                }
            });
    })

    .controller('HomeCtrl', ['$scope', 'DTOptionsBuilder', 'DTColumnBuilder', 'DummyService',
        function ($scope, DTOptionsBuilder, DTColumnBuilder, DummyService) {

            $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function () {
                return DummyService.getDummyData();
            }).withPaginationType('full_numbers').withBootstrap();

            $scope.dtColumns = [
                DTColumnBuilder.newColumn('id').withTitle('ID'),
                DTColumnBuilder.newColumn('firstName').withTitle('First name'),
                DTColumnBuilder.newColumn('lastName').withTitle('Last name').notVisible()
            ];

        }]);