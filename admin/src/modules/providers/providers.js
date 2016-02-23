angular.module('app.providers', ['ui.router', 'ngFileUpload'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('providers', {
                url: '/providers',
                templateUrl: 'modules/providers/providers.tpl.html',
                controller: 'ProvidersCtrl',
                data: {
                    pageTitle: 'Providers'
                }
            })
            .state('providers.list', {
                url: '/list',
                templateUrl: 'modules/providers/providers.list.tpl.html',
                controller: 'ProvidersListCtrl',
                data: {
                    pageTitle: 'Providers - List'
                }
            })
            .state('providers.new', {
                url: '/new',
                templateUrl: 'modules/providers/providers.form.tpl.html',
                controller: 'ProvidersFormCtrl',
                data: {
                    pageTitle: 'Providers - New'
                }
            })
            .state('providers.edit', {
                url: '/edit/:id',
                templateUrl: 'modules/providers/providers.form.tpl.html',
                controller: 'ProvidersFormCtrl',
                data: {
                    pageTitle: 'Providers - Edit'
                }
            });
    })

    .controller('ProvidersCtrl', ['$scope', function ($scope) {

    }])
    .controller('ProvidersFormCtrl', ['$scope', '$state', '$stateParams', 'ProvidersService', 'Notification', 'Upload', '$site-configs', 'AuthService',
        function ($scope, $state, $stateParams, ProvidersService, Notification, Upload, $configs, AuthService) {

            var vm = this;

            $scope.image = '/assets/images/image-placeholder.gif';

            $scope.uploadAndSave = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.providerForm.$valid) {

                    if ($scope.providerForm.file.$valid && $scope.file) {
                        Notification.info('Processing image!');

                        $scope.upload($scope.file)
                            .then(function (resp) {
                                $scope.provider.image =  resp.data.data.filename;
                                $scope.save();
                            }, function (resp) {
                                Notification.error('Oops! something went wrong trying to save the image!');
                            });
                    } else {
                        $scope.save();
                    }
                }
            };

            $scope.save = function () {

                function success(res) {
                    Notification.success('Provider created successfully!');
                    $state.go('providers.list');
                }

                function error(err) {
                    Notification.error("Oops! there was an error trying to save the provider!");
                }

                ProvidersService.save($scope.provider)
                    .then(success, error);
            };

            $scope.upload = function (file) {
                return Upload.upload({
                    url: $configs.API_BASE_URL + 'files/uploads',
                    data: {file: file},
                    headers: {'Authentication': AuthService.getUserToken()}
                });
            };

            vm.getProviderForEdit = function (id) {
                ProvidersService.getOne(id)
                    .then(function (res) {
                        $scope.provider = res.data;
                        $scope.image = '/assets/images/' + $scope.provider.image;
                    });
            };

            vm.init = function () {
                if(angular.isDefined($stateParams.id)){
                    vm.getProviderForEdit($stateParams.id);
                }
            };

            vm.init();
        }])

    .controller('ProvidersListCtrl', ['$scope', 'ProvidersService', 'Notification', 'modalService', function ($scope, ProvidersService, Notification, modalService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getProvider();
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

                vm.getProvider();
            }
        };

		$scope.openDeleteConfirm = function (id) {
			var modalOptions = {
				closeButtonText: 'Cancel',
				actionButtonText: 'Delete Broker',
				headerText: 'Delete Broker?',
				bodyText: 'Are you sure you want to delete this broker?'
			};

			modalService.showModal({}, modalOptions).then(function (result) {
				ProvidersService.destroy(id).then(vm.successDelete, vm.errorDelete);
			});
		};

		vm.successDelete = function (res) {
			vm.getProvider();
			Notification.success('Broker was removed successfully!');
		};

		vm.errorDelete = function (err) {
			Notification.error('Oops! there was an error trying to remove this broker!');
		};

        vm.getProvider = function () {

            var params = {
                start: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                length: $scope.pagination.itemsPerPage,
                sortBy: $scope.sortBy.column,
                sortDir: $scope.sortBy.dir
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.totalItems;
                $scope.providers = res.data.items;
            }

            function error(err) {
                Notification.error('Oops! there was an error trying to load providers!');
            }

            ProvidersService.query(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getProvider();
        };

        vm.init();
    }]);
