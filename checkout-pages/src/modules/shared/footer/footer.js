angular.module('app.footer', [])
    .controller('FooterCtrl', ['$scope', 'ChatService', function ($scope, ChatService) {

        $scope.loadChat = function () {
            ChatService.show();
        };
    }]);