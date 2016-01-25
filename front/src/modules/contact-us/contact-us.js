angular.module('app.contact-us', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('contact_us', {
                url: '/contact-us',
                templateUrl: 'modules/contact-us/contact-us.tpl.html',
                data: {
                    pageTitle: 'Contact Us',
                    isPublic: true
                }
            });
    });
