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
    'LocalStorageModule'
])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $httpProvider, localStorageServiceProvider) {

        $urlRouterProvider.otherwise('/login');
        showErrorsConfigProvider.showSuccess(true);
        $httpProvider.interceptors.push('httpRequestInterceptor');

        localStorageServiceProvider
            .setPrefix('app');
    })

    .run(function run() {

    })

    .controller('AppCtrl', ['$scope', 'AuthService', '$state', function AppCtrl($scope, AuthService, $state) {
        $scope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {

            if (!AuthService.isLoggedIn() && toState.name !== 'login') {
                event.preventDefault();
                $state.go('login');
            }
        });

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
    }]);
