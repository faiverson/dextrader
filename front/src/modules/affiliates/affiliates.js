angular.module('app.affiliates', ['ui.router', 'youtube-embed', 'app.affiliates-resources'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('affiliates', {
                url: '/affiliates',
                templateUrl: 'modules/affiliates/affiliates.tpl.html',
                controller: 'AffiliatesCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('affiliates.upgrade', {
                url: '/upgrade',
                templateUrl: 'modules/affiliates/upgrade.tpl.html',
                controller: 'AffiliatesUpgradeCtrl',
                data: {
                    pageTitle: 'Affiliate - Upgrade'
                }
            })
            .state('affiliates.how_it_works', {
                url: '/how-it-works',
                templateUrl: 'modules/affiliates/how-it-works.tpl.html',
                controller: 'HowItWorksCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            })
            .state('affiliates.links', {
                url: '/links',
                templateUrl: 'modules/affiliates/links.tpl.html',
                controller: 'MarketingLinksCtrl',
                data: {
                    pageTitle: 'Affiliates - Links',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            })
            .state('affiliates.training', {
                url: '/training',
                templateUrl: 'modules/affiliates/training.tpl.html',
                controller: 'TrainingCtrl',
                data: {
                    pageTitle: 'Affiliates - Training',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            })
            .state('affiliates.resources', {
                url: '/resources',
                templateUrl: 'modules/affiliates/resources.tpl.html',
                controller: 'ResourcesCtrl',
                data: {
                    pageTitle: 'Affiliates - Resources',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            })
            .state('affiliates.commissions', {
                url: '/commissions',
                templateUrl: 'modules/affiliates/commissions.tpl.html',
                controller: 'CommissionsCtrl',
                data: {
                    pageTitle: 'Affiliates - Commissions',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            })
            .state('affiliates.payments', {
                url: '/payments',
                templateUrl: 'modules/affiliates/payments.tpl.html',
                controller: 'PaymentsCtrl',
                data: {
                    pageTitle: 'Affiliates - Payments',
                    permission: 'user.view',
                    redirectTo: 'affiliates.upgrade'
                }
            });
    })

    .controller('AffiliatesCtrl', ['$scope', '$state', 'AuthService', function ($scope, $state, AuthService) {

        $scope.isLoggedIn = AuthService.isLoggedIn;

        if (!AuthService.isLoggedIn()) {
            $state.go('affiliates.upgrade');
        }else{
            $state.go('affiliates.how_it_works');
        }

    }])

    .controller('AffiliatesUpgradeCtrl', ['$state', function ($state) {

    }])

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

    .controller('CommissionsCtrl', ['$scope', 'CommissionService', 'Notification', function ($scope, CommissionService, Notification) {

        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 5
        };

        $scope.filters = {
            from: {
                format: 'dd MMM yyyy'
            },
            to: {
                format: 'dd MMM yyyy'
            },
            apply: function () {
                //TODO call api
            }
        };

        vm.getCommissionTotals = function () {
            function success(res) {
                $scope.commissionTotals = res.data;
            }

            function error(res) {
                Notification.error('Ups! there was an error trying to load commission totals!');
            }

            CommissionService.getCommissionTotals()
                .then(success, error);
        };

        vm.getCommissions = function () {
            function success(res) {
                $scope.commissions = res.data;
            }

            function error(res) {
                Notification.error('Ups! there was an error trying to load commissions!');
            }

            CommissionService.getCommissions()
                .then(success, error);
        };

        vm.init = function () {
            vm.getCommissionTotals();
            vm.getCommissions();
        };

        vm.init();

    }])

    .controller('PaymentsCtrl', ['$scope', function ($scope) {

    }]);