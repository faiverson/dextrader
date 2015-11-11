
(function(){
    angular.module('app.http-services', ['app.site-configs'])
        .factory('UserService', ['$http', '$q', '$site-configs', function($http, $q, $configs){
            var service = $configs.API_BASE_URL + 'users';

            return {
                getUsers: getUsers
            };

            function getUsers(){
                var deferred = $q.defer(),
                    endpoint = service;


                $http.get(endpoint).then(success, error);

                function success(res){
                    deferred.resolve(res.data);
                }

                function error(res){
                    deferred.reject(res);
                }

                return deferred.promise;
            }
        }]);
})();
