angular.module('app.affiliates', ['ui.router', 'youtube-embed', 'app.affiliates-resources'])
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
                controller: 'MarketingLinksCtrl',
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

    .controller('MarketingLinksCtrl', ['$scope', 'MarketingLinksService', 'Notification', function ($scope, MarketingLinksService, Notification) {
        var vm = this;

        vm.getMarketingLinks = function () {
            MarketingLinksService.query()
                .then(vm.success, vm.error);
        };

        vm.success = function (res) {
            $scope.mLinks = res.data;
        };

        vm.error = function (err) {
            Notification.error('Ups! Something went wrong, try again...');
        };

        vm.init = function () {
            vm.getMarketingLinks();
        };

        vm.init();

    }])

    .controller('TrainingCtrl', ['$scope', 'TrainingService', function ($scope, TrainingService) {

        var vm = this;

        vm.getTrainings = function () {
            TrainingService.queryAffiliates()
                .then(vm.successTrainingQuery, vm.errorTrainingQuery);
        };

        vm.successTrainingQuery = function (res) {
            $scope.trainings = res.data;

            if ($scope.trainings.length > 0) {
                $scope.setVideo($scope.trainings[0]);
            }
        };

        vm.errorTrainingQuery = function (err) {

        };

        vm.init = function () {
            vm.getTrainings();
        };

        vm.init();

        $scope.setVideo = function (video) {

            $scope.nextVideo = false;

            //reset player status
            if (angular.isDefined($scope.currentVideo)) {
                $scope.currentVideo.playing = false;
            }

            $scope.currentVideo = video;

            var index = $scope.trainings.indexOf(video);
            if (index > -1 && (index + 1) < $scope.trainings.length) {
                $scope.nextVideo = $scope.trainings[(index + 1)];
            }
        };

        $scope.$on('youtube.player.playing', function ($event, player) {
            $scope.currentVideo.playing = true;
        });

        $scope.$on('youtube.player.paused', function ($event, player) {
            $scope.currentVideo.playing = false;
        });

        $scope.$on('youtube.player.ended', function ($event, player) {
            $scope.currentVideo.playing = false;
        });

        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])

    .controller('ResourcesCtrl', ['$scope', 'AffiliateResources', 'Notification', '$templateCache', '$filter', function ($scope, AffiliateResources, Notification, $templateCache, $filter) {
        var vm = this;

        $scope.oneAtATime = true;

        $scope.select = function (resource) {

            if (angular.isDefined($scope.selectedResource)) {
                $scope.selectedResource.selected = false;
            }

            $scope.selectedResource = resource;

            $scope.selectedResource.selected = true;
            angular.forEach($scope.selectedResource.items, function (item) {
                item.text = $filter('htmlToPlaintext')($templateCache.get(item.templateUrl));
            });
        };

        $scope.copySuccess = function () {
            Notification.success('Text copied to clipboard!');
        };

        $scope.copyError = function () {
            Notification.success('Ups! something went wrong! select the text and copy manually');
        };

        vm.init = function () {
            $scope.resources = AffiliateResources;

            $scope.select($scope.resources[0]);
        };

        vm.init();
    }])

    .controller('CommissionsCtrl', ['$scope', function ($scope) {

    }])

    .controller('PaymentsCtrl', ['$scope', function ($scope) {

    }]);