angular.module('app.privacy', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('privacy', {
                url: '/privacy-policy',
                templateUrl: 'modules/privacy/privacy.tpl.html',
                data: {
                    pageTitle: 'Privacy Policy'
                }
            });
    });
