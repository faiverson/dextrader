angular.module('app.upgrade-modal-form', [])
    .controller('UpgradeModalFormCtrl', ['$scope', 'CheckoutService', 'products', '$uibModalInstance', 'Notification', '$state', 'InvoicesService', 'os-info', 'specialOffers',
        function ($scope, CheckoutService, products, $uibModalInstance, Notification, $state, InvoicesService, osInfo, specialOffers) {
            var vm = this;

            $scope.userData = {};
			$scope.promotionalPrice = {
				initial_payment: specialOffers.amount,
				future_payments: angular.isDefined(specialOffers.product) ? specialOffers.product.amount : specialOffers.amount
			};

			$scope.formData = {
                products: products
            };

			$scope.close = function () {
				$uibModalInstance.dismiss('close');
			};

			$scope.send = function () {

				$scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

				if ($scope.formCheckout.$valid) {

					$scope.formData.billing_address_id = $scope.address.address_id;
					$scope.formData.card_id = $scope.card.cc_id;
					if(angular.isDefined(specialOffers.offer_id)) {
						$scope.formData.offer_id = specialOffers.offer_id;
					}
					$scope.formData.info = osInfo.getOS();

					if (InvoicesService.getInvoices().length > 0  && angular.isDefined(InvoicesService.getInvoices()[0].user_id)) {
						CheckoutService.upgrade($scope.formData, InvoicesService.getInvoices()[0].user_id)
							.then(vm.success, vm.error);
					}

				} else {
					$scope.$broadcast('show-errors-check-validity');
				}

			};

            vm.setUserData = function () {
                var invoice = InvoicesService.getInvoices().length > 0 ? InvoicesService.getInvoices()[0] : {};

                $scope.userData.email = invoice.email;
                $scope.userData.username = invoice.username;
                $scope.userData.first_name = invoice.first_name;
                $scope.userData.last_name = invoice.last_name;
                $scope.userData.phone = invoice.billing_phone;
                $scope.userData.email = invoice.email;
            };

            vm.init = function () {
                vm.setUserData();
                vm.setCreditCards();
                vm.setAddress();
            };

            vm.setAddress = function () {
                $scope.addresses = [
                    {
                        address: InvoicesService.getInvoices().length > 0 ? InvoicesService.getInvoices()[0].billing_address : '',
                        address_id: InvoicesService.getInvoices().length > 0 ? InvoicesService.getInvoices()[0].billing_address_id : null
                    }
                ];
                if ($scope.addresses.length > 0) {
                    $scope.address = $scope.addresses[0];
                }
            };

            vm.setCreditCards = function () {
                $scope.cards = [
                    {
                        last_four: '**** **** **** ' + (InvoicesService.getInvoices().length > 0 ? InvoicesService.getInvoices()[0].card_last_four : '****'),
                        cc_id: InvoicesService.getInvoices().length > 0 ? InvoicesService.getInvoices()[0].card_id: null
                    }
                ];

                if ($scope.cards.length > 0) {
                    $scope.card = $scope.cards[0];
                }
            };

            vm.success = function (res) {
                InvoicesService.setInvoice(res.data);
                $uibModalInstance.close(res);
            };

            vm.error = function (response) {
				var txt = '';
				response = response.data;
				if (response.data && angular.isDefined(response.data.error)) {
					if (angular.isArray(response.error)) {
						angular.forEach(response.error, function (item) {
							txt += item + '<br>';
						});
					}
					else if (angular.isObject(response.error)) {
						angular.forEach(response.error, function (value, key) {
							if (angular.isArray(value)) {
								angular.forEach(value, function (response) {
									txt += response;
								});
							}
							else {
								txt += value;
							}
						});
					}
					else {
						txt += response.error;
					}
				} else {
					txt += 'Something went wrong! Contact with support!';
				}
				Notification.error("Oops! " + txt);
            };

            vm.init();
        }]);

