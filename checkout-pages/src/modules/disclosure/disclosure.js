angular.module('app.disclosure', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('disclosure', {
                url: '/disclosure',
                templateUrl: 'modules/disclosure/disclosure.tpl.html',
                data: {
                    pageTitle: 'Disclosure',
                    isPublic: true
                }
            });
    });
