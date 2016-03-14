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
    .controller('UserProfileController', ['$scope', '$site-configs', '$state', '$stateParams', 'UserService',
        'BillingAddressService', 'CreditCardService', 'Notification', 'InvoiceService', 'SubscriptionService',
        'CommissionService', 'PaymentService', 'MarketingStatsService', 'TransactionService', '$uibModal', 'modalService',
        function ($scope, $configs, $state, $stateParams, UserService, BillingAddressService, CreditCardService,
                  Notification, InvoiceService, SubscriptionService, CommissionService, PaymentService,
                  MarketingStatsService, TransactionService, $uibModal, modalService) {
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

            $scope.commissions = {
                filters: {
                    from: {
                        format: 'dd MMM yyyy'
                    },
                    to: {
                        format: 'dd MMM yyyy'
                    },
                    products: [
                        {id: 1, name: 'IB'},
                        {id: 2, name: 'IB PRO'}
                    ],
                    status: [
                        'Pending', 'Ready to Pay', 'Paid'
                    ],
                    apply: function () {
                        //TODO call api
                    }
                },
                sortBy: {
                    column: 'created_at',
                    dir: 'desc',
                    sort: function (col) {
                        if (col === this.column) {
                            this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.column = col;
                            this.dir = 'asc';
                        }

                        this.loadUserCommissions($scope.user.user_id);
                    }
                },
                pagination: {
                    totalItems: 20,
                    currentPage: 1,
                    itemsPerPage: 10,
                    pageChange: function () {
                        this.loadUserCommissions($scope.user.user_id);
                    }
                },
                loadUserCommissions: function (user_id) {

                    var order = [];
                    order[this.sortBy.column] = this.sortBy.dir;

                    var params = {
                        offset: (this.pagination.currentPage - 1) * this.pagination.itemsPerPage,
                        limit: this.pagination.itemsPerPage,
                        order: order
                    };

                    var prom = CommissionService.getCommissions(params, user_id);

                    function success(res) {
                        $scope.commissions.pagination.totalItems = res.data.total;
                        $scope.commissions.data = res.data.commissions;
                    }

                    function error(err) {
                        Notification.error(err.data.error);
                    }

                    prom.then(success, error);

                    return prom;
                }
            };

            $scope.payments = {
                filters: {
                    from: {
                        format: 'dd MMM yyyy'
                    },
                    to: {
                        format: 'dd MMM yyyy'
                    },
                    products: [
                        {id: 1, name: 'IB'},
                        {id: 2, name: 'IB PRO'}
                    ],
                    status: [
                        'Pending', 'Ready to Pay', 'Paid'
                    ],
                    apply: function () {
                        //TODO call api
                    }
                },
                sortBy: {
                    column: 'created_at',
                    dir: 'desc',
                    sort: function (col) {
                        if (col === this.column) {
                            this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.column = col;
                            this.dir = 'asc';
                        }

                        this.loadUserCommissions($scope.user.user_id);
                    }
                },
                pagination: {
                    totalItems: 20,
                    currentPage: 1,
                    itemsPerPage: 10,
                    pageChange: function () {
                        this.loadUserPayments($scope.user.user_id);
                    }
                },
                loadUserPayments: function (user_id) {

                    var order = [];
                    order[this.sortBy.column] = this.sortBy.dir;

                    var params = {
                        offset: (this.pagination.currentPage - 1) * this.pagination.itemsPerPage,
                        limit: this.pagination.itemsPerPage,
                        order: order
                    };

                    var prom = PaymentService.getPayments(params, user_id);

                    function success(res) {
                        $scope.payments.pagination.totalItems = res.data.total;
                        $scope.payments.data = res.data.payments;
                    }

                    function error(err) {
                        Notification.error(err.data.error);
                    }

                    prom.then(success, error);

                    return prom;
                }
            };

            $scope.transactions = {
                filters: {
                    from: {
                        format: 'dd MMM yyyy'
                    },
                    to: {
                        format: 'dd MMM yyyy'
                    },
                    apply: function () {
                        //TODO call api
                    }
                },
                sortBy: {},
                pagination: {
                    totalItems: 20,
                    currentPage: 1,
                    itemsPerPage: 10,
                    pageChange: function () {
                        this.loadUserTransactions($scope.user.user_id);
                    }
                },
                loadUserTransactions: function (user_id) {

                    var params = {
                        offset: (this.pagination.currentPage - 1) * this.pagination.itemsPerPage,
                        limit: this.pagination.itemsPerPage,
                        order: this.sortBy
                    };

                    var prom = TransactionService.query(user_id, params);

                    function success(res) {
                        $scope.transactions.pagination.totalItems = res.data.total;
                        $scope.transactions.data = res.data.transactions;
                    }

                    function error(err) {
                        Notification.error(err.data.error);
                    }

                    prom.then(success, error);

                    return prom;
                }
            };

            $scope.stats = {
                filters: {
                    from: {
                        format: 'dd MMM yyyy'
                    },
                    to: {
                        format: 'dd MMM yyyy'
                    },
                    apply: function () {
                        $scope.stats.loadUserStats($scope.user.user_id);
                    }
                },
                sortBy: {},
                pagination: {
                    totalItems: 20,
                    currentPage: 1,
                    itemsPerPage: 10,
                    pageChange: function () {
                        $scope.stats.loadUserStats($scope.user.user_id);
                    }
                },
                loadUserStats: function (user_id) {

                    var params = {
                        offset: (this.pagination.currentPage - 1) * this.pagination.itemsPerPage,
                        limit: this.pagination.itemsPerPage,
                        order: this.sortBy
                    };

                    var prom = MarketingStatsService.queryStats(params, user_id);

                    function success(res) {
                        $scope.stats.pagination.totalItems = res.data.total;
                        $scope.stats.data = res.data.stats;
                    }

                    function error(err) {
                        Notification.error(err.data.error);
                    }

                    prom.then(success, error);

                    return prom;
                }
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

            $scope.loadUserSubscriptions = function (user_id) {
                var prom = SubscriptionService.query(user_id);

                function success(res) {
                    $scope.subscriptions = res.data;
                    console.log(res.data);
                }

                function error(err) {
                    Notification.error(err.data.error);
                }

                prom.then(success, error);

                return prom;
            };

            $scope.loginAsUser = function (user) {
                UserService.loginAsUser(user.user_id)
                    .then(function (res) {
                        window.open($configs.DASHBOARD_URL + "/doLogin?token=" + res.data.token);
                    });
            };

            vm.refund = function (transaction) {
                TransactionService.refund(transaction.id)
                    .then(function (res) {
                        transaction.type = 'refund';
                        Notification.success('Transaction refunded successfully!');
                    }, function (err) {
                        Notification.error('Oops, there was an error trying to refund the transaction!');
                    });
            };

            $scope.openRefundConfirm = function (transaction) {
                var modalOptions = {
                    closeButtonText: 'Cancel',
                    actionButtonText: 'Refund!',
                    headerText: 'Are you sure?',
                    bodyText: 'Are you sure you want to refund this Transaction?'
                };

                modalService.showModal({}, modalOptions).then(function (result) {
                    vm.refund(transaction);
                });
            };

            $scope.open = function (transaction) {

                var tmpTransaction = angular.copy(transaction);
                var nonShowProps = ['info', 'avsresponse', 'cvvresponse', 'refunded', 'response', 'response_code', 'responsetext'];
                angular.forEach(nonShowProps, function (propToRemove) {
                    if (angular.isDefined(tmpTransaction[propToRemove])) {
                        delete tmpTransaction[propToRemove];
                    }
                });

				tmpTransaction = angular.forEach( tmpTransaction, function ( value, key) {
					var nKey = key.substring(0, 1).toUpperCase() + key.substring(1).toLowerCase();
					nKey = nKey.replace(/_/g, ' ');
					tmpTransaction[nKey] = value;
					delete tmpTransaction[key];
				});

                var modalInstance = $uibModal.open({
                    templateUrl: 'transactionDetails.html',
                    controller: ['$scope', 'properties', '$uibModalInstance', function ($scope, properties, $uibModalInstance) {
                        $scope.properties = properties;

                        $scope.close = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                    }],
                    size: 'lg',
                    resolve: {
                        properties: function () {
                            return tmpTransaction;
                        }
                    }
                });
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
