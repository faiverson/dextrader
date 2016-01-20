angular.module('app.start', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('start', {
                url: '/start',
                templateUrl: 'modules/start/start.tpl.html',
                controller: 'StartCtrl',
                data: {
                    pageTitle: 'Start',
                    bodyClass: 'page-dashboard',
                    isPublic: true
                }
            });
    })

    .controller('StartCtrl', ['$scope', '$uibModal', '$site-configs', function ($scope, $uibModal, $config) {

        $scope.secure_url = $config.SECURE_URL;

        $scope.openVideo = function () {
            var modalInstance = $uibModal.open({
                windowClass: 'dashboard-video-modal',
                animation: true,
                templateUrl: 'videoModal.html',
                controller: 'VideoModalCtrl',
                resolve: {
                    videoId: function () {
                        return 'L1v7hXEQhsQ';
                    }
                }
            });
        };
    }])

    .controller('VideoModalCtrl', ['$scope', 'videoId', '$uibModalInstance', function ($scope, videoId, $uibModalInstance) {
        $scope.video_id = videoId;
        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };

        $scope.close = function(){
            $uibModalInstance.dismiss('close');
        };
    }]);