angular.module('app.dashboard', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('dashboard', {
                url: '/dashboard',
                templateUrl: 'modules/dashboard/dashboard.tpl.html',
                controller: 'DashboardCtrl',
                data: {
                    pageTitle: 'Dashboard',
                    bodyClass: 'page-dashboard'
                }
            });
    })

    .controller('DashboardCtrl', ['$scope', '$uibModal', function ($scope, $uibModal) {

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