angular.module('app.downsell', ['ui.router', 'app.upgrade-modal-form'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('downsell', {
                url: '/downsell/:invoice',
                templateUrl: 'modules/downsell/downsell.tpl.html',
                controller: 'DownsellCtrl',
                data: {
                    pageTitle: 'DEX Trader - Down sell'
                }
            });
    })

    .controller('DownsellCtrl', ['$scope', '$stateParams', '$state', 'InvoicesService', '$uibModal', 'Notification', 'SpecialOffersService',
        function ($scope, $stateParams, $state, InvoicesService, $uibModal, Notification, SpecialOffersService) {
            var vm = this;
            $scope.invoice = $stateParams.invoice;
            $scope.products = [2]; //IB PRO

            vm.loadInvoice = function (id) {
                $scope.invoice_details = InvoicesService.getInvoice(id);
                console.log($scope.invoice_details);
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
                if(angular.isDefined(vm.upgradeModalForm)){
                    vm.upgradeModalForm.dismiss('close');
                }

                $state.go('thankyou', {invoice: $stateParams.invoice});
            };

            $scope.timerChange = function (remainSeconds) {
                $scope.invoice_details.downsell_timer_seconds = remainSeconds;
                InvoicesService.setInvoice($stateParams.invoice, $scope.invoice_details);
            };

            $scope.openUpgradeForm = function () {

                vm.upgradeModalForm = $uibModal.open({
                    templateUrl: 'modules/shared/upgrade-modal-form/upgrade-modal-form.tpl.html',
                    controller: 'UpgradeModalFormCtrl',
                    size: 'lg',
                    resolve: {
                        products: function () {
                            return [2];
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