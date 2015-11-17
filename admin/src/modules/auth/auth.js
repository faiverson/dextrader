angular.module('app.auth', ['ui.router', 'ui.bootstrap.showErrors', 'ngCookies'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'modules/auth/auth.form.tpl.html',
                controller: 'AuthController as auth',
                data: {
                    pageTitle: 'Login Page'
                }
            })
            .state('logout', {
                url: '/logout',
                //templateUrl: 'modules/users/users.form.tpl.html',
                controller: 'AuthController',
                data: {
                    pageTitle: 'Login Page'
                }
            });
    })

    .controller('AuthController', ['$auth','$scope', '$state', 'UserService', '$rootScope', '$http', '$cookies',
	function ($auth, $scope, $state, UserService, $rootScope, $http, $cookies) {
		var cookie = $cookies.get('startup_cp');
		console.log(cookie);
		var vm = this;

		$scope.login = function() {

			var credentials = {
				username: vm.username,
				password: vm.password
			};
			console.log(credentials);
			$http({
				url: '/api/login',
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
				withCredentials: false,
				data: credentials
			}).then(function(response) {
				var r = response.data,
					user;
				console.log(response);
				// Set the stringified user data into local storage
				if(r.success) {
					user = JSON.stringify(response.data.user);
					$cookies.put('startup', user);
					$rootScope.currentUser = response.data.user;
					$state.go('users', {});
				} else {
					console.log(response.data.error);
				}
			}, function(error) {
			});
		};

	}]);