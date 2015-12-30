angular.module('app.thankyou', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('thankyou', {
                url: '/thankyou/:invoice',
                templateUrl: 'modules/thankyou/thankyou.tpl.html',
                controller: 'ThankyouCtrl',
                data: {
                    pageTitle: 'DEX Trader - Thank you'
                }
            });
    })

    .controller('ThankyouCtrl', ['$scope', 'AuthService', 'Notification', '$site-configs', 'InvoicesService', '$stateParams',
        function ($scope, AuthService, Notification, $configs, InvoicesService, $stateParams) {
            var vm = this;

            $scope.login = function () {

                $scope.$broadcast('show-errors-check-validity');

                if ($scope.loginFormDetails.$valid) {
                    AuthService.login($scope.user.username, $scope.user.password)
                        .then(vm.successLogin, vm.errorLogin);
                }

            };

            vm.successLogin = function (res) {
                window.location.href = $configs.DASHBOARD_URL + "/doLogin?token=" + res.data.token;
            };

            vm.errorLogin = function (err) {
                Notification.error(err.error);
            };

            vm.loadInvoice = function (id) {
                $scope.invoice = InvoicesService.getInvoice(id);
                console.log($scope.invoice);
            };

            vm.init = function () {
                if (angular.isDefined($stateParams.invoice)) {
                    vm.loadInvoice($stateParams.invoice);
                }
            };

            vm.init();
        }]);
