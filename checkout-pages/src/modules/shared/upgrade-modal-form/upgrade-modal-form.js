angular.module('app.upgrade-modal-form', [])
    .controller('UpgradeModalFormCtrl', ['$scope', 'CheckoutService', 'products', '$uibModalInstance', 'Notification', '$state', 'invoiceData', 'promotionalPrice',
        function ($scope, CheckoutService, products, $uibModalInstance, Notification, $state, invoiceData, promotionalPrice) {
            var vm = this;

            $scope.userData = {};
            $scope.promotionalPrice = promotionalPrice;

            $scope.formData = {
                products: products
            };

            vm.setUserData = function () {
                var invoice = invoiceData || {};

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
                        address: invoiceData.billing_address,
                        address_id: invoiceData.billing_address_id
                    }
                ];
                if ($scope.addresses.length > 0) {
                    $scope.address = $scope.addresses[0];
                }
            };

            vm.setCreditCards = function () {
                $scope.cards = [
                    {
                        last_four: '**** **** **** ' + invoiceData.card_last_four,
                        cc_id: invoiceData.card_id
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

                    if (angular.isDefined(invoiceData.user_id)) {
                        CheckoutService.upgrade($scope.formData, invoiceData.user_id)
                            .then(vm.success, vm.error);
                    }

                } else {
                    $scope.$broadcast('show-errors-check-validity');
                }

            };

            vm.success = function (res) {
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
                    Notification.error('Ups! something went wrong! please try again!');
                }

            };

            vm.init();
        }]);

