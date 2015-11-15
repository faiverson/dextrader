
angular.module('app.http-services', ['app.site-configs'])
.factory('UserService', ['$http', '$q', '$site-configs', function($http, $q, $configs){
		var service = $configs.API_BASE_URL + 'users';

		function getUsers(){
			var deferred = $q.defer(),
				endpoint = service;

			function success(res){
				deferred.resolve(res.data);
			}

			function error(res){
				deferred.reject(res);
			}

			$http.get(endpoint).then(success, error);

			return deferred.promise;
		}

		return {
			getUsers: getUsers
		};
	}]);
