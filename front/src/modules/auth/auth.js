angular.module('app.auth', ['ui.router', 'ui.bootstrap.showErrors'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'modules/auth/auth.form.tpl.html',
                controller: 'AuthController',
                data: {
                    pageTitle: 'Login Page',
                    bodyClass: 'page-login',
                    isPublic: true
                }
            })
            .state('doLogin', {
                url: '/doLogin?token',
                templateUrl: 'modules/auth/auth.form.tpl.html',
                controller: 'DoLoginController',
                data: {
                    pageTitle: 'Login Page',
                    bodyClass: 'page-login',
                    isPublic: true
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
            })
            .state('reset_password', {
                url: '/password/reset/:token',
                templateUrl: 'modules/auth/auth.reset-password.tpl.html',
                controller: 'ResetPasswordCtrl',
                data: {
                    pageTitle: 'Reset Password',
                    bodyClass: 'page-login',
                    isPublic: true
                }
            });
    })

    .controller('AuthController', ['$scope', '$state', 'AuthService', 'Notification', '$uibModal',
        function ($scope, $state, AuthService, Notification, $uibModal) {

            var vm = this;

            $scope.login = function () {
                var prom;
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.loginForm.$valid) {
                    prom = AuthService.login($scope.username, $scope.password);
                    prom.then(vm.successLogin, vm.errorLogin);
                }
                return prom;
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

                modalInstance.result.then(function (email) {
                    Notification.success('An E-mail has been sent to ' + email + ' with instructions to reset the Password!');
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };

        }])

    .controller('DoLoginController', ['$scope', '$state', 'AuthService', 'Notification',
        function ($scope, $state, AuthService, Notification) {

            var vm = this;

            vm.init = function () {
                if (angular.isDefined($state.params.token)) {
                    AuthService.logout().then(function () {
                        AuthService.setUserToken($state.params.token);

                        var user = AuthService.getLoggedInUser();

                        Notification.success("Welcome " + user.full_name);

                        $state.go('affiliates.how_it_works');
                    });
                } else {
                    $state.go('dashboard');
                }
            };

            vm.init();

        }])

    .controller('ForgotPasswordCtrl', ['$scope', '$uibModalInstance', 'AuthService', 'Notification', function ($scope, $uibModalInstance, AuthService, Notification) {
        $scope.close = function () {
            $uibModalInstance.dismiss('close');
        };

        $scope.send = function () {

            AuthService.forgotPassword($scope.email)
                .then(function () {
                    $uibModalInstance.close($scope.email);
                }, function (err) {
                    Notification.error(err.error);
                });
        };
    }])

    .controller('ResetPasswordCtrl', ['$scope', 'AuthService', 'Notification', '$state', '$stateParams', function ($scope, AuthService, Notification, $state, $stateParams) {

        $scope.send = function () {

            AuthService.resetPassword($stateParams.token, $scope.auth.email, $scope.auth.password, $scope.auth.password_confirmation)
                .then(function (res) {
                    var user = AuthService.getLoggedInUser();

                    Notification.success('Password has been reset successfully!');

                    Notification.success("Welcome " + user.full_name);

                    $state.go('dashboard');

                }, function (err) {
                    Notification.error(err.error);
                });
        };
    }]);