angular.module('app.sign-up-modal-form', [])
    .controller('SignUpModalFormCtrl', ['$scope', 'UserService', 'AuthService', 'enroller', 'tag', '$uibModalInstance', 'Notification', '$state',
        function ($scope, UserService, AuthService, enroller, tag, $uibModalInstance, Notification, $state) {
            var vm = this;

            $scope.userData = {
				username: '',
				first_name: '',
				last_name: '',
				email: '',
				password: '',
				phone: '',
                enroller: enroller ? enroller : '',
                tag: tag ? tag : ''
            };

			$scope.usernameFill = function () {
				if($scope.userData.email !== '') {
					$scope.userData.username = $scope.userData.email.indexOf('@') > 0 ? $scope.userData.email.split('@')[0] : $scope.userData.email;
				}
			};

            $scope.close = function () {
                $uibModalInstance.dismiss('close');
            };

            $scope.closeAndNew = function () {
                $uibModalInstance.dismiss('close');
                $state.go('user.billing');
            };

            $scope.send = function () {
                var proms = [];

                if ($scope.formCheckout.$valid) {
                    UserService.signUp($scope.userData)
                        .then(vm.success, vm.error);
                }

            };

            vm.success = function (res) {
				AuthService.login($scope.userData.username, $scope.userData.password)
					.then(function () {
						$uibModalInstance.close(res);
						Notification.success('Welcome to Dextrader!');
						$state.go('affiliates.how_it_works');
					});
            };

            vm.error = function (err) {
				if (err.data && angular.isDefined(err.data.error)) {
					if (angular.isArray(err.data.error)) {
						angular.forEach(err.data.error, function (e) {
							Notification.error(e);
						});
					}
					else if(angular.isObject( err.data.error )) {
						angular.forEach(err.data.error, function (value, key) {
							if (angular.isArray(value)) {
								angular.forEach(value, function (response) {
									Notification.error(response);
								});
							}
							else {
								Notification.error(value);
							}
						});
					}
					else {
						Notification.error(err.data.error);
					}
				} else {
					Notification.error('Oops! Something went wrong! Contact with support!');
				}
			};
        }]);

