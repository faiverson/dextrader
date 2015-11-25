angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', function ($http, $q, $configs, localStorageService, jwtHelper, $objects) {

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

            if (this.isLoggedIn()) {
                data = jwtHelper.decodeToken(localStorageService.get('token'));
            }
            return data;
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

        return {
            login: login,
            getLoggedInUser: getLoggedInUser,
            isLoggedIn: isLoggedIn,
            logout: logout
        };
    }])

    .factory('httpRequestInterceptor', ['localStorageService', function(localStorageService) {
        return {
            request: function($config) {
                var header;
                if($config.withCredentials !== false) {
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
