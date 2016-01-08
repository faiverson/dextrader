angular.module('app.refund', [])
    .config(function config($stateProvider) {
        $stateProvider
            .state('refund', {
                url: '/refund-policy',
                templateUrl: 'modules/refund/refund.tpl.html',
                controller: 'RefundCtrl',
                data: {
                    pageTitle: 'Refund Policy',
                    isPublic: true
                }
            });
    })

    .controller('RefundCtrl', ['$scope', 'ChatService', function ($scope, ChatService) {
        $scope.loadChat = function () {
            ChatService.show();
        };
    }]);
