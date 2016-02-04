angular.module('app.thankyou', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('thankyou', {
                url: '/thankyou',
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
                    AuthService.userLogin($scope.user.username, $scope.user.password)
                        .then(vm.successLogin, vm.errorLogin);
                }

            };

            $scope.totalInvoices = function (invoices) {
                var total = 0;

                angular.forEach(invoices, function (invoice) {
                    total += invoice.amount;
                });

                return total;
            };

            vm.successLogin = function (res) {
                window.location.href = $configs.DASHBOARD_URL + "/doLogin?token=" + res.data.token;
            };

            vm.errorLogin = function (err) {
                Notification.error(err.error);
            };

            vm.loadInvoice = function () {
                $scope.invoices = InvoicesService.getInvoices();
                console.log($scope.invoices);
            };

            vm.init = function () {
                vm.loadInvoice($stateParams.invoice);
            };

            vm.init();
        }]);
