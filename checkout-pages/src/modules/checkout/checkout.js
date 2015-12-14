angular.module('app.checkout', ['ui.router', 'ui.mask'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('checkout', {
                url: '/checkout',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                }
            });
    })

    .controller('CheckoutCtrl', ['$scope', 'SalesService', function ($scope, SalesService) {
        var vm = this;
        $scope.formData = {};

        vm.feelExpMonth = function () {
            $scope.months = [];

            for (var i = 1; i <= 12; i++) {

                $scope.months.push(i);
            }
        };

        vm.feelExpYear = function () {
            var ini = parseInt(moment().format('YY'), 10);
            $scope.years = [];

            for (var i = ini; i <= (ini + 14); i++) {
                $scope.years.push(i);
            }
        };

        $scope.send = function () {

            $scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

            if ($scope.formCheckout.$valid) {
                vm.getUserBrowserData();

                SalesService.send($scope.formData)
                    .then(vm.success, vm.error);

            } else {
                $scope.$broadcast('show-errors-check-validity');
            }

        };

        vm.success = function (res) {

        };

        vm.error = function (err) {

        };

        vm.getUserBrowserData = function () {
            $scope.formData.data = {
                'userAgent': navigator.userAgent,
                'appVersion': navigator.appVersion,
                'platform': navigator.platform
            };
        };

        vm.init = function () {
            vm.feelExpMonth();
            vm.feelExpYear();
        };

        vm.init();
    }]);
