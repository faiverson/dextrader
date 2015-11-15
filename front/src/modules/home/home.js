angular.module('app.home', ['ui.router'])
    .config(function config( $stateProvider ) {
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

    .controller('HomeCtrl', ['$scope', 'UserService',
        function ($scope, UserService) {

            var vm = this;

            $scope.pagination = {
                itemsPerPage: 3,
                currentPage: 1,
                getPage: function () {
                    vm.getUsers();
                }
            };

            vm.getUsers = function(page){
                function success(response){
                    $scope.users = response.data.splice(($scope.pagination.currentPage - 1)* $scope.pagination.itemsPerPage, $scope.pagination.itemsPerPage);
                }

                function error(res){
                    console.log(res);
                }

				UserService.getUsers().then(success, error);
            };

            vm.init = function(){
                vm.getUsers();
            };

            vm.init();

        }]);