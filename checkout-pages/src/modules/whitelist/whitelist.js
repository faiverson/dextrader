angular.module('app.whitelist', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('whitelist', {
                url: '/whitelist',
                templateUrl: 'modules/whitelist/whitelist.tpl.html',
                data: {
                    pageTitle: 'Whitelist',
                    isPublic: true
                }
            });
    });

