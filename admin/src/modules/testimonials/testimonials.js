angular.module('app.testimonials', ['ui.router', 'ngFileUpload'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('testimonials', {
                url: '/testimonials',
                templateUrl: 'modules/testimonials/testimonials.tpl.html',
                controller: 'TestimonialsCtrl',
                data: {
                    pageTitle: 'Testimonials'
                }
            })
            .state('testimonials.list', {
                url: '/list',
                templateUrl: 'modules/testimonials/testimonials.list.tpl.html',
                controller: 'TestimonialsListCtrl',
                data: {
                    pageTitle: 'Testimonials - List'
                }
            })
            .state('testimonials.new', {
                url: '/new',
                templateUrl: 'modules/testimonials/testimonials.form.tpl.html',
                controller: 'TestimonialsFormCtrl',
                data: {
                    pageTitle: 'Testimonials - New'
                }
            })
            .state('testimonials.edit', {
                url: '/edit/:id',
                templateUrl: 'modules/testimonials/testimonials.form.tpl.html',
                controller: 'TestimonialsFormCtrl',
                data: {
                    pageTitle: 'Testimonials - Edit'
                }
            });
    })

    .controller('TestimonialsCtrl', ['$scope', function ($scope) {

    }])
    .controller('TestimonialsFormCtrl', ['$scope', '$state', '$stateParams', 'TestimonialsService', 'Notification', 'Upload', '$site-configs', 'AuthService',
        function ($scope, $state, $stateParams, TestimonialsService, Notification, Upload, $configs, AuthService) {
            var vm = this;

            $scope.image = '/assets/images/image-placeholder.gif';

            $scope.uploadAndSave = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.testimonialForm.$valid) {

                    if ($scope.testimonialForm.file.$valid && $scope.file) {
                        Notification.info('Processing image!');

                        $scope.upload($scope.file)
                            .then(function (resp) {
                                $scope.testimonial.image =  resp.data.data.filename;
                                $scope.save();
                            }, function (resp) {
                                Notification.error('Ups! something went wrong trying to save the image!');
                            });
                    } else {
                        $scope.save();
                    }
                }
            };

            $scope.save = function () {

                function success(res) {
                    Notification.success('Provider created successfully!');
                    $state.go('testimonials.list');
                }

                function error(err) {
                    Notification.error("Ups! there was an error trying to save the provider!");
                }

                TestimonialsService.save($scope.testimonial)
                    .then(success, error);
            };

            $scope.upload = function (file) {
                return Upload.upload({
                    url: $configs.API_BASE_URL + 'files/uploads',
                    data: {file: file},
                    headers: {'Authentication': AuthService.getUserToken()}
                });
            };

            vm.getTestimonialForEdit = function (id) {
                TestimonialsService.getOne(id)
                    .then(function (res) {
                        $scope.testimonial = res.data;
                        $scope.image = 'http://local.dextrader.com/assets/images/' + $scope.testimonial.image;
                    });
            };

            vm.init = function () {
                if(angular.isDefined($stateParams.id)){
                    vm.getTestimonialForEdit($stateParams.id);
                }
            };

            vm.init();
        }])

    .controller('TestimonialsListCtrl', ['$scope', 'TestimonialsService', 'Notification', '$uibModal', function ($scope, TestimonialsService, Notification, $uibModal) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getTestimonials();
            }
        };

        $scope.sortBy = {
            column: 1,
            dir: 'asc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getTestimonials();
            }
        };

        $scope.open = function (testimonial) {

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'TestimonialView.html',
                controller: ['$scope', 'testimonial', '$uibModalInstance', function($scope, testimonial, $uibModalInstance){
                    $scope.testimonial = testimonial;

                    $scope.close = function () {
                        $uibModalInstance.close();
                    };
                }],
                resolve: {
                    testimonial: function () {
                        return testimonial;
                    }
                }
            });
        };

        vm.getTestimonials = function () {

            var params = {
                start: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                length: $scope.pagination.itemsPerPage,
                sortBy: $scope.sortBy.column,
                sortDir: $scope.sortBy.dir
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.totalItems;
                $scope.testimonials = res.data;
            }

            function error(err) {
                Notification.error('Ups! there was an error trying to load providers!');
            }

            TestimonialsService.query(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getTestimonials();
        };

        vm.init();
    }]);
