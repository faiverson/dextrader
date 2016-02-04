angular.module('app.upsell', ['ui.router', 'app.upgrade-modal-form'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('upsell', {
                url: '/upsell',
                templateUrl: 'modules/upsell/upsell.tpl.html',
                controller: 'UpsellCtrl',
                data: {
                    pageTitle: 'DEX Trader - Up sell'
                }
            });
    })

    .controller('UpsellCtrl', ['$scope', '$stateParams', '$state', 'InvoicesService', '$uibModal', 'Notification', 'SpecialOffersService',
        function ($scope, $stateParams, $state, InvoicesService, $uibModal, Notification, SpecialOffersService) {
            var vm = this;
            $scope.products = [2]; //IB PRO
            $scope.funnel_id = 1;

            vm.getSpecialOffer = function () {
                SpecialOffersService.query($scope.funnel_id, $scope.products, true, 'upsell')
                    .then(function (res) {
                        console.log(res);
                        $scope.specialOffers = res;
                    });

            };

            vm.init = function () {
                vm.getSpecialOffer();
            };

            vm.init();

            $scope.timerFinish = function () {
                if (angular.isDefined(vm.upgradeModalForm)) {
                    vm.upgradeModalForm.dismiss('close');
                }
                $state.go('thankyou');
            };

            $scope.timerChange = function (remainSeconds) {
            //    $scope.invoice_details.countdown_upsell = remainSeconds;
            };

            $scope.openUpgradeForm = function () {

                vm.upgradeModalForm = $uibModal.open({
                    templateUrl: 'modules/shared/upgrade-modal-form/upgrade-modal-form.tpl.html',
                    controller: 'UpgradeModalFormCtrl',
                    size: 'lg',
                    resolve: {
                        products: function () {
                            return $scope.products;
                        },
                        invoiceData: function () {
                            return $scope.invoice_details;
                        },
                        promotionalPrice: function () {
                            var initialPayment = $scope.specialOffers[0].amount;
                            var futurePayments = angular.isDefined($scope.specialOffers[0].product) ? $scope.specialOffers[0].product.amount : $scope.specialOffers[0].amount;

                            return {
                                initial_payment: initialPayment,
                                future_payments: futurePayments
                            };
                        }
                    }
                });

                vm.upgradeModalForm.result.then(function (email) {
                    Notification.success('Upgrade complete successfully!');
                    $state.go('thankyou');
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };
        }]);
