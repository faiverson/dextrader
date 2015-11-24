angular.module('app.user-profile', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('user', {
                url: '/profile',
                templateUrl: 'modules/user-profile/user.profile.tpl.html',
                controller: 'UserProfileCtrl',
                data: {
                    pageTitle: 'Dashboard'
                }
            })
            .state('user.profile', {
                url: '/settings',
                templateUrl: 'modules/user-profile/user.profile.settings.tpl.html',
                controller: 'UserProfileSettingsCtrl',
                data: {
                    pageTitle: 'User Settings'
                }
            })
            .state('user.support', {
                url: '/settings',
                templateUrl: 'modules/user-profile/user.profile.form.tpl.html',
                controller: 'UserProfileSettingsCtrl',
                data: {
                    pageTitle: 'User Settings'
                }
            })
            .state('user.billing', {
                url: '/settings',
                templateUrl: 'modules/user-profile/user.profile.billing.tpl.html',
                controller: 'UserProfileSettingsCtrl',
                data: {
                    pageTitle: 'User Settings'
                }
            });
    })

    .controller('UserProfileCtrl', ['$scope', function ($scope) {

    }])

    .controller('UserProfileSettingsCtrl', ['$scope', 'UserService', 'AuthService', 'Notification', function ($scope, UserService, AuthService, Notification) {
        var vm = this;

        $scope.save = function () {
            if ($scope.userForm.$valid) {

                if (angular.isUndefined($scope.user.password) || $scope.user.password.length === 0) {
                    delete $scope.user.password;
                    delete $scope.user.confirm_password;
                }

                UserService.saveUser($scope.user)
                    .then(vm.successSaveUser, vm.errorSaveUser);

            } else {
                $scope.$broadcast('show-errors-check-validity');
            }
        };

        vm.successSaveUser = function success(res){
            Notification.success("User settings changed successfully!");
        };

        vm.errorSaveUser = function error(err){
            Notification.error("Ups! something went wrong, try again!");
        };

        vm.getUser = function () {

            function success(res) {
                $scope.user = res.data;
            }

            function error(err) {
                Notification.error(err);
            }

            UserService.getUser(AuthService.getLoggedInUser().user_id)
                .then(success, error);
        };

        vm.init = function () {
            vm.getUser();
        };

        vm.init();
    }]);
