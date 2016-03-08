angular.module('app.upgrade-modal-form', [])
    .controller('UpgradeModalFormCtrl', ['$scope', 'CheckoutService', 'products', '$uibModalInstance', 'Notification', '$state', 'InvoicesService', 'promotionalPrice',
        function ($scope, CheckoutService, products, $uibModalInstance, Notification, $state, InvoicesService, promotionalPrice) {
            var vm = this;

            $scope.userData = {};
            $scope.promotionalPrice = promotionalPrice;

            $scope.formData = {
                products: products
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

            $scope.send = function () {

                $scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

                if ($scope.formCheckout.$valid) {

                    $scope.formData.billing_address_id = $scope.address.address_id;
                    $scope.formData.card_id = $scope.card.cc_id;

                    if (InvoicesService.getInvoices().length > 0  && angular.isDefined(InvoicesService.getInvoices()[0].user_id)) {
                        CheckoutService.upgrade($scope.formData, InvoicesService.getInvoices()[0].user_id)
                            .then(vm.success, vm.error);
                    }

                } else {
                    $scope.$broadcast('show-errors-check-validity');
                }

            };

            vm.success = function (res) {
                InvoicesService.setInvoice(res.data);
                $uibModalInstance.close(res);
            };

            vm.error = function (err) {
                if (err.data && angular.isDefined(err.data.error)) {
                    if (angular.isArray(err.data.error)) {
                        angular.forEach(err.data.error, function (e) {
                            Notification.error(e);
                        });
                    } else {
                        Notification.error(err.data.error);
                    }

                } else {
                    Notification.error('Oops! something went wrong! Contact with support!');
                }

            };

            vm.init();
        }]);

