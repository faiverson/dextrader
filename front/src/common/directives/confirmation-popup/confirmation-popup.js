angular.module('app.confirmation-popup', [])
    .directive('confirmation', [function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'common/directives/confirmation-popup/confirmation-popup.tpl.html',
            scope: {
                onConfirm: '&'
            },
            link: function ($scope, $elem, $attrs) {
				$scope.text = $attrs['text'] || 'Remove';

                $scope.confirm = function () {
                    if(angular.isFunction($scope.onConfirm)){
                        $scope.onConfirm();
                    }
                };

                $scope.cancel = function () {
                    $scope.isOpen = false;
                };
            }
        };
    }]);
