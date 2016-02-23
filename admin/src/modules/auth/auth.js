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

    .controller('AuthController', ['$scope', '$state', 'AuthService', 'Notification',
        function ($scope, $state, AuthService, Notification) {

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
                Notification.success('Welcome ' + user.full_name);
                $state.go('users');
            };

            vm.errorLogin = function (response) {
				Notification.error(response.error);
            };

        }]);