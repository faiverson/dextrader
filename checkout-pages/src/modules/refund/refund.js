angular.module('app.refund', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('refund', {
                url: '/refund-policy',
                templateUrl: 'modules/refund/refund.tpl.html',
                data: {
                    pageTitle: 'Refund Policy',
                    isPublic: true
                }
            });
    });
