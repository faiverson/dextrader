angular.module('app.views', ['app.home', 'app.auth', 'ui.router']);

angular.module('app', [
        'templates-app',
        'app.header',
        'app.footer',
        'app.home',
        'app.auth',
        'app.dashboard',
        'app.providers',
        'ui-notification',
        'app.shared-directives',
        'ui.bootstrap.tpls',
        'ui.bootstrap',
        'ui.bootstrap.datetimepicker',
        'ngAnimate',
        'app.ui-services',
        'app.shared-filters',
        'angular-loading-bar',
        'app.http-services',
        'LocalStorageModule',
        'app.user-profile',
        'app.testimonials',
        'app.live-signals'
    ])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $httpProvider, localStorageServiceProvider, NotificationProvider) {

        $urlRouterProvider.otherwise('/login');
        showErrorsConfigProvider.showSuccess(true);
        $httpProvider.interceptors.push('httpRequestInterceptor');
        NotificationProvider.setOptions({
            delay: 10000,
            startTop: 20,
            startRight: 10,
            verticalSpacing: 20,
            horizontalSpacing: 20,
            positionX: 'center',
            positionY: 'top'
        });

        localStorageServiceProvider
            .setPrefix('admin');

        $locationProvider.html5Mode({
            enabled: true,
            requireBase: true
        });
    })

    .run(function run() {

    })

    .controller('AppCtrl', ['$scope', 'AuthService', '$state', function AppCtrl($scope, AuthService, $state) {
        $scope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            var perm = toState.data.permission;
            var redirectTo = toState.data.redirectTo;


            if (!AuthService.isLoggedIn() && toState.name !== 'login' && angular.isDefined(perm)) {
                event.preventDefault();
                $state.go('login');
            }

            if (AuthService.isLoggedIn() && toState.name === 'login') {
                event.preventDefault();
                $state.go('dashboard');
            }

            if (AuthService.isLoggedIn() && !AuthService.userHasPermission(perm)) {
                event.preventDefault();

                if (angular.isDefined(redirectTo)) {
                    $state.go(redirectTo);
                } else {
                    $state.go('dashboard');
                }
            }
        });

        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle;
            }

            $scope.bodyClass = toState.data.bodyClass || '';
        });
    }]);

