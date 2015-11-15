	angular.module('app.views', ['app.home', 'ui.router']);

	angular.module('app', [
		'templates-app',
		'app.header',
		'app.footer',
		'app.home',
		'ui.bootstrap.tpls',
		'ui.bootstrap',
		'app.http-services'
	])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider) {

        $urlRouterProvider.otherwise('/home');
    })

    .run(function run() {

    })

    .controller('AppCtrl', function AppCtrl($scope, $location) {
        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle + ' | boilerplate';
            }
        });

        $scope.links = [
            {state: 'home', text: 'Home', icon: 'home'}
        ];
    });
