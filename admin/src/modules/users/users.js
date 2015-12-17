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

    .controller('UsersCtrl', ['$scope', 'UserService', 'DTOptionsBuilder', 'DTColumnBuilder', 'localStorageService', '$compile', '$site-configs',
        function ($scope, UserService, DTOptionsBuilder, DTColumnBuilder, localStorageService, $compile, $config) {

            function actionsHtml(data, type, full, meta) {
                return '<button class="btn btn-warning" ui-sref="users-edit({ id:' + data.user_id + '})">' +
                    '   <i class="fa fa-edit"></i>' +
                    '</button>&nbsp;' +
                    '<button class="btn btn-danger" ng-click="showCase.delete(showCase.persons[' + data.user_id + '])" )"="">' +
                    '   <i class="fa fa-trash-o"></i>' +
                    '</button>';
            }

            function createdRow(row, data, dataIndex) {
                // Recompiling so we can bind Angular directive to the DT
                $compile(angular.element(row).contents())($scope);
            }

            $scope.dtOptions = DTOptionsBuilder.newOptions()
                .withOption('ajax', {
                    headers: {'Authorization': 'Bearer ' + localStorageService.get('token')},
                    url: $config.API_BASE_URL + 'users',
                    type: 'GET'
                })
                .withDataProp('data')
                .withOption('processing', true)
                .withOption('serverSide', true)
                .withBootstrap()
                .withPaginationType('full_numbers')
                .withOption('createdRow', createdRow);

            $scope.dtColumns = [
                DTColumnBuilder.newColumn('user_id').withTitle('ID'),
                DTColumnBuilder.newColumn('username').withTitle('Username'),
                DTColumnBuilder.newColumn('full_name').withTitle('Name'),
                DTColumnBuilder.newColumn('email').withTitle('Email'),
                DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                    .renderWith(actionsHtml)
            ];

        }])

    .controller('UsersFormCtrl', ['$scope', '$q', '$state', '$stateParams', '$filter', 'UserService', 'UserRolesService',
        function ($scope, $q, $state, $stateParams, $filter, UserService, UserRolesService) {

            var vm = this;

            $scope.save = function () {
                if ($scope.userForm.$valid) {

                    $scope.user.roles = $scope.roles
                        .filter(function (role) {
                            return role.selected;
                        })
                        .map(function (role) {
                            return {id: role.id};
                        });

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

            vm.errorUserSave = function (err) {
                console.log('error', err);
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
                if(angular.isArray($scope.user.roles)){
                    angular.forEach($scope.roles, function (role) {
                        role.selected = ($filter('filter')($scope.user.roles, {id: role.id}, true)).length > 0;

                    });
                }

            };

            $scope.toggleRole = function (role) {
                role.selected = !role.selected;
            };

            vm.init();

        }]);