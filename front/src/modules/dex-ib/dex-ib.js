angular.module('app.dex_ib', ['ui.router', 'youtube-embed'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_ib', {
                url: '/dexib',
                templateUrl: 'modules/dex-ib/dex-ib.tpl.html',
                controller: 'DexIBCtrl',
                data: {
                    pageTitle: 'Dex IB'
                }
            })
            .state('dex_ib.upgrade', {
                url: '/upgrade',
                templateUrl: 'modules/dex-ib/dex-ib.upgrade.tpl.html',
                controller: 'DexIBUpgradeCtrl',
                data: {
                    pageTitle: 'Dex IB - Upgrade'
                }
            })
            .state('dex_ib.certification_training', {
                url: '/certification-training',
                templateUrl: 'modules/dex-ib/dex-ib.certification-training.tpl.html',
                controller: 'CertificationTrainingCtrl',
                data: {
                    pageTitle: 'Dex IB - Certification Training',
                    permission: 'product.ib',
                    redirectTo: 'dex_ib.upgrade'
                }
            })
            .state('dex_ib.live_signals', {
                url: '/live-signals',
                templateUrl: 'modules/dex-ib/dex-ib.live-signals.tpl.html',
                controller: 'LiveSignalsCtrl',
                data: {
                    pageTitle: 'Dex IB - Live Signals',
                    permission: 'product.ib.training',
                    redirectTo: 'dex_ib.certification_training'
                }
            })
            .state('dex_ib.dex_score', {
                url: '/dex-score',
                templateUrl: 'modules/dex-ib/dex-ib.dex-score.tpl.html',
                controller: 'DexScoreCtrl',
                data: {
                    pageTitle: 'Dex IB - Dex Score',
                    permission: 'product.ib.training',
                    redirectTo: 'dex_ib.live_signals'
                }
            })
            .state('dex_ib.dex_ib_pro', {
                url: '/dexib-pro',
                templateUrl: 'modules/dex-ib/dex-ib.dex-ib-pro.tpl.html',
                controller: 'DexIBProCtrl',
                data: {
                    pageTitle: 'Dex IB - Pro',
                    permission: 'product.ib.dex_score',
                    redirectTo: 'dex_ib.dex_score'
                }
            })
            .state('dex_ib.dex_ib_pro_upgrade', {
                url: '/dexib-pro-upgrade',
                templateUrl: 'modules/dex-ib/dex-ib.dex-ib-pro.upgrade.tpl.html',
                controller: 'DexIBProUpgradeCtrl',
                data: {
                    pageTitle: 'Dex IB - Pro - Upgrade',
                    permission: 'product.ib',
                    redirectTo: 'dashboard'
                }
            })

            .state('dex_ib_sales', {
                url: '/ib',
                templateUrl: 'modules/dex-ib/dex-ib.sales.tpl.html',
                controller: 'DexIBSalesCtrl',
                data: {
                    pageTitle: 'Dex IB',
                    bodyClass: 'dex-in-sales',
                    isPublic: true
                }
            })

            .state('dex_ib_sales1', {
                url: '/ib/:sponsor',
                templateUrl: 'modules/dex-ib/dex-ib.sales.tpl.html',
                controller: 'DexIBSalesCtrl',
                data: {
                    pageTitle: 'Dex IB',
                    bodyClass: 'dex-in-sales',
                    isPublic: true
                }
            })

            .state('dex_ib_sales2', {
                url: '/ib/:sponsor/:tag',
                templateUrl: 'modules/dex-ib/dex-ib.sales.tpl.html',
                controller: 'DexIBSalesCtrl',
                data: {
                    pageTitle: 'Dex IB',
                    bodyClass: 'dex-in-sales',
                    isPublic: true
                }
            });

    })

    .controller('DexIBCtrl', ['$scope', '$state', 'AuthService', function ($scope, $state, AuthService) {
        $scope.isLoggedIn = AuthService.isLoggedIn;

        if (AuthService.isLoggedIn()) {
            $state.go('dex_ib.certification_training');
        } else {
            $state.go('dex_ib.upgrade');
        }

    }])

    .controller('DexIBUpgradeCtrl', ['$state', function ($state) {

    }])

    .controller('CertificationTrainingCtrl', ['$scope', '$filter', '$interval', 'TrainingService', '$state', function ($scope, $filter, $interval, TrainingService, $state) {

        var vm = this;

        vm.getTrainings = function () {
            TrainingService.queryDexIB()
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

        vm.startTimer = function () {
            vm.stopTime = $interval(vm.updateTime, 1000);
        };

        vm.stopTimer = function () {
            $interval.cancel(vm.stopTime);
        };

        vm.updateTime = function () {
            $scope.currentVideo.watched_seconds++;

            if ($scope.currentVideo.watched_seconds > $scope.currentVideo.unlock_at) {
                vm.unlock($scope.currentVideo.id);
                vm.stopTimer();
            }
        };

        vm.unlock = function (id) {

            function success() {
                $scope.currentVideo.completed = 1;
            }

            function error() {

            }

            TrainingService.unlockTraining(id)
                .then(success, error);
        };

        vm.init = function () {
            vm.getTrainings();
        };

        vm.init();

        $scope.setVideo = function (video) {

            if ($scope.canReproduce(video)) {

                vm.stopTimer();

                $scope.nextVideo = false;

                //reset player status
                if (angular.isDefined($scope.currentVideo)) {
                    $scope.currentVideo.playing = false;
                }

                $scope.currentVideo = video;
                $scope.currentVideo.watched_seconds = 0;

                var index = $scope.trainings.indexOf(video);
                if (index > -1 && (index + 1) < $scope.trainings.length) {
                    $scope.nextVideo = $scope.trainings[(index + 1)];
                }
            }
        };

        $scope.canReproduce = function (training) {

            return $scope.trainings.indexOf(training) === 0 || $scope.trainings[$scope.trainings.indexOf(training) - 1].completed;
        };

        $scope.isTrainingComplete = function () {
            var completes = $filter('filter')($scope.trainings, {completed: 1}, true);

            return angular.isDefined(completes) && completes.length === $scope.trainings.length;
        };

        $scope.nextStep = function () {
            if ($scope.isTrainingComplete()) {
                $state.go('dex_ib.live_signals');
            }
        };

        $scope.$on('youtube.player.playing', function ($event, player) {
            vm.startTimer();
            $scope.currentVideo.playing = true;
        });

        $scope.$on('youtube.player.paused', function ($event, player) {
            vm.stopTimer();
            $scope.currentVideo.playing = false;
        });

        $scope.$on('youtube.player.ended', function ($event, player) {
            vm.stopTimer();
            $scope.currentVideo.playing = false;
        });

        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])

    .controller('LiveSignalsCtrl', ['$scope', function ($scope) {

    }])

    .controller('DexScoreCtrl', ['$scope', 'ProvidersService', 'Notification', function ($scope, ProvidersService, Notification) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getProvider();
            }
        };

        $scope.sortBy = {
            column: 1,
            dir: 'asc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getProvider();
            }
        };

        vm.getProvider = function () {

            var params = {
                start: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                length: $scope.pagination.itemsPerPage,
                sortBy: $scope.sortBy.column,
                sortDir: $scope.sortBy.dir
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.totalItems;
                $scope.providers = res.data.items;
            }

            function error(err) {
                Notification.error('Ups! there was an error trying to load providers!');
            }

            ProvidersService.query(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getProvider();
        };

        vm.init();
    }])

    .controller('DexIBProCtrl', ['$scope', function ($scope) {

    }])
    .controller('DexIBProUpgradeCtrl', ['$scope', function ($scope) {
        $scope.videoId = 'lYKRPzOi1zI';
    }])

    .controller('DexIBSalesCtrl', ['$scope', 'TestimonialsService', '$stateParams', function ($scope, TestimonialsService, $stateParams) {
        var vm = this;

        $scope.orderNowLink = '/ib';

        vm.getTestimonials = function () {

            function success(res) {

                if (res.data.length > 1) {
                    var half_length = Math.ceil(res.data.length / 2);
                    var leftSide = res.data.splice(0, half_length);
                    var rightSide = res.data;

                    $scope.testimonialsLeft = leftSide;
                    $scope.testimonialsRight = rightSide;
                } else {
                    $scope.testimonialsLeft = res.data;
                }

            }

            function error(err) {
                console.log(err);
            }

            TestimonialsService.query()
                .then(success, error);
        };

        vm.init = function () {
            vm.getTestimonials();

            if (angular.isDefined($stateParams.sponsor)) {
                $scope.orderNowLink += '/' + $stateParams.sponsor;
            }

            if (angular.isDefined($stateParams.tag)) {
                $scope.orderNowLink += '/' + $stateParams.tag;
            }
        };

        vm.init();
    }]);