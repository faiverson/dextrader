angular.module('app.header', [])
    .controller('HeaderCtrl', ['$scope', '$state', 'AuthService', function ($scope, $state, AuthService) {

        $scope.isLoggedIn = AuthService.isLoggedIn;

        $scope.logout = function () {
            AuthService.logout()
                .then(function () {
                    $state.go('login');
                });
        };
    }]);
