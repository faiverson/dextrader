angular.module('app.footer', [])
    .controller('FooterCtrl', ['$scope', 'ChatService', 'AuthService', function ($scope, ChatService, AuthService) {

        $scope.isLoggedIn = AuthService.isLoggedIn;

        $scope.loadChat = function () {
            ChatService.show();
        };
    }]);
