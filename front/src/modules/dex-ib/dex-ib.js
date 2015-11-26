angular.module('app.dex_ib', ['ui.router', 'youtube-embed'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dex_ib', {
                url: '/dexib',
                templateUrl: 'modules/dex-ib/dex-ib.tpl.html',
                controller: 'DexIBCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('dex_ib.certification_training', {
                url: '/certification-training',
                templateUrl: 'modules/dex-ib/dex-ib.certification-training.tpl.html',
                controller: 'CertificationTrainingCtrl',
                data: {
                    pageTitle: 'Dex IB - Certification Training'
                }
            })
            .state('dex_ib.live_signals', {
                url: '/live-signals',
                templateUrl: 'modules/dex-ib/dex-ib.live-signals.tpl.html',
                controller: 'LiveSignalsCtrl',
                data: {
                    pageTitle: 'Dex IB - Live Signals'
                }
            })
            .state('dex_ib.dex_score', {
                url: '/dex-score',
                templateUrl: 'modules/dex-ib/dex-ib.dex-score.tpl.html',
                controller: 'DexScoreCtrl',
                data: {
                    pageTitle: 'Dex IB - Dex Score'
                }
            })
            .state('dex_ib.dex_ib_pro', {
                url: '/dexib-pro',
                templateUrl: 'modules/dex-ib/dex-ib.dex-ib-pro.tpl.html',
                controller: 'DexIBProCtrl',
                data: {
                    pageTitle: 'Dex IB - Pro'
                }
            });
    })

    .controller('DexIBCtrl', ['$state', function ($state) {
        $state.go('dex_ib.certification_training');
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

        vm.startTimer = function(){
            vm.stopTime = $interval(vm.updateTime, 1000);
        };

        vm.stopTimer = function(){
            $interval.cancel(vm.stopTime);
        };

        vm.updateTime = function () {
            $scope.currentVideo.watched_seconds++;

            if($scope.currentVideo.watched_seconds > $scope.currentVideo.unlock_at){
                vm.unlock($scope.currentVideo.id);
                vm.stopTimer();
            }
        };

        vm.unlock = function(id){
            TrainingService.unlockTraining(id)
                .then(success, error);

            function success(){
                $scope.currentVideo.completed = 1;
            }

            function error(){

            }
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

            return $scope.trainings.indexOf(training) == 0 || $scope.trainings[$scope.trainings.indexOf(training) - 1].completed;
        };

        $scope.isTrainingComplete = function () {
            var completes = $filter('filter')($scope.trainings, { completed: 1 }, true);

            return angular.isDefined(completes) && completes.length === $scope.trainings.length;
        };

        $scope.nextStep = function(){
            if($scope.isTrainingComplete()){
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

    .controller('DexScoreCtrl', ['$scope', function ($scope) {

    }])

    .controller('DexIBProCtrl', ['$scope', function ($scope) {

    }]);