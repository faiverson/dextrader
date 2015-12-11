angular.module('app.term-and-conditions', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('terms_and_conditions', {
                url: '/terms-and-conditions',
                templateUrl: 'modules/terms-and-conditions/terms-and-conditions.tpl.html',
                data: {
                    pageTitle: 'Term And Conditions'
                }
            });
    });
