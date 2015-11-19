angular.module('app.auth', ['ui.router', 'ui.bootstrap.showErrors'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'modules/auth/auth.form.tpl.html',
                controller: 'AuthController as auth',
                data: {
                    pageTitle: 'Login Page'
                }
            })
            .state('logout', {
                url: '/logout',
                //templateUrl: 'modules/users/users.form.tpl.html',
                controller: 'AuthController',
                data: {
                    pageTitle: 'Login Page'
                }
            });
    })

    .controller('AuthController', ['$scope', '$state', 'AuthService', '$rootScope',
        function ($scope, $state, AuthService, $rootScope) {

            var vm = this;

            //this is because we use syntax Controller as Ctrl
            $scope.setFromScope = function (scope) {
                $scope.form = scope;
            };

            $scope.login = function () {

                if ($scope.form.loginForm.$valid) {
                    AuthService.login(vm.username, vm.password)
                        .then(vm.successLogin, vm.errorLogin);
                }

            };

            vm.successLogin = function () {
                var user = AuthService.getLoggedInUser();

                $rootScope.$broadcast('user-login-success');

                //message

                $state.go('users');
            };

            vm.errorLogin = function () {
                //message
            };

        }]);