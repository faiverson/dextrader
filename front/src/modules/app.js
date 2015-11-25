angular.module('app.views', ['app.auth', 'ui.router']);

angular.module('app', [
    'templates-app',
    'app.header',
    'app.footer',
    'app.auth',
    'app.dashboard',
    'app.user-profile',
    'app.affiliates',
    'ui-notification',
    'ui.bootstrap.tpls',
    'ui.bootstrap',
    'app.http-services',
    'app.shared-directives',
    'LocalStorageModule',
    'ngAnimate'
])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $httpProvider, localStorageServiceProvider, NotificationProvider) {

        $urlRouterProvider.otherwise('login');
        showErrorsConfigProvider.showSuccess(true);
        $httpProvider.interceptors.push('httpRequestInterceptor');
        NotificationProvider.setOptions({
            delay: 10000,
            startTop: 20,
            startRight: 10,
            verticalSpacing: 20,
            horizontalSpacing: 20,
            positionX: 'right',
            positionY: 'top'
        });


        localStorageServiceProvider
            .setPrefix('app');

        $locationProvider.html5Mode(true);
    })

    .run(function run() {

    })

    .controller('AppCtrl', ['$scope', 'AuthService', '$state', function AppCtrl($scope, AuthService, $state) {
        $scope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {

            if (!AuthService.isLoggedIn() && toState.name !== 'login') {
                event.preventDefault();
                $state.go('login');
            }

            if (AuthService.isLoggedIn() && toState.name === 'login') {
                event.preventDefault();
                $state.go('dashboard');
            }
        });

        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle;
            }

            $scope.bodyClass = toState.data.bodyClass || '';
        });

        $scope.links = [
            {
                state: 'home',
                text: 'Home',
                icon: 'home'
            }
        ];
    }]);
