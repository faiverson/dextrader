angular.module('app', [
        'templates-app',
        'app.header',
        'app.footer',
        'ui-notification',
        'ui.bootstrap.tpls',
        'ui.bootstrap',
        'ui.bootstrap.showErrors',
        'app.http-services',
        'app.shared-directives',
        'LocalStorageModule',
        'ngAnimate',
        'app.home'
    ])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $httpProvider, localStorageServiceProvider, NotificationProvider) {

        $urlRouterProvider.otherwise('home');
        showErrorsConfigProvider.showSuccess(true);
        //$httpProvider.interceptors.push('httpRequestInterceptor');
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
            .setPrefix('dextraderCP');

        $locationProvider.html5Mode(true);
    })

    .run(function () {
    })

    .controller('AppCtrl', ['$scope', '$state', function AppCtrl($scope, $state) {

        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle;
            }
        });
    }]);
