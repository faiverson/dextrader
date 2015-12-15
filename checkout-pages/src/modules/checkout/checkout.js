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

    .controller('CheckoutCtrl', ['$scope', 'SalesService', 'CountriesService', '$q', function ($scope, SalesService, CountriesService, $q) {
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

        $scope.getCountry = function (q) {
            var deferred = $q.defer();

            CountriesService.queryCountries(q)
                .then(function (res) {
                        deferred.resolve(res.data);
                    },
                    function (err) {
                        deferred.reject(err);
                    });

            return deferred.promise;
        };

        $scope.selectCountry = function ($item, $model, $label) {
            $scope.formData.country = $item.name;
            $scope.selectedCountry = $item;
        };

        $scope.getCity = function (q) {
            var deferred = $q.defer();
            var countryCode = $scope.selectedCountry.code;

            CountriesService.queryCities(countryCode, q)
                .then(function (res) {
                        deferred.resolve(res.data);
                    },
                    function (err) {
                        deferred.reject(err);
                    });

            return deferred.promise;
        };

        $scope.selectCity = function ($item, $model, $label) {
            $scope.formData.city = $item.name;
            $scope.selectedCity = $item;
            $scope.formData.state = $item.district;
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
