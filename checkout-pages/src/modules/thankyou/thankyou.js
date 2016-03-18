angular.module('app.thankyou', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('thankyou', {
                url: '/thankyou',
                templateUrl: 'modules/thankyou/thankyou.tpl.html',
                controller: 'ThankyouCtrl',
                data: {
                    pageTitle: 'DEX Trader - Thank you'
                }
            });
    })

    .controller('ThankyouCtrl', ['$scope', 'AuthService', 'Notification', '$site-configs', 'InvoicesService', '$stateParams', '$filter', 'localStorageService',
        function ($scope, AuthService, Notification, $configs, InvoicesService, $stateParams, $filter, localStorageService) {
            var vm = this;

            $scope.login = function () {
				var token = localStorageService.get('user-token');
				window.location.href = $configs.DASHBOARD_URL + "/doLogin?token=" + token;
            };

            $scope.totalInvoices = function (invoices) {
                var total = 0;

                angular.forEach(invoices, function (invoice) {
                    total = (parseFloat(total) + parseFloat(invoice.amount)).toFixed(2);
                });

                return total;
            };

            $scope.getProductPrice = function (invoice, prd_id) {
                var amount = 0;

                if (angular.isDefined(invoice.offers)) {
                    var offer;

                    if(angular.isObject(invoice.offers)){
                        amount = invoice.offers[prd_id].amount;
                    } else {
                        offer = $filter('filter')(invoice.offers, {product_id: prd_id}, true);

                        if (offer.length > 0) {
                            amount = offer[0].amount;
                        }
                    }
                }else{
                    var product = $filter('filter')(invoice.products, {product_id: prd_id}, true);

                    if (product.length > 0) {
                        amount = product[0].product_amount;
                    }
                }

                return amount;
            };

            vm.loadInvoice = function () {
                $scope.invoices = InvoicesService.getInvoices();
                console.log($scope.invoices);
                $scope.order_date = moment().format('MM-DD-YYYY');
            };

            vm.init = function () {
                vm.loadInvoice($stateParams.invoice);
            };

            vm.init();
        }]);
