angular.module('app.home', ['ui.router', 'ui.bootstrap.showErrors'])
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

    .controller('UsersCtrl', ['$scope', 'UserService',
        function ($scope, UserService) {

            var vm = this;

            $scope.pagination = {
                itemsPerPage: 3,
                currentPage: 1,
                getPage: function () {
                    vm.getUsers();
                }
            };

            vm.getUsers = function (page) {
                function success(response) {
                    $scope.users = response.data.splice(($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage, $scope.pagination.itemsPerPage);
                }

                function error(res) {
                    console.log(res);
                }

                UserService.getUsers().then(success, error);
            };


            vm.init = function () {
                vm.getUsers();
            };

            vm.init();

        }])

    .controller('UsersFormCtrl', ['$scope', '$state', '$stateParams', '$filter', 'UserService', 'UserRolesService',
        function ($scope, $state, $stateParams, $filter, UserService, UserRolesService) {

            var vm = this;

            $scope.save = function () {
                if ($scope.userForm.$valid) {

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
                console.log('error', err)
            };

            vm.getUserRoles = function () {
                $scope.roles = UserRolesService.getRoles();
            };

            vm.loadUser = function (id) {
                UserService.getUser(id)
                    .then(vm.successLoadUser, vm.errorLoadUser);
            };

            vm.successLoadUser = function (res) {
                $scope.user = res.data;

                $scope.user.password = '******';

                $scope.selectedRole = $filter('filter')($scope.roles, {id: $scope.user.role_id}, true)[0];
            };

            vm.errorLoadUser = function (res) {
                $state.go('users');
            };

            vm.init = function () {
                vm.getUserRoles();

                if (angular.isDefined($stateParams.id)) {
                    vm.loadUser($stateParams.id);
                }
            };

            vm.init();

        }]);