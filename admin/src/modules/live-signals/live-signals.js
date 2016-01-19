angular.module('app.live-signals', ['ui.router', 'ngFileUpload', 'ui.mask'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('live_signals', {
                url: '/live-signals',
                templateUrl: 'modules/live-signals/live-signals.tpl.html',
                controller: 'LiveSignalsCtrl',
                data: {
                    pageTitle: 'Live Signals',
                    permission: 'signal',
                    redirectTo: 'dashboard'
                }
            })
            .state('live_signals.list', {
                url: '/list',
                templateUrl: 'modules/live-signals/live-signals.list.tpl.html',
                controller: 'LiveSignalsListCtrl',
                data: {
                    pageTitle: 'Live Signals - List'
                }
            })
            .state('live_signals.new', {
                url: '/new',
                templateUrl: 'modules/live-signals/live-signals.form.tpl.html',
                controller: 'LiveSignalsFormCtrl',
                data: {
                    pageTitle: 'Live Signals - New'
                }
            })
            .state('live_signals.edit', {
                url: '/edit/:prd/:id',
                templateUrl: 'modules/live-signals/live-signals.form.tpl.html',
                controller: 'LiveSignalsFormCtrl',
                data: {
                    pageTitle: 'Live Signals - Edit'
                }
            });
    })
    .controller('LiveSignalsCtrl', ['$scope', function ($scope) {

    }])
    .controller('LiveSignalsFormCtrl', ['$scope', '$state', '$stateParams', 'LiveSignalsService', 'Notification', 'Upload', '$site-configs', 'AuthService',
        function ($scope, $state, $stateParams, LiveSignalsService, Notification, Upload, $configs, AuthService) {

            var vm = this;

            $scope.signal = {
                direction: 0
            };

            $scope.open = function ($event) {
                $scope.dateDatepickerOpen = true;
            };

            $scope.products = [
                { id: 1, name: 'IB' },
                { id: 3, name: 'NA' },
                { id: 4, name: 'FX' }
            ];

            $scope.product = $scope.products[0];

            $scope.save = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.liveSignalsForm.$valid) {

                    $scope.signal.signal_date = moment($scope.signal_date).format("YY-M-D");
                    $scope.signal.signal_time = moment($scope.signal_time).format("HH:mm");
                    $scope.signal.expiry_time = moment($scope.expiry_time).format("HH:mm");

                    if ($scope.asset.indexOf('/') < 0) {
                        $scope.signal.asset = $scope.asset.substring(0, 3) + '/' + $scope.asset.substring(3, 6);
                    } else {
                        $scope.signal.asset = $scope.asset;
                    }

                    LiveSignalsService.save($scope.product.name.toLowerCase(), $scope.signal)
                        .then(vm.success, vm.error);
                }
            };

            vm.success = function (res) {
                Notification.success('Signal created successfully!');
                $state.go('live_signals.list');
            };

            vm.error = function (err) {
                Notification.error("Ups! there was an error trying to save the signal!");
            };

            vm.getSignalForEdit = function (id, prd) {
                LiveSignalsService.getOne(id, prd)
                    .then(function (res) {
                        $scope.signal.id = res.data.id;
                        $scope.signal.target_price = parseFloat(res.data.target_price);
                        $scope.signal.close_price = parseFloat(res.data.close_price);
                        $scope.signal.open_price = parseFloat(res.data.open_price);
                        $scope.signal.trade_type = res.data.trade_type;
                        $scope.signal_date = moment(res.data.signal_date, "YYYY-MM-DD").toDate();
                        $scope.signal_time = moment(res.data.signal_time, "HH:mm");
                        $scope.expiry_time = moment(res.data.expiry_time, "HH:mm");
                        $scope.asset = res.data.asset;

                    });
            };

            vm.init = function () {
                if (angular.isDefined($stateParams.id) && angular.isDefined($stateParams.prd)) {
                    vm.getSignalForEdit($stateParams.id, $stateParams.prd);
                }
            };

            vm.init();
        }])

    .controller('LiveSignalsListCtrl', ['$scope', 'LiveSignalsService', 'Notification', 'modalService', function ($scope, LiveSignalsService, Notification, modalService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getLiveSignals();
            }
        };

        $scope.filters = {
            products: [
                { id: 1, name: 'IB' },
                { id: 3, name: 'NA' },
                { id: 4, name: 'FX' }
            ],
            apply: function () {
                vm.getLiveSignals();
            }
        };

        $scope.sortBy = {
            column: 1,
            dir: 'asc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getLiveSignals();
            }
        };

        $scope.openDeleteConfirm = function (id) {
            var modalOptions = {
                closeButtonText: 'Cancel',
                actionButtonText: 'Delete Signal',
                headerText: 'Delete Signal?',
                bodyText: 'Are you sure you want to delete this Signal?'
            };

            modalService.showModal({}, modalOptions).then(function (result) {
                LiveSignalsService.destroy(id).then(vm.successDelete, vm.errorDelete);
            });
        };

        vm.successDelete = function (res) {
            vm.getLiveSignals();
            Notification.success('Live Signal was removed successfully!');
        };

        vm.errorDelete = function (err) {
            Notification.error('Ups! there was an error trying to remove this Live Signal!');
        };

        vm.getLiveSignals = function () {

            var params = {
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                limitt: $scope.pagination.itemsPerPage,
                sortBy: $scope.sortBy.column,
                sortDir: $scope.sortBy.dir
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.total;
                $scope.signals = res.data.signals;
            }

            function error(err) {
                Notification.error('Ups! there was an error trying to load providers!');
            }

            LiveSignalsService.query($scope.filters.product.name.toLowerCase(), params)
                .then(success, error);
        };

        vm.init = function () {
            $scope.filters.product = $scope.filters.products[0];
            vm.getLiveSignals();
        };

        vm.init();
    }]);
