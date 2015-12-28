angular.module('app.shared-directives', [])

    .directive('countDown', ['$interval', function ($interval) {
        return {
            restrict: 'E',
            scope: {
                totalSeconds: '='
            },
            replace: true,
            template: '<div class="udt-timer"><span data-ng-bind="minutes"></span> : <span data-ng-bind="seconds"></span></div>',
            link: function ($scope, $elem, $attrs) {
                var vm = this;

                var duration = moment.duration($scope.totalSeconds*1000, 'milliseconds');

                vm.seconds = $scope.seconds;

                vm.update = function () {
                    duration = moment.duration(duration - 1000, 'milliseconds');
                    $scope.minutes = (duration.minutes() < 10) ? '0' + duration.minutes() : duration.minutes();
                    $scope.seconds = (duration.seconds() < 10) ? '0' +  duration.seconds() : duration.seconds();

                    if(parseInt($scope.minutes, 0) === 0 && parseInt($scope.seconds, 0) === 0){
                        $interval.cancel(vm.interval);
                    }
                };

                vm.start = function () {
                    vm.interval = $interval(vm.update, 1000);
                };

                vm.init = function () {
                    vm.start();
                };

                vm.init();
            }
        };
    }])
    .directive('ngCsrc', ['$site-configs', function($configs) {
        return {
            priority: 99,
            link: function(scope, element, attr) {
                attr.$observe('ngCsrc', function(value) {
                    if (!value){
                        return;
                    }

                    attr.$set('src', $configs.DASHBOARD_URL + value);
                });
            }
        };
    }]);
