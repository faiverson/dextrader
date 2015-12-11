angular.module('app.user-profile', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('user_profile', {
                url: '/user-profile/:id',
                templateUrl: 'modules/user-profiles/user-profile.tpl.html',
                controller: 'UserProfileController',
                data: {
                    pageTitle: 'User profile'
                }
            });
    })
    .controller('UserProfileController', ['$scope', '$state', '$stateParams', 'UserService',
        function ($scope, $state, $stateParams, UserService) {
            var vm = this;

            vm.loadUser = function (id) {
                var prom = UserService.getUser(id);

                function success(res) {
                    $scope.user = res.data;
                }

                function error(err) {

                }

                prom.then(success, error);

                return prom;
            };

            vm.init = function () {
                $scope.user = {};

                if (angular.isDefined($stateParams.id)) {
                    vm.loadUser($stateParams.id);
                } else {
                    $state.go('users');
                }
            };

            vm.init();

        }]);
