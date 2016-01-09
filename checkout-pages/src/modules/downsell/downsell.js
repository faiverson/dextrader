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

    .controller('DownsellCtrl', ['$scope', '$stateParams', '$state', 'InvoicesService', '$uibModal', 'Notification',
        function ($scope, $stateParams, $state, InvoicesService, $uibModal, Notification) {
            var vm = this;
            $scope.invoice = $stateParams.invoice;

            vm.loadInvoice = function (id) {
                $scope.invoice_details = InvoicesService.getInvoice(id);
                console.log($scope.invoice_details);
            };

            vm.init = function () {
                if (angular.isDefined($stateParams.invoice)) {
                    vm.loadInvoice($stateParams.invoice);
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
                            return {
                                initial_payment: 1,
                                future_payments: 47
                            };
                        }
                    }
                });

                vm.upgradeModalForm.result.then(function (email) {
                    Notification.success('Upgrade complete successfully!');
                }, function () {
                    //$log.info('Modal dismissed at: ' + new Date());
                });
            };
        }]);