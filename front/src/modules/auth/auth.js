angular.module('app.auth', ['ui.router', 'ui.bootstrap.showErrors'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'modules/auth/auth.form.tpl.html',
                controller: 'AuthController as auth',
                data: {
                    pageTitle: 'Login Page',
                    bodyClass: 'page-login'
                }
            })
            .state('logout', {
                url: '/logout',
                //templateUrl: 'modules/users/users.form.tpl.html',
                controller: 'AuthController',
                data: {
                    pageTitle: 'Login Page',
                    bodyClass: 'page-login'
                }
            });
    })

    .controller('AuthController', ['$scope', '$state', 'AuthService', 'Notification', '$uibModal',
        function ($scope, $state, AuthService, Notification, $uibModal) {

            var vm = this;

            //this is because we use syntax Controller as Ctrl
            $scope.setFromScope = function (scope) {
                $scope.form = scope;
            };

            $scope.login = function () {

                $scope.$broadcast('show-errors-check-validity');

                if ($scope.form.loginForm.$valid) {
                    AuthService.login(vm.username, vm.password)
                        .then(vm.successLogin, vm.errorLogin);
                }

            };

            vm.successLogin = function () {
                var user = AuthService.getLoggedInUser();

                Notification.success("Welcome " + user.full_name);

                $state.go('dashboard');
            };

            vm.errorLogin = function (err) {
                Notification.error(err.error);
            };


            $scope.openForgotPassword = function () {

                var modalInstance = $uibModal.open({
                    templateUrl: 'modules/auth/auth.forgot-password.tpl.html',
                    controller: 'ForgotPasswordCtrl'
                });

                modalInstance.result.then(function (selectedItem) {
                    //$scope.selected = selectedItem;
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };

        }])

    .controller('ForgotPasswordCtrl', ['$scope', '$uibModalInstance', 'AuthService', function ($scope, $uibModalInstance, AuthService) {
        $scope.close = function () {
            $uibModalInstance.dismiss('close');
        };

        $scope.send = function () {

            AuthService.forgotPassword($scope.email)
                .then(function () {
                    $uibModalInstance.close();
                }, function () {

                });
        };
    }]);