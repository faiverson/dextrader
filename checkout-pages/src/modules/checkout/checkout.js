angular.module('app.checkout', ['ui.router', 'ui.mask', 'app.shared-helpers'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('checkout', {
                url: '/checkout/:enroller?/:tag?',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                }
            })
            .state('checkout_root', {
                url: '/checkout',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                }
            });
    })

    .controller('CheckoutCtrl', ['$scope', 'SalesService', 'CountriesService', '$q', 'os-info', '$stateParams', 'PageService', 'Notification', 'HitsService',
        function ($scope, SalesService, CountriesService, $q, osInfo, $stateParams, PageService, Notification, HitsService) {
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
                    $scope.formData.data = vm.getUserBrowserData();

                    $scope.formData.enroller = $stateParams.enroller;

                    if (angular.isUndefined($scope.formData.username)) {
                        $scope.formData.username = $scope.formData.email;
                    }

                    SalesService.send($scope.formData, $scope.token)
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

            $scope.autoCompleteBillingName = function () {
                $scope.formData.card_name = $scope.formData.first_name + ' ' + $scope.formData.last_name;
            };

            $scope.autoCompleteBillingPhone = function () {
                $scope.formData.billing_phone = $scope.formData.phone;
            };

            $scope.selectCity = function ($item, $model, $label) {
                $scope.formData.city = $item.name;
                $scope.selectedCity = $item;
                $scope.formData.state = $item.district;
            };

            vm.sendHit = function () {
                var data = {
                    funnel_id: 2,
                    info: vm.getUserBrowserData(),
                    product_id: 1
                };

                if (angular.isDefined($stateParams.enroller) && $stateParams.enroller.length > 0) {
                    data.enroller_id = $stateParams.enroller;
                }

                if (angular.isDefined($stateParams.tag)) {
                    data.tag_id = $stateParams.tag;
                }

                HitsService.send(data, $scope.token);
            };

            vm.getToken = function () {
                var promise = PageService.getToken();
                promise.then(function (res) {
                    $scope.token = res.data.token;
                });
                return promise;
            };

            vm.success = function (res) {
                //TODO see where we should redirect the user
                Notification.success('Congratulations!!! Account has been created!');
            };

            vm.error = function (err) {
                if (angular.isDefined(err.data.error)) {
                    if (angular.isArray(err.data.error)) {
                        angular.forEach(err.data.error, function (e) {
                            Notification.error(e);
                        });
                    } else {
                        Notification.error(err.data.error);
                    }

                } else {
                    Notification.error('Ups! something went wrong! please try again!');
                }

            };

            vm.getUserBrowserData = function () {
                return osInfo.getOS();
            };

            vm.init = function () {
                vm.feelExpMonth();
                vm.feelExpYear();

                vm.getToken()
                    .then(function () {
                        vm.sendHit();
                    });
            };

            vm.init();
        }]);
