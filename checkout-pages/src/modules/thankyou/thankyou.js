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

    .controller('ThankyouCtrl', ['$scope', 'AuthService', 'Notification', '$site-configs', 'InvoicesService', '$stateParams', '$filter',
        function ($scope, AuthService, Notification, $configs, InvoicesService, $stateParams, $filter) {
            var vm = this;

            $scope.login = function () {

                $scope.$broadcast('show-errors-check-validity');

                if ($scope.loginFormDetails.$valid) {
                    AuthService.userLogin($scope.user.username, $scope.user.password)
                        .then(vm.successLogin, vm.errorLogin);
                }

            };

            $scope.totalInvoices = function (invoices) {
                var total = 0;

                angular.forEach(invoices, function (invoice) {
                    total += invoice.amount;
                });

                return total;
            };

            $scope.getProductPrice = function (invoice, prd_id) {
                var amount = 0;

                if (angular.isDefined(invoice.offers)) {
                    var offer;

                    if(angular.isObject(invoice.offers)){
                        amount = invoice.offers[1].amount;
                    }else {

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

            vm.successLogin = function (res) {
                window.location.href = $configs.DASHBOARD_URL + "/doLogin?token=" + res.data.token;
            };

            vm.errorLogin = function (err) {
                Notification.error(err.error);
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
