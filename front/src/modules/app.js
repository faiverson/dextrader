	angular.module('app.views', ['app.home', 'app.auth', 'ui.router']);

	angular.module('app', [
		'templates-app',
		'app.header',
		'app.footer',
		'app.home',
		'app.auth',
		'ui.bootstrap.tpls',
		'ui.bootstrap',
		'app.http-services',
		'satellizer'
	])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $authProvider, $httpProvider) {
		// Satellizer configuration that specifies which API
		// route the JWT should be retrieved from
		$authProvider.loginUrl = '/api/login';
        $urlRouterProvider.otherwise('/login');
        showErrorsConfigProvider.showSuccess(true);
		$httpProvider.interceptors.push('httpRequestInterceptor');
	})

    .run(function run() {

    })

	.factory('httpRequestInterceptor', ['$rootScope', function($rootScope) {
		return {
			request: function($config) {
				var header;
				if($config.withCredentials !== false) {
					$config.withCredentials = true;
					header = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmaXJzdF9uYW1lIjoiQ2hyaXN0aW5hIiwibGFzdF9uYW1lIjoiSGFobiIsInVzZXJuYW1lIjoiQmF1bWJhY2hfTXlhIiwiZW1haWwiOiJTeWJsZTUzQExlc2NoLmluZm8iLCJpc3MiOiJsb2dpbiIsImV4cCI6MTQ0ODMzNDU1OCwic3ViIjo0LCJpYXQiOiIxNDQ3NzI5NzU4IiwibmJmIjoiMTQ0NzcyOTc1OCIsImp0aSI6IjVlNWY5ZjFiYTU1MjM0N2VlMmUwMWU3OTUzYzhmYzc0In0.QVIm35-XayAxE8YdfFJh9Vtfuz0pbLi4dEjXu-JCGpo';
					$config.headers['Authorization'] = header;
				}
				return $config;
			}
		};
	}])

    .controller('AppCtrl', function AppCtrl($scope, $location) {
        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle;
            }
        });

        $scope.links = [
            {
				state: 'home',
				text: 'Home',
				icon: 'home'
			}
        ];
    });
