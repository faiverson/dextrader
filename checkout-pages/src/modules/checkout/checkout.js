angular.module('app.checkout', ['ui.router', 'ui.mask', 'app.shared-helpers'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('checkout', {
                url: '/ib?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [1], name: 'ib', funnel_id: 1, showRecurrentPayment: true};
                    }
                }
            })
            .state('ckdownsell', {
                url: '/downsell/ib?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [1], name: 'ib', funnel_id: 1, showRecurrentPayment: true, type: 'downsell'};
                    }
                }
            })
            .state('ckfreeib', {
                url: '/ib/free?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [1, 2], name: 'ib', funnel_id: 1, showRecurrentPayment: true, type: 'free-30-days'};
                    }
                }
            })
            .state('checkoutNA', {
                url: '/na?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [3], name: 'na', funnel_id: 2, showRecurrentPayment: true};
                    }
                }
            })
            .state('checkoutFX', {
                url: '/fx?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [4], name: 'fx', funnel_id: 2, showRecurrentPayment: true};
                    }
                }
            })
            .state('checkoutACADEMY', {
                url: '/academy?user&tag',
                templateUrl: 'modules/checkout/checkout.tpl.html',
                controller: 'CheckoutCtrl',
                data: {
                    pageTitle: 'Checkout Page'
                },
                resolve: {
                    product: function () {
                        return {id: [5], name: 'academy', funnel_id: 2, showRecurrentPayment: false};
                    }
                }
            });

    })

    .controller('CheckoutCtrl', ['$scope', 'CheckoutService', 'UserService', 'CountriesService', '$q', 'os-info', '$stateParams', 'Notification', 'HitsService', 'TestimonialsService', '$state', 'InvoicesService', 'SpecialOffersService', 'product', 'UserSettings',
        function ($scope, CheckoutService, UserService, CountriesService, $q, osInfo, $stateParams, Notification, HitsService, TestimonialsService, $state, InvoicesService, SpecialOffersService, product, UserSettings) {
            var vm = this;

            $scope.product = product;
            $scope.formData = {
                billing_address2: "",
                products: product.id,
                funnel_id: product.funnel_id
            };

            $scope.productsData = [
                {name: '', description: ''},
                {
                    name: 'International Binary Options',
                    description: 'Getting Binary Option Signals From Dex Signals Helps Both The Novice And Experienced Binary Option Traders Stop Guessing And Start Winning!'
                },
                {name: 'International Binary Options PRO', description: ''},
                {name: 'North American Derivatives', description: ''},
                {name: 'Forex Options Newsletter', description: ''},
                {name: 'Dex Trading Academy', description: ''}
            ];

            $scope.userData = {};

			$scope.model = {};

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
                var offers_id = [];
                $scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

                if ($scope.formCheckout.$valid) {
                    $scope.working = true;
                    $scope.formData.data = vm.getUserBrowserData();

                    if (angular.isDefined($stateParams.user) && $stateParams.user.length > 0) {
                        $scope.formData.enroller = $stateParams.user;
                        $scope.userData.enroller = $stateParams.user;
                    }

                    if (angular.isDefined($stateParams.tag)) {
                        $scope.formData.tag = $stateParams.tag;
                    }

                    if (angular.isUndefined($scope.userData.username)) {
                        $scope.userData.username = $scope.formData.email;
                    }


                    if($scope.products.length > 0){
                        angular.forEach($scope.products, function (prd) {
                            if(angular.isDefined(prd.offer_id)){
                                offers_id.push(prd.offer_id);
                            }
                        });

                        if(offers_id.length > 0){
                            $scope.formData.offer_id = offers_id;
                        }
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

            $scope.total = function () {
                var sum = 0;
                angular.forEach($scope.products, function (prd) {
                    sum += parseFloat(angular.isDefined(prd.product) ? prd.product.amount : prd.amount, 2);
                });

                return sum;
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
                    funnel_id: product.funnel_id,
                    info: vm.getUserBrowserData(),
                    product_id: product.id
                };

                if (angular.isDefined($stateParams.user) && $stateParams.user.length > 0) {
                    data.enroller = $stateParams.user;
                }

                if (angular.isDefined($stateParams.tag)) {
                    data.tag = $stateParams.tag;
                }

                HitsService.send(data, $scope.token);
            };

            vm.success = function (res) {
                var invoice_details = res.data,
					token = res.data.token;
                $scope.working = false;
				if(invoice_details.hasOwnProperty('token')) {
					delete invoice_details['token'];
				}
                InvoicesService.setInvoice(invoice_details);

                //forcing the user to go to thankyou page for IB and IB PRO for free
                if(product.id.length > 1){
                    $state.go('thankyou');
                }else{
                    $state.go('upsell');
                }

                Notification.success('Congratulations!!! Your account has been created!');
            };

            vm.error = function (err) {
                $scope.working = false;

                if (err.data && angular.isDefined(err.data.error)) {
                    if (angular.isArray(err.data.error)) {
                        angular.forEach(err.data.error, function (e) {
                            Notification.error(e);
                        });
                    }
					else if(angular.isObject( err.data.error )) {
						angular.forEach(err.data.error, function (value, key) {
							if (angular.isArray(value)) {
								angular.forEach(value, function (response) {
									Notification.error(response);
								});
							}
							else {
								Notification.error(value);
							}
						});
					}
					else {
                        Notification.error(err.data.error);
                    }
                } else {
                    Notification.error('Oops! something went wrong! Contact with support!');
                }
            };

            vm.getUserBrowserData = function () {
                return osInfo.getOS();
            };

            vm.getSpecialOffer = function () {
                var checkForOffers = $state.includes('ckdownsell') || $state.includes('ckfreeib');
                SpecialOffersService.query($scope.formData.funnel_id, $scope.formData.products, checkForOffers, product.type)
                    .then(function (res) {
                        $scope.products = res;
                        console.log(res);
                    });

            };

            vm.init = function () {
                $scope.nextPaymentDate = moment().add(3, 'd').format('MM/DD/YYYY');

                if (angular.isUndefined($state.params.user) && !angular.isUndefined(UserSettings.userEnroller())) {
                    $state.go($state.$current.name, {user: UserSettings.userEnroller()});
                } else if (!angular.isUndefined($state.params.user)) {
                    UserSettings.setEnroller($state.params.user);
                }


                vm.feelExpMonth();
                vm.feelExpYear();
                vm.getTestimonials();
                vm.getSpecialOffer();
                vm.sendHit();
            };

            vm.init();
        }]);
