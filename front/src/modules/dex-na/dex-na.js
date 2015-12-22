angular.module('app.dex-na', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_na', {
                url: '/dexna',
                templateUrl: 'modules/dex-na/dex-na.tpl.html',
                data: {
                    pageTitle: 'Dex NA'
                }
            })
            .state('dex_na.coming_soon', {
                url: '/coming-soon',
                templateUrl: 'modules/dex-na/dex-na.coming-soon.tpl.html',
                controller: 'ComingSoonCtrl',
                data: {
                    pageTitle: 'Dex NA - Coming Soon'
                }
            });
    })

    .controller('ComingSoonCtrl', ['$scope', 'UserService', 'Notification', '$state', 'AuthService',
        function ($scope, UserService, Notification, $state, AuthService) {
            var vm = this;

            $scope.user = {
                product: 'NA',
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