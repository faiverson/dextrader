angular.module('app.affiliates', ['ui.router', 'youtube-embed'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('affiliates', {
                url: '/affiliates',
                templateUrl: 'modules/affiliates/affiliates.tpl.html',
                controller: 'HowItWorksCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('affiliates.how_it_works', {
                url: '/how-it-works',
                templateUrl: 'modules/affiliates/how-it-works.tpl.html',
                controller: 'HowItWorksCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('affiliates.links', {
                url: '/links',
                templateUrl: 'modules/affiliates/links.tpl.html',
                controller: 'LinksCtrl',
                data: {
                    pageTitle: 'Affiliates - Links'
                }
            })
            .state('affiliates.training', {
                url: '/training',
                templateUrl: 'modules/affiliates/training.tpl.html',
                controller: 'TrainingCtrl',
                data: {
                    pageTitle: 'Affiliates - Training'
                }
            })
            .state('affiliates.resources', {
                url: '/resources',
                templateUrl: 'modules/affiliates/resources.tpl.html',
                controller: 'ResourcesCtrl',
                data: {
                    pageTitle: 'Affiliates - Resources'
                }
            })
            .state('affiliates.commissions', {
                url: '/commissions',
                templateUrl: 'modules/affiliates/commissions.tpl.html',
                controller: 'CommissionsCtrl',
                data: {
                    pageTitle: 'Affiliates - Commissions'
                }
            })
            .state('affiliates.payments', {
                url: '/payments',
                templateUrl: 'modules/affiliates/payments.tpl.html',
                controller: 'PaymentsCtrl',
                data: {
                    pageTitle: 'Affiliates - Payments'
                }
            });
    })

    .controller('HowItWorksCtrl', ['$scope', function ($scope) {
        $scope.youTubeVideoId = "lYKRPzOi1zI";

        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])
    .controller('LinksCtrl', ['$scope', function ($scope) {
        $scope.youTubeVideoId = "lYKRPzOi1zI";
        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])
    .controller('TrainingCtrl', ['$scope', function ($scope) {
        $scope.youTubeVideoId = "lYKRPzOi1zI";
        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])
    .controller('ResourcesCtrl', ['$scope', function ($scope) {

    }])
    .controller('CommissionsCtrl', ['$scope', function ($scope) {

    }])
    .controller('PaymentsCtrl', ['$scope', function ($scope) {

    }]);
