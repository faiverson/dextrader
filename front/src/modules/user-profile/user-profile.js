angular.module('app.user-profile', ['ui.router', 'ui.select', 'ngSanitize', 'ui.mask', 'angularMoment', 'ngFileSaver'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('user', {
                url: '/profile',
                templateUrl: 'modules/user-profile/user.profile.tpl.html',
                controller: 'UserProfileCtrl',
                data: {
                    pageTitle: 'User Profile'
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
            .state('user.billing', {
                url: '/billing',
                templateUrl: 'modules/user-profile/user.profile.billing.tpl.html',
                controller: 'BillingCtrl',
                data: {
                    pageTitle: 'User Settings'
                }
            });
    })

    .controller('UserProfileCtrl', ['$scope', 'ChatService', function ($scope, ChatService) {
        $scope.loadChat = function () {
            ChatService.show();
        };
    }])

    .controller('UserProfileSettingsCtrl', ['$scope', 'UserService', 'AuthService', 'Notification', function ($scope, UserService, AuthService, Notification) {
        var vm = this;

        $scope.save = function () {
            var prom;
            if ($scope.userForm.$valid) {

                if (angular.isUndefined($scope.user.password) || $scope.user.password.length === 0) {
                    delete $scope.user.password;
                    delete $scope.user.confirm_password;
                }

                prom = UserService.saveUser($scope.user);
                prom.then(vm.successSaveUser, vm.errorSaveUser);

            } else {
                $scope.$broadcast('show-errors-check-validity');
            }

            return prom;
        };

        vm.successSaveUser = function success(res) {
            Notification.success("User settings changed successfully!");
        };

        vm.errorSaveUser = function error(response) {
            var txt = '';
            response = response.data;
            if (angular.isArray(response.error)) {
                angular.forEach(response.error, function (item) {
                    txt += item + '<br>';
                });
            }
            else {
                txt += response.error;
            }
            Notification.error("Oops! " + txt);
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
    }])

    .controller('BillingCtrl', ['$scope', '$window', 'CreditCardService', 'BillingAddressService', 'Notification', '$uibModal', 'SubscriptionService', 'InvoiceService', 'FileSaver',
        function ($scope, $window, CreditCardService, BillingAddressService, Notification, $uibModal, SubscriptionService, InvoiceService, FileSaver) {
            var vm = this;
            $scope.creditCards = [];
            $scope.addresses = [];
            $scope.invoices = [];
            $scope.subscriptions = [];

            $scope.openFormCreditCard = function (cc_id) {

                var modalInstance = $uibModal.open({
                    templateUrl: 'modules/user-profile/user.profile.credit-card-form.tpl.html',
                    controller: 'CreditCardFormCtrl',
                    resolve: {
                        cc_id: function () {
                            return cc_id;
                        }
                    }
                });

                modalInstance.result.then(function (email) {
                    vm.getUserCreditCards();
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };

            $scope.openFormBillingAddress = function (address_id) {

                var modalInstance = $uibModal.open({
                    templateUrl: 'modules/user-profile/user.profile.billing-address-form.tpl.html',
                    controller: 'BillingAddressFormCtrl',
                    resolve: {
                        address_id: function () {
                            return address_id;
                        }
                    }
                });

                modalInstance.result.then(function (email) {
                    vm.getUserBillingAddress();
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };

            $scope.openSubscriptionForm = function (subscription) {
                var modalInstance = $uibModal.open({
                    templateUrl: 'modules/user-profile/user.profile.subscription-form.tpl.html',
                    controller: 'SubscriptionFormCtrl',
                    resolve: {
                        subscription: function () {
                            return subscription;
                        }
                    }
                });

                modalInstance.result
                    .then(function (email) {
                        vm.getSubscriptions();
                    }, function () {
                        //$log.info('Modal dismissed at: ' + new Date());
                    });
            };

			$scope.getPdf = function (invoice) {
				var filename = 'invoice-' + invoice.id + '.pdf';
				InvoiceService.download(invoice.id)
					.then(function (res) {
						var urlCreator = window.URL || window.webkitURL || window.mozURL || window.msURL;
						var link = document.createElement("a");
						var blob = new Blob([res], {type: "application/pdf"});
						var url = urlCreator.createObjectURL(blob);

						link.setAttribute("href", url);
						link.setAttribute("download", filename);

						// Simulate clicking the download link
						var event = document.createEvent('MouseEvents');
						event.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
						link.dispatchEvent(event);
						//FileSaver.saveAs(blob, 'test.pdf');
					}, function (err) {});
			};

            vm.getSubscriptions = function () {
                SubscriptionService.query()
                    .then(vm.getSubscriptionsSuccess, vm.getSubscriptionsError);
            };

            vm.getSubscriptionsSuccess = function (res) {
                $scope.subscriptions = res.data;
            };

            vm.getSubscriptionsError = function (err) {

            };

            vm.getInvoices = function () {
                InvoiceService.query()
                    .then(vm.getInvoicesSuccess, vm.getInvoicesError);
            };

            vm.getInvoicesSuccess = function (res) {
                $scope.invoices = res.data;
            };

            vm.getInvoicesError = function (err) {

            };

            vm.getUserCreditCards = function () {

                function success(res) {
                    $scope.creditCards = res.data;
                }

                function error(err) {
                    Notification.error('Oops! there was an error trying to load credit cards');
                }

                CreditCardService.query()
                    .then(success, error);
            };

            vm.getUserBillingAddress = function () {

                function success(res) {
                    $scope.addresses = res.data;
                }

                function error(err) {
                    Notification.error('Oops! there was an error trying to load billing addresses');
                }

                BillingAddressService.query()
                    .then(success, error);
            };

            vm.init = function () {
                vm.getUserCreditCards();
                vm.getUserBillingAddress();
                vm.getSubscriptions();
                vm.getInvoices();
            };

            vm.init();

        }])

    .controller('CreditCardFormCtrl', ['$scope', '$uibModalInstance', 'Notification', 'cc_id', 'CreditCardService', '$filter',
        function ($scope, $uibModalInstance, Notification, cc_id, CreditCardService, $filter) {
            var vm = this;
            $scope.card = {};
            $scope.networks = ['mastercard', 'visa', 'amex', 'dinersclub', 'discover', 'jcb'];

            vm.init = function () {
                if (angular.isDefined(cc_id)) {
                    CreditCardService.getOne(cc_id)
                        .then(vm.getCardSuccess, vm.getCardError);
                }

                vm.feelExpMonth();
                vm.feelExpYear();
            };

            vm.getCardSuccess = function (res) {
                $scope.card = res.data;

                $scope.exp_month = $filter('filter')($scope.months, {id: parseInt($scope.card.exp_month, 10)}, true)[0];
                $scope.exp_year = $filter('filter')($scope.years, {id: parseInt($scope.card.exp_year)}, true)[0];
            };

            vm.getCardError = function (err) {
                Notification.error('Oops! there was an error trying to load the card info!');
            };

            vm.feelExpMonth = function () {
                var date = moment([moment().year(), 0]);
                $scope.months = [];

                for (var i = 1; i <= 12; i++) {
                    var month = {
                        id: i,
                        name: date.format('M (MMM)')
                    };

                    $scope.months.push(month);

                    date.add(1, 'M');
                }
            };

            vm.feelExpYear = function () {
                var ini = parseInt(moment().format('YY'), 10);
                $scope.years = [];

                for (var i = ini; i <= (ini + 14); i++) {
                    var year = {
                        id: i,
                        name: i
                    };

                    $scope.years.push(year);
                }
            };

            vm.saveSuccess = function (res) {
                Notification.success('Credit Card saved succssfully!');
                $uibModalInstance.close(res.data);
            };

            vm.saveError = function (err) {
                Notification.error(err.data.error);
            };

            $scope.selectCCType = function (network) {
                if (!angular.isDefined(cc_id)) {
                    $scope.card.network = network;
                }
            };

            $scope.close = function () {
                $uibModalInstance.dismiss('close');
            };

            $scope.save = function () {
                //$scope.$broadcast('show-errors-check-validity');
                var prom;
                if ($scope.ccForm.$valid) {
                    $scope.card.month = $scope.exp_month.id;
                    $scope.card.year = $scope.exp_year.id;

                    prom = CreditCardService.save($scope.card);
                    prom.then(vm.saveSuccess, vm.saveError);
                }

                return prom;
            };

            vm.init();
        }])

    .controller('BillingAddressFormCtrl', ['$scope', '$uibModalInstance', 'Notification', 'address_id', 'BillingAddressService', '$filter',
        function ($scope, $uibModalInstance, Notification, address_id, BillingAddressService, $filter) {
            var vm = this;
            $scope.address = {};

            vm.init = function () {
                if (angular.isDefined(address_id)) {
                    BillingAddressService.getOne(address_id)
                        .then(vm.getAddressSuccess, vm.getAddressError);
                }
            };

            vm.getAddressSuccess = function (res) {
                $scope.address = res.data;
                $scope.address.phone = parseInt($scope.address.phone, 10);

            };

            vm.getAddressError = function (err) {
                Notification.error('Oops! there was an error trying to load the address info!');
            };

            vm.saveSuccess = function (res) {
                Notification.success('Address saved successfully!');
                $uibModalInstance.close(res.data);
            };

            vm.saveError = function (err) {
                Notification.error(err.data.error);
            };

            $scope.close = function () {
                $uibModalInstance.dismiss('close');
            };

            $scope.save = function () {
                var prom;
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.addressForm.$valid) {

                    prom = BillingAddressService.save($scope.address);
                    prom.then(vm.saveSuccess, vm.saveError);
                }

                return prom;
            };

            vm.init();
        }])

    .controller('SubscriptionFormCtrl', ['$scope', '$uibModalInstance', 'Notification', 'subscription', 'BillingAddressService', '$filter', 'CreditCardService', 'SubscriptionService',
        function ($scope, $uibModalInstance, Notification, subscription, BillingAddressService, $filter, CreditCardService, SubscriptionService) {
            var vm = this;
            $scope.subscription = subscription;

            vm.init = function () {
                BillingAddressService.query()
                    .then(vm.getAddressSuccess, vm.getAddressError);

                CreditCardService.query()
                    .then(vm.getCardsSuccess, vm.getCardsError);
            };

            vm.getAddressSuccess = function (res) {

                angular.forEach(res.data, function (address) {
                    if (address.address_id === subscription.address.address_id) {
                        $scope.address = address;
                    }
                });

                $scope.addresses = res.data;

            };

            vm.getAddressError = function (err) {
                Notification.error('Oops! there was an error trying to load the billing addresses!');
            };

            vm.getCardsSuccess = function (res) {

                angular.forEach(res.data, function (card) {
                    card.last_four = '**** **** **** ' + card.last_four;
                    if (card.cc_id === subscription.card.cc_id) {
                        $scope.card = card;
                    }
                });

                $scope.cards = res.data;
            };

            vm.getCardsError = function (err) {
                Notification.error('Oops! there was an error trying to load the credit cards!');
            };

            vm.saveSuccess = function (res) {
                Notification.success('Address saved successfully!');
                $uibModalInstance.close(res.data);
            };

            vm.saveError = function (err) {
                Notification.error(err.data.error);
            };

            $scope.close = function () {
                $uibModalInstance.dismiss('close');
            };

            $scope.save = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.subscriptionForm.$valid) {

                    var data = {
                        subscription_id: $scope.subscription.subscription_id,
                        card_id: $scope.card.cc_id,
                        billing_address_id: $scope.address.address_id
                    };

                    SubscriptionService.save(data)
                        .then(vm.saveSuccess, vm.saveError);
                }
            };

            $scope.checkStatus = function () {
                var data = {
                    subscription_id: $scope.subscription.subscription_id
                };

                if ($scope.isActive()) {
                    SubscriptionService.cancel(data).then(function (response) {
                        Notification.success('Your subscription has been canceled');
                        $uibModalInstance.close(response.data);
                    }, vm.saveError);
                }
                else {
                    SubscriptionService.reactive(data).then(function (response) {
                        Notification.success('Your subscription has been reactivated');
                        $uibModalInstance.close(response.data);
                    }, vm.saveError);
                }
            };

            $scope.isActive = function () {
                return $scope.subscription.status === 'active';
            };


            vm.init();
        }]);
