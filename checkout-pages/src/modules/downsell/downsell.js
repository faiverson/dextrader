angular.module('app.downsell', ['ui.router', 'app.upgrade-modal-form'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('downsell', {
                url: '/downsell',
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
            $scope.funnel_id = 1;

            vm.getSpecialOffer = function () {
                SpecialOffersService.query($scope.funnel_id, $scope.products, true, 'downsell')
                    .then(function (res) {
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
                //$scope.invoice_details.downsell_timer_seconds = remainSeconds;
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