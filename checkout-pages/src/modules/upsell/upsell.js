angular.module('app.upsell', ['ui.router', 'app.upgrade-modal-form'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('upsell', {
                url: '/upsell/:invoice',
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
            $scope.invoice = $stateParams.invoice;
            $scope.products = [2]; //IB PRO

            vm.loadInvoice = function (id) {
                $scope.invoice_details = InvoicesService.getInvoice(id);
            };

            vm.getSpecialOffer = function () {
                SpecialOffersService.query($scope.invoice_details.funnel_id, $scope.products, true)
                    .then(function (res) {
                        $scope.specialOffers = res;
                    });

            };

            vm.init = function () {
                if (angular.isDefined($stateParams.invoice)) {
                    vm.loadInvoice($stateParams.invoice);
                    vm.getSpecialOffer();
                }
            };

            vm.init();

            $scope.timerFinish = function () {
                if (angular.isDefined(vm.upgradeModalForm)) {
                    vm.upgradeModalForm.dismiss('close');
                }
                $state.go('thankyou', {invoice: $stateParams.invoice});
            };

            $scope.timerChange = function (remainSeconds) {
                $scope.invoice_details.upsell_timer_seconds = remainSeconds;
                InvoicesService.setInvoice($stateParams.invoice, $scope.invoice_details);
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
                    $state.go('thankyou', {invoice: $stateParams.invoice});
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };
        }]);
