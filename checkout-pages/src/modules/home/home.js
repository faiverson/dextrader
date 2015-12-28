angular.module('app.home', ['ui.router'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('home', {
                url: '/home',
                templateUrl: 'modules/home/home.tpl.html',
                controller: 'HomeCtrl',
                data: {
                    pageTitle: 'Home'
                }
            });
    })

    .controller('HomeCtrl', ['$scope', 'TestimonialsService', function ($scope, TestimonialsService) {
        var vm = this;

        vm.getTestimonials = function () {

            function success(res) {

                if(res.data.length > 1){
                    var half_length = Math.ceil(res.data.length / 2);
                    var leftSide = res.data.splice(0,half_length);
                    var rightSide = res.data;

                    $scope.testimonialsLeft = leftSide;
                    $scope.testimonialsRight = rightSide;
                }else{
                    $scope.testimonialsLeft = res.data;
                }

            }

            function error(err){
                console.log(err);
            }

            TestimonialsService.query()
                .then(success, error);
        };

        vm.init = function () {
            vm.getTestimonials();
        };

        vm.init();
    }]);