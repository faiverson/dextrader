angular.module('app.shared-directives', [])

    .directive('countDown', ['$interval', function ($interval) {
        return {
            restrict: 'E',
            scope: {
                totalSeconds: '=',
                onFinish: '&',
                onChange: '&'
            },
            replace: true,
            template: '<div class="udt-timer"><span data-ng-bind="minutes"></span> : <span data-ng-bind="seconds"></span></div>',
            link: function ($scope, $elem, $attrs) {

                var duration = moment.duration($scope.totalSeconds * 1000, 'milliseconds');

                $scope.update = function () {
                    duration = moment.duration(duration - 1000, 'milliseconds');
                    $scope.minutes = (duration.minutes() < 10) ? '0' + duration.minutes() : duration.minutes();
                    $scope.seconds = (duration.seconds() < 10) ? '0' + duration.seconds() : duration.seconds();

                    if (parseInt($scope.minutes, 0) === 0 && parseInt($scope.seconds, 0) === 0) {
                        $interval.cancel($scope.interval);

                        if (angular.isFunction($scope.onFinish)) {
                            $scope.onFinish();

                            return;
                        }
                    }

                    if (angular.isFunction($scope.onChange)) {
                        $scope.onChange({remainSeconds: $scope.totalSeconds});
                    }

                    $scope.totalSeconds--;
                };

                $scope.start = function () {
                    $scope.interval = $interval($scope.update, 1000);
                };

                $scope.init = function () {
                    if (angular.isNumber($scope.totalSeconds) && $scope.totalSeconds > 0) {
                        $scope.start();
                    }
                };

                $scope.init();
            }
        };
    }])
    .directive('ngCsrc', ['$site-configs', function ($configs) {
        return {
            priority: 99,
            link: function (scope, element, attr) {
                attr.$observe('ngCsrc', function (value) {
                    if (!value) {
                        return;
                    }

                    attr.$set('src', $configs.DASHBOARD_URL + value);
                });
            }
        };
    }]);
