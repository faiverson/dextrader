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

    .controller('UsersCtrl', ['$scope', 'UserService', 'localStorageService', '$site-configs',
        function ($scope, UserService, localStorageService, $config) {
            var vm = this;
            $scope.pagination = {
                totalItems: 0,
                currentPage: 1,
                itemsPerPage: 10,
                pageChanged: function () {
                    vm.getUsers();
                }
            };

            $scope.filters = {
                params: {},
                reset: function(){
                    this.params = {};
                    delete this.name;
                    delete this.email;
                },
                apply: function () {

                    this.params = {};

                    if(angular.isDefined(this.name)){
                        this.params.first_name = this.name;
                    }

                    if(angular.isDefined(this.email)){
                        this.params.email = this.email;
                    }

                    vm.getUsers();

                }
            };

            vm.getUsers = function () {
                var params = {
                    start: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                    length: $scope.pagination.itemsPerPage,
                    filter: $scope.filters.params
                };

                function success(res) {
                    $scope.pagination.totalItems = res.data.total;
                    $scope.users = res.data.users;
                }

                function error(err) {

                }

                UserService.query(params)
                    .then(success, error);
            };

            $scope.remove = function (id) {

            };

            vm.init = function () {
                vm.getUsers();
            };

            vm.init();

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
                            return role.role_id;
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