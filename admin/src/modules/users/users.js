angular.module('app.home', ['ui.router', 'ui.bootstrap.showErrors', 'datatables', 'datatables.bootstrap'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('users', {
                url: '/users',
                templateUrl: 'modules/users/users.list.tpl.html',
                controller: 'UsersCtrl',
                data: {
                    pageTitle: 'Users'
                }
            })
            .state('users-new', {
                url: '/users/new',
                templateUrl: 'modules/users/users.form.tpl.html',
                controller: 'UsersFormCtrl',
                data: {
                    pageTitle: 'Users'
                }
            })
            .state('users-edit', {
                url: '/users/edit/:id',
                templateUrl: 'modules/users/users.form.tpl.html',
                controller: 'UsersFormCtrl',
                data: {
                    pageTitle: 'Users'
                }
            });
    })

    .controller('UsersCtrl', ['$scope', '$site-configs', 'UserService', 'modalService', 'Notification', '$timeout',
        function ($scope, $configs, UserService, modalService, Notification, $timeout) {
			var vm = this;

			$scope.pagination = {
				totalItems: 20,
				currentPage: 1,
				itemsPerPage: 10,
				pageChange: function () {
					$scope.getUsers();
				}
			};

			$scope.sortBy = {};

			$scope.filters = {
				from: null,
				to: null,
				first_name: null,
				last_name: null,
				email: null,
				apply: function () {
					this.toApply.from = null;
					this.toApply.to = null;
					this.toApply.first_name = null;
					this.toApply.last_name = null;
					this.toApply.email = null;

					if (angular.isDefined(this.from) && this.from !== null) {
						this.toApply.from = moment(this.from).format('YYYY-MM-DD');
					}

					if (angular.isDefined(this.to) && this.to !== null) {
						this.toApply.to = moment(this.to).format('YYYY-MM-DD');
					}

					if (angular.isDefined(this.first_name) && this.first_name !== null && this.first_name.length >= 3) {
						this.toApply.first_name = this.first_name;
					}

					if (angular.isDefined(this.last_name) && this.last_name !== null && this.last_name.length >= 3) {
						this.toApply.last_name = this.last_name;
					}

					if (angular.isDefined(this.email) && this.email !== null && this.email.length >= 3) {
						this.toApply.email = this.email;
					}

					$scope.getUsers();
				},
				toApply: {}
			};

			$scope.$watch('filters.from', function (nv, ov) {
				$scope.filters.apply();
			});

			$scope.$watch('filters.to', function (nv, ov) {
				if (angular.isDefined(nv) && nv !== ov) {
					$scope.filters.apply();
				}
			});

			$scope.$watch('filters.first_name', function (nv, ov) {
				if (angular.isDefined(nv) && nv !== ov) {
					$timeout.cancel(vm.first_name_tm);

					vm.first_name_tm = $timeout(function () {
						$scope.filters.apply();
					}, 600);

				}
			});

			$scope.$watch('filters.last_name', function (nv, ov) {
				if (angular.isDefined(nv) && nv !== ov) {
					$timeout.cancel(vm.last_name_tm);

					vm.last_name_tm = $timeout(function () {
						$scope.filters.apply();
					}, 600);
				}
			});

			$scope.$watch('filters.email', function (nv, ov) {
				if (angular.isDefined(nv) && nv !== ov) {
					$timeout.cancel(vm.email_tm);

					vm.email_tm = $timeout(function () {
						$scope.filters.apply();
					}, 600);
				}
			});

			$scope.openDeleteConfirm = function (id) {
				var modalOptions = {
					closeButtonText: 'Cancel',
					actionButtonText: 'Delete User',
					headerText: 'Delete User?',
					bodyText: 'Are you sure you want to delete this User?'
				};

				modalService.showModal({}, modalOptions).then(function (result) {
					UserService.destroy(id).then(vm.successDelete, vm.errorDelete);
				});
			};

			$scope.loginAsUser = function (id) {
				UserService.loginAsUser(id).then(function(response) {
						window.open($configs.DASHBOARD_URL + "/doLogin?token=" + response.data.token);
				},
				function (err) {
					Notification.error('Oops! there was an error trying to login as this user!');
				});
			};

			$scope.getUsers = function () {

				var params = {
					offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
					limit: $scope.pagination.itemsPerPage,
					order: $scope.sortBy,
					filter: $scope.filters.toApply
				};

				function success(res) {
					$scope.pagination.totalItems = res.data.totalItems;
					$scope.users = res.data.users;
				}

				function error(err) {
					Notification.error('Oops! there was an error trying to load users!');
				}

				UserService.getUsers(params)
						.then(success, error);
			};

			vm.successDelete = function (res) {
				$scope.getUsers();
				Notification.success('User was removed successfully!');
			};

			vm.errorDelete = function (err) {
				Notification.error('Oops! there was an error trying to remove this user!');
			};

			vm.init = function () {
				$scope.getUsers();
			};

			vm.init();
		}])

    .controller('UsersFormCtrl', ['$scope', '$q', '$state', '$stateParams', '$filter', 'UserService', 'UserRolesService', 'Notification',
        function ($scope, $q, $state, $stateParams, $filter, UserService, UserRolesService, Notification) {

            var vm = this;

            $scope.save = function () {
                if ($scope.userForm.$valid) {

                    $scope.user.roles = $scope.roles
                        .filter(function (role) {
                            return role.selected;
                        })
                        .map(function (role) {
                            return role.role_id;
                        });

					$scope.user.roles = JSON.stringify($scope.user.roles);

                    if ($scope.user.password === '******') {
                        delete $scope.user.password;
                    }

                    delete $scope.user.role;

                    UserService.saveUser($scope.user)
                        .then(vm.successUserSave, vm.errorUserSave);
                } else {
                    $scope.$broadcast('show-errors-check-validity');
                }
            };

            vm.successUserSave = function (res) {
                $state.go('users');
            };

            vm.errorUserSave = function (response) {
				var txt = '';
				response = response.data;
				if(angular.isArray(response.error)) {
					angular.forEach(response.error, function(item) {
						txt += item + '<br>';
					});
				}
				else {
					txt += response.error;
				}
				Notification.error("Oops! " + txt);
            };

            vm.getUserRoles = function () {
                var prom = UserRolesService.getRoles();
                prom.then(function (res) {
                    $scope.roles = res.data;
                });

                return prom;
            };

            vm.loadUser = function (id) {
                var prom = UserService.getUser(id);
                prom.then(vm.successLoadUser, vm.errorLoadUser);

                return prom;
            };

            vm.successLoadUser = function (res) {
                $scope.user = res.data;
            };

            vm.errorLoadUser = function (res) {
                $state.go('users');
            };

            vm.init = function () {
                $scope.user = {};

                var proms = [];
                proms.push(vm.getUserRoles());

                if (angular.isDefined($stateParams.id)) {
                    proms.push(vm.loadUser($stateParams.id));
                }

                $q.all(proms).then(vm.setUser);
            };

            vm.setUser = function () {
                $scope.user.password = '******';
                if (angular.isArray($scope.user.roles)) {
                    angular.forEach($scope.roles, function (role) {
                        role.selected = ($filter('filter')($scope.user.roles, {role_id: role.role_id}, true)).length > 0;

                    });
                }

            };

            $scope.toggleRole = function (role) {
                role.selected = !role.selected;
            };

            vm.init();

        }]);