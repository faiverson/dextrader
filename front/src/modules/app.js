angular.module('app.views', ['app.auth', 'ui.router']);

angular.module('app', [
        'templates-app',
        'app.header',
        'app.footer',
        'app.auth',
        'app.dashboard',
        'app.user-profile',
        'app.affiliates',
        'app.dex_ib',
        'ui-notification',
        'ui.bootstrap.tpls',
        'ui.bootstrap',
        'ui.bootstrap.datetimepicker',
        'app.http-services',
        'app.shared-directives',
        'app.shared-filters',
        'LocalStorageModule',
        'ngAnimate',
        'angular-loading-bar',
        'app.privacy',
        'app.disclosure',
        'app.term-and-conditions',
        'app.dex-na',
        'app.dex-fx',
        'app.whitelist',
        'app.refund',
        'app.start',
        'app.contact-us'
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
            .setPrefix('dextrader');

        $locationProvider.html5Mode(true);
    })

    .run(function () {
    })

    .controller('AppCtrl', ['$scope', 'AuthService', '$state', function AppCtrl($scope, AuthService, $state) {
        $scope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            var perm = toState.data.permission;
            var redirectTo = toState.data.redirectTo;
            var isPublic = toState.data.isPublic;


            if (!AuthService.isLoggedIn() && toState.name !== 'login' && angular.isUndefined(isPublic)) {
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

        $scope.links = [
            {
                state: 'home',
                text: 'Home',
                icon: 'home'
            }
        ];
    }]);
