angular.module('app.checkout', ['ui.router', 'ui.mask', 'app.shared-helpers'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('checkout', {
                url: '/checkout/:enroller',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                }
            })
            .state('checkout2', {
                url: '/checkout/:enroller/:tag',
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

    .controller('CheckoutCtrl', ['$scope', 'CheckoutService', 'UserService', 'CountriesService', '$q', 'os-info', '$stateParams', 'PageService', 'Notification', 'HitsService', 'TestimonialsService', '$state', 'InvoicesService',
        function ($scope, CheckoutService, UserService, CountriesService, $q, osInfo, $stateParams, PageService, Notification, HitsService, TestimonialsService, $state, InvoicesService) {
            var vm = this;
            $scope.formData = {
                billing_address2: "",
                products: [1],
                funnel_id: 1
            };

            $scope.userData = {};

            vm.feelExpMonth = function () {
                $scope.months = [];
                var text = '';

                for (var i = 1; i <= 12; i++) {

                    text = i < 10 ? '0' + i : i;

                    $scope.months.push(text);
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
                var proms = [];
                $scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

                if ($scope.formCheckout.$valid) {
                    $scope.formData.data = vm.getUserBrowserData();

                    if (angular.isDefined($stateParams.enroller) && $stateParams.enroller.length > 0) {
                        $scope.formData.enroller = $stateParams.enroller;
                    }

                    if (angular.isDefined($stateParams.tag)) {
                        $scope.formData.tag = $stateParams.tag;
                    }

                    if (angular.isUndefined($scope.userData.username)) {
                        $scope.userData.username = $scope.formData.email;
                    }

                    if (angular.isDefined($scope.formData.user_id)) {
                        CheckoutService.send($scope.formData, $scope.token)
                            .then(vm.success, vm.error);
                    } else {
                        UserService.send($scope.userData).then(function (res) {
                            $scope.formData.user_id = res.data.user_id;
                            CheckoutService.send($scope.formData, $scope.token)
                                .then(vm.success, vm.error);

                        }, vm.error);
                    }

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
                $scope.formData.billing_country = $item.name;
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
                $scope.formData.card_name = $scope.userData.first_name + ' ' + $scope.userData.last_name;
            };

            $scope.autoCompleteBillingPhone = function () {
                $scope.formData.billing_phone = $scope.userData.phone;
            };

            $scope.selectCity = function ($item, $model, $label) {
                $scope.formData.billing_city = $item.name;
                $scope.selectedCity = $item;
                $scope.formData.billing_state = $item.district;
            };

            $scope.nextTestimonial = function () {
                var currentIndex = $scope.testimonials.indexOf($scope.selectedTestimonial);

                if (currentIndex < $scope.testimonials.length - 1) {
                    $scope.selectedTestimonial = $scope.testimonials[currentIndex + 1];
                } else {
                    $scope.selectedTestimonial = $scope.testimonials[0];
                }
            };

            $scope.prevTestimonial = function () {
                var currentIndex = $scope.testimonials.indexOf($scope.selectedTestimonial);

                if (currentIndex > 1) {
                    $scope.selectedTestimonial = $scope.testimonials[currentIndex - 1];
                } else {
                    $scope.selectedTestimonial = $scope.testimonials[$scope.testimonials.length - 1];
                }
            };

            vm.getTestimonials = function () {

                function success(res) {
                    $scope.testimonials = res.data;
                    if (res.data.length > 0) {
                        $scope.selectedTestimonial = res.data[0];
                    }
                }

                function error(err) {
                    console.log(err);
                }

                TestimonialsService.query()
                    .then(success, error);
            };

            vm.sendHit = function () {
                var data = {
                    funnel_id: 1,
                    info: vm.getUserBrowserData(),
                    product_id: 1
                };

                if (angular.isDefined($stateParams.enroller) && $stateParams.enroller.length > 0) {
                    data.enroller = $stateParams.enroller;
                }

                if (angular.isDefined($stateParams.tag)) {
                    data.tag = $stateParams.tag;
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
                var internalId = moment().unix();

                InvoicesService.setInvoice(internalId, res.data);

                $state.go('upsell', { invoice: internalId });
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
                vm.getTestimonials();

                vm.getToken()
                    .then(function () {
                        vm.sendHit();
                    });
            };

            vm.init();
        }]);
