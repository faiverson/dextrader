angular.module('app.sign-up-modal-form', [])
    .controller('SignUpModalFormCtrl', ['$scope', 'UserService', 'enroller', 'tag', '$uibModalInstance', 'Notification', '$state',
        function ($scope, UserService, enroller, tag, $uibModalInstance, Notification, $state) {
            var vm = this;

            $scope.userData = {};

            $scope.userData = {
                enroller: enroller,
                tag: tag
            };

            vm.init = function () {

            };

            $scope.closeAndNew = function () {
                $uibModalInstance.dismiss('close');
                $state.go('user.billing');
            };

            $scope.send = function () {
                var proms = [];
                $scope.showAgreementWarning = angular.isUndefined($scope.userData.terms);

                if ($scope.formCheckout.$valid) {

                    UserService.signUp($scope.userData)
                        .then(vm.success, vm.error);

                } else {
                    $scope.$broadcast('show-errors-check-validity');
                }

            };

            vm.success = function (res) {
                $uibModalInstance.close(res);
            };

            vm.error = function (err) {
                if (err.data && angular.isDefined(err.data.error)) {
                    if (angular.isArray(err.data.error)) {
                        angular.forEach(err.data.error, function (e) {
                            Notification.error(e);
                        });
                    } else {
                        Notification.error(err.data.error);
                    }

                } else {
                    Notification.error('Ups! something went wrong! please try again!');
                }

            };

            vm.init();
        }]);

