angular.module('app.upgrade-modal-form', [])
    .controller('UpgradeModalFormCtrl', ['$scope', 'AuthService', 'BillingAddressService', 'CreditCardService', 'CheckoutService', 'products', '$uibModalInstance', 'Notification', '$state',
        function ($scope, AuthService, BillingAddressService, CreditCardService, CheckoutService, products, $uibModalInstance, Notification, $state) {
            var vm = this;

            $scope.userData = {};

            $scope.formData = {
                products: products
            };

            vm.setUserData = function () {
                var user = AuthService.getLoggedInUser();

                $scope.userData.email = user.email;
                $scope.userData.username = user.username;
                $scope.userData.first_name = user.first_name;
                $scope.userData.last_name = user.last_name;
                $scope.userData.phone = user.phone;
                $scope.userData.email = user.email;
            };

            vm.init = function () {
                vm.setUserData();

                BillingAddressService.query()
                    .then(vm.getAddressSuccess, vm.getAddressError);

                CreditCardService.query()
                    .then(vm.getCardsSuccess, vm.getCardsError);
            };

            vm.getAddressSuccess = function (res) {
                $scope.addresses = res.data;
                if (res.data.length > 0) {
                    $scope.address = res.data[0];
                }
            };

            vm.getAddressError = function (err) {
                Notification.error('Ups! there was an error trying to load the billing addresses!');
            };

            vm.getCardsSuccess = function (res) {

                angular.forEach(res.data, function (card) {
                    card.last_four = '**** **** **** ' + card.last_four;
                });

                $scope.cards = res.data;

                if (res.data.length > 0) {
                    $scope.card = res.data[0];
                }
            };

            vm.getCardsError = function (err) {
                Notification.error('Ups! there was an error trying to load the credit cards!');
            };

            $scope.closeAndNew = function () {
                $uibModalInstance.dismiss('close');
                $state.go('user.billing');
            };

            $scope.send = function () {
                var proms = [];
                $scope.showAgreementWarning = angular.isUndefined($scope.formData.terms);

                if ($scope.formCheckout.$valid) {

                    $scope.formData.billing_address_id = $scope.address.address_id;
                    $scope.formData.card_id = $scope.card.cc_id;

                    if (angular.isDefined(AuthService.getLoggedInUser().user_id)) {
                        CheckoutService.send($scope.formData, AuthService.getLoggedInUser().user_id)
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

