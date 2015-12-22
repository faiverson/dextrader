angular.module('app.dex-fx', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_fx', {
                url: '/dexfx',
                templateUrl: 'modules/dex-fx/dex-fx.tpl.html',
                data: {
                    pageTitle: 'Dex FX'
                }
            })
            .state('dex_fx.coming_soon', {
                url: '/coming-soon',
                templateUrl: 'modules/dex-fx/dex-fx.coming-soon.tpl.html',
                controller: 'ComingSoonFXCtrl',
                data: {
                    pageTitle: 'Dex FX - Coming Soon'
                }
            });
    })

    .controller('ComingSoonFXCtrl', ['$scope', 'UserService', 'Notification', '$state', 'AuthService',
        function ($scope, UserService, Notification, $state, AuthService) {
            var vm = this;

            $scope.user = {
                product: 'FX',
                email: AuthService.getLoggedInUser().email,
                phone: AuthService.getLoggedInUser.phone
            };

            vm.success = function (res) {
                Notification.success('Congratulations! Successfully subscribed.');

                $state.go('dashboard');
            };

            vm.error = function (err) {
                if (angular.isArray(err.data.error)) {
                    angular.forEach(err.data.error, function (e) {
                        Notification.error(e);
                    });
                } else {
                    Notification.error(err.data.error);
                }
            };

            $scope.send = function () {
                if ($scope.soonForm.$valid) {
                    UserService.soon($scope.user)
                        .then(vm.success, vm.error);
                }
            };
        }]);