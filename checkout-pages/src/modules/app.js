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
        'angular-loading-bar',
        'app.checkout',
        'app.upsell',
        'app.downsell',
        'app.thankyou',
        'app.privacy',
        'app.disclosure',
        'app.term-and-conditions',
        'app.whitelist',
        'app.refund',
        'app.contact-us',
        'xtForm',
        'ngAutodisable'
    ])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider, showErrorsConfigProvider, $httpProvider, localStorageServiceProvider, NotificationProvider) {

        $urlRouterProvider.otherwise('ib');
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
            .setStorageType('sessionStorage')
            .setPrefix('dextraderCP');

        $locationProvider.html5Mode(true);
    })

    .run(function () {
    })


    .controller('AppCtrl', ['$scope', '$state', '$location', '$window', '$site-configs', function AppCtrl($scope, $state, $location, $window, $configs) {

        $scope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            forceSSL();
        });

        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            if (angular.isDefined(toState.data.pageTitle)) {
                $scope.pageTitle = toState.data.pageTitle;
            }
        });

        var forceSSL = function () {
            if ($location.protocol() !== $configs.HTTP_PROTOCOL) {
                $window.location.href = $location.absUrl().replace('http', $configs.HTTP_PROTOCOL);
            }
        };
    }]);
