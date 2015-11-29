angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', '$filter', function ($http, $q, $configs, localStorageService, jwtHelper, $objects, $filter) {

        function login(username, password) {
            var endpoint = $configs.API_BASE_URL + 'login';
            var deferred = $q.defer();

            function success(res) {
                if (res.data.success) {

                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);

                    deferred.resolve();
                } else {
                    deferred.reject(res);
                }
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http({
                url: endpoint,
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                withCredentials: false,
                data: $objects.toUrlString({
                    username: username,
                    password: password
                })
            }).then(success, error);


            return deferred.promise;
        }

        function getLoggedInUser() {
            var data = {};

            if (isLoggedIn()) {
                data = jwtHelper.decodeToken(localStorageService.get('token'));
            }

            return data;
        }

        function getUserPermissions() {
            var userData = getLoggedInUser();
            var permissions = [];

            if (angular.isUndefined(userData)) {
                return permissions;
            }

            angular.forEach(userData.roles, function (role) {
                permissions = permissions.concat(role.permissions);
            });

            console.log(permissions);

            return permissions;
        }

        function userHasPermission(perm) {
            var permissions = getUserPermissions();

            if (angular.isUndefined(perm)) {
                return true;
            }

            return ($filter('filter')(permissions, {name: perm}, true)).length > 0;
        }

        function isLoggedIn() {
            var token = localStorageService.get('token');

            return token != null && angular.isDefined(token);
        }

        function logout() {
            var deferred = $q.defer();

            //TODO: implement logout request

            setTimeout(function () {
                localStorageService.clearAll();
                deferred.resolve();
            });

            return deferred.promise;
        }

        function forgotPassword(email) {
            var endpoint = $configs.API_BASE_URL + 'password';
            var deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http.post(endpoint, {email: email}).then(success, error);

            return deferred.promise;
        }

        function resetPassword(token, email, password, password_confirmation) {
            var endpoint = $configs.API_BASE_URL + 'password/reset';
            var deferred = $q.defer();

            function success(res) {
                if (res.data.success) {

                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);

                    deferred.resolve();
                } else {
                    deferred.reject(res);
                }
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http.post(endpoint, {
                token: token,
                email: email,
                password: password,
                password_confirmation: password_confirmation
            }).then(success, error);

            return deferred.promise;
        }

        return {
            login: login,
            getLoggedInUser: getLoggedInUser,
            isLoggedIn: isLoggedIn,
            logout: logout,
            userHasPermission: userHasPermission,
            forgotPassword: forgotPassword,
            resetPassword: resetPassword
        };
    }])

    .factory('httpRequestInterceptor', ['localStorageService', function (localStorageService) {
        return {
            request: function ($config) {
                var header;
                if ($config.withCredentials !== false) {
                    $config.withCredentials = true;
                    header = 'Bearer ' + localStorageService.get('token');
                    $config.headers['Authorization'] = header;
                }
                return $config;
            }
        };
    }])

    .factory('UserService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'users';

        function getUsers() {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);
            return deferred.promise;
        }

        function save(data) {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            if (angular.isDefined(data.id)) {
                $http.put(endpoint, data).then(success, error);
            } else {
                $http.post(endpoint, data).then(success, error);
            }

            return deferred.promise;
        }

        function getUser(id) {
            var deferred = $q.defer(),
                endpoint = service + '/';

            if (angular.isUndefined(id)) {
                deferred.reject('User id not found!');
            }

            endpoint += id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            getUsers: getUsers,
            saveUser: save,
            getUser: getUser
        };
    }])

    .factory('MarketingLinksService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'marketing-links';

        function query() {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            query: query
        };
    }])

    .factory('TrainingService', ['$http', '$q', '$site-configs', 'localStorageService', function ($http, $q, $configs, localStorageService) {
        var service = $configs.API_BASE_URL + 'training';

        function queryAffiliates() {
            var deferred = $q.defer(),
                endpoint = service + '/affiliates';

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function queryDexIB() {
            var deferred = $q.defer(),
                endpoint = service + '/certification';

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function unlockTraining(id) {
            var deferred = $q.defer(),
                endpoint = service + '/certification';


            if (angular.isUndefined(id)) {
                deferred.reject();
            }

            function success(res) {
                // Set the token into local storage
                localStorageService.set('token', res.data.data.token);

                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.post(endpoint, {training_id: id}).then(success, error);

            return deferred.promise;
        }

        return {
            queryAffiliates: queryAffiliates,
            queryDexIB: queryDexIB,
            unlockTraining: unlockTraining
        };
    }])

    .factory('UserRolesService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        //var service = $configs.API_BASE_URL + 'users';
        function getRoles() {
            return [
                {id: 1, name: 'User'},
                {id: 5, name: 'Editor'},
                {id: 9, name: 'Admin'},
                {id: 10, name: 'Super Admin'}
            ];
        }

        return {
            getRoles: getRoles
        };
    }]);
