angular.module('app.http-services', ['app.site-configs'])
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

            if(angular.isUndefined(id)){
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
