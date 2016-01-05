angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', '$filter', '$rootScope', function ($http, $q, $configs, localStorageService, jwtHelper, $objects, $filter, $rootScope) {

        function login(username, password) {
            var endpoint = $configs.API_BASE_URL + 'login';
            var deferred = $q.defer();

            function success(res) {
                if (res.data.success) {

                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);
                    $rootScope.$broadcast("user-has-change");

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

        function isLoggedIn() {
            var token = localStorageService.get('token');

            return token != null && angular.isDefined(token);
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

            return permissions;
        }

        function userHasPermission(perm) {
            var permissions = getUserPermissions();

            if (angular.isUndefined(perm)) {
                return true;
            }

            return ($filter('filter')(permissions, {name: perm}, true)).length > 0;
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
                    $rootScope.$broadcast("user-has-change");

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

        function setUserToken(token) {
            // Set the token into local storage
            localStorageService.set('token', token);
            $rootScope.$broadcast("user-has-change");
        }

        function getUserToken() {
            return 'Bearer ' + localStorageService.get('token');
        }

        return {
            login: login,
            getLoggedInUser: getLoggedInUser,
            isLoggedIn: isLoggedIn,
            logout: logout,
            userHasPermission: userHasPermission,
            forgotPassword: forgotPassword,
            resetPassword: resetPassword,
            setUserToken: setUserToken,
            getUserToken: getUserToken
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

    .factory('ProvidersService', ['$http', '$q', '$site-configs', '$objects', function ($http, $q, $configs, $objects) {
        var service = $configs.API_BASE_URL + 'providers';

        function query(params) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isDefined(params)) {
                endpoint += '?' + $objects.toUrlString(params);
            }

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
            var endpoint = service,
                deferred = $q.defer();


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

        function getOne(id) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isUndefined(id)) {
                deferred.reject('ID field is required!');
            }

            endpoint += '/' + id;

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
            query: query,
            save: save,
            getOne: getOne
        };
    }])

    .factory('LiveSignalsService', ['$http', '$q', '$site-configs', '$objects', function ($http, $q, $configs, $objects) {
        var service = $configs.API_BASE_URL + 'signals/live';

        function query(params) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isDefined(params)) {
                endpoint += '?' + $objects.toUrlString(params);
            }

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
            var endpoint = service,
                deferred = $q.defer();


            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            if (angular.isDefined(data.id)) {
                endpoint += '/' + data.id;
                $http.put(endpoint, data).then(success, error);
            } else {
                $http.post(endpoint, data).then(success, error);
            }

            return deferred.promise;
        }

        function getOne(id) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isUndefined(id)) {
                deferred.reject('ID field is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function destroy(id) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isUndefined(id)) {
                deferred.reject('ID field is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.delete(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            query: query,
            save: save,
            getOne: getOne,
            destroy: destroy
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

    .factory('TestimonialsService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'testimonials';

        function query(params) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();


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

        function getOne(id) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isUndefined(id)) {
                deferred.reject('ID field is required!');
            }

            endpoint += '/' + id;

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
            query: query,
            save: save,
            getOne: getOne
        };

    }])

    .factory('CreditCardService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'users/';

        function query(user_id) {
            var endpoint = service+ user_id + '/cards',
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function getOne(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Card id is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.cc_id)) {
                endpoint += '/' + data.cc_id;

                $http.put(endpoint, data)
                    .then(success, error);
            } else {
                $http.post(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            getOne: getOne,
            save: save
        };
    }])

    .factory('BillingAddressService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'users/';

        function query(user_id) {
            var endpoint = service + user_id + '/billing-address',
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }


        function getOne(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Address id is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.address_id)) {
                endpoint += '/' + data.address_id;

                $http.put(endpoint, data)
                    .then(success, error);
            } else {
                $http.post(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            getOne: getOne,
            save: save
        };
    }])

    .factory('UserRolesService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'roles';

        function getRoles() {
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
            getRoles: getRoles
        };
    }]);
