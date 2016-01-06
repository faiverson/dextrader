angular.module('app.user-profile', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('user_profile', {
                url: '/user-profile/:id',
                templateUrl: 'modules/user-profiles/user-profile.tpl.html',
                controller: 'UserProfileController',
                data: {
                    pageTitle: 'User profile'
                }
            });
    })
    .controller('UserProfileController', ['$scope', '$state', '$stateParams', 'UserService', 'BillingAddressService', 'CreditCardService', 'Notification', 'InvoiceService',
        function ($scope, $state, $stateParams, UserService, BillingAddressService, CreditCardService, Notification, InvoiceService) {
            var vm = this;

            vm.loadUser = function (id) {
                var prom = UserService.getUser(id);

                function success(res) {
                    $scope.user = res.data;
                    console.log(res.data);
                }

                function error(err) {
                    Notification.error(err.data.error);
                    $state.go('users');
                }

                prom.then(success, error);

                return prom;
            };

            vm.loadUserBillingAddresses = function (user_id) {
                var prom = BillingAddressService.query(user_id);

                function success(res) {
                    $scope.billing_addresses = res.data;
                    console.log(res.data);
                }

                function error(err) {
                    Notification.error(err.data.error);
                }

                prom.then(success, error);

                return prom;
            };

            $scope.loadUserCreditCards = function (user_id) {
                var prom = CreditCardService.query(user_id);

                function success(res) {
                    $scope.credit_cards = res.data;
                    console.log(res.data);
                }

                function error(err) {
                    Notification.error(err.data.error);
                }

                prom.then(success, error);

                return prom;
            };

            $scope.loadUserInvoices = function (user_id) {
                var prom = InvoiceService.query(user_id);

                function success(res) {
                    $scope.invoices = res.data;
                    console.log(res.data);
                }

                function error(err) {
                    Notification.error(err.data.error);
                }

                prom.then(success, error);

                return prom;
            };

            vm.init = function () {
                $scope.user = {};

                if (angular.isDefined($stateParams.id)) {
                    vm.loadUser($stateParams.id);
                    vm.loadUserBillingAddresses($stateParams.id);
                } else {
                    $state.go('users');
                }
            };

            vm.init();

        }]);
