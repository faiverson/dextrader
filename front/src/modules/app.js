angular.module('app', [
    'templates-modules',
    // 'templates-common',
    'app.home',
    'ui.router',
    'ui.bootstrap.tpls',
    'ui.bootstrap'
])

    .config(function appConfig($stateProvider, $urlRouterProvider, $locationProvider) {

        $stateProvider
            .state('home', {
                url: '/home',
                templateUrl: 'home/home.tpl.html',
                data: {
                    pageTitle: 'Home'
                }
            });

        $urlRouterProvider.otherwise('/home');
    })

    .run(function run() {

    });
