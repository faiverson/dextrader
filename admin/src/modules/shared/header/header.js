angular.module('app.header', [])
    .controller('HeaderCtrl', ['$scope', '$state', 'AuthService', function ($scope, $state, AuthService) {

        $scope.isLoggedIn = AuthService.isLoggedIn();

        $scope.logout = function () {
            AuthService.logout()
                .then(function () {
                    $scope.isLoggedIn = AuthService.isLoggedIn();
                    $state.go('login');
                });
        };

        $scope.$on('user-login-success',function(){
            $scope.isLoggedIn = AuthService.isLoggedIn();
        });

    }]);
