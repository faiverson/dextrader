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
                                Notification.error('Oops! something went wrong trying to save the image!');
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

                function error(response) {
					var txt = '';
					response = response.data;
					if(angular.isArray(response.error)) {
						angular.forEach(response.error, function(item) {
							txt += item + '<br>';
						});
					}
					else {
						txt += response.error;
					}
					Notification.error("Oops! " + txt);
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
                        $scope.image = $configs.DASHBOARD_URL + '/assets/images/' + $scope.testimonial.image;
                    });
            };

            vm.init = function () {
                if(angular.isDefined($stateParams.id)){
                    vm.getTestimonialForEdit($stateParams.id);
                }
            };

            vm.init();
        }])

    .controller('TestimonialsListCtrl', ['$scope', 'TestimonialsService', 'Notification', '$uibModal', 'modalService', function ($scope, TestimonialsService, Notification, $uibModal, modalService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                $scope.getTestimonials();
            }
        };

        $scope.openDeleteConfirm = function (id) {
            var modalOptions = {
                closeButtonText: 'Cancel',
                actionButtonText: 'Delete Testimonial?',
                headerText: 'Delete Signal?',
                bodyText: 'Are you sure you want to delete this Testimonial?'
            };

            modalService.showModal({}, modalOptions)
                .then(function (result) {
                    TestimonialsService.destroy(id).then(vm.successDelete, vm.errorDelete);
                });
        };

        vm.successDelete = function (res) {
            $scope.getTestimonials();
            Notification.success('Testimonial was removed successfully!');
        };

        vm.errorDelete = function (err) {
            Notification.error('Oops! there was an error trying to remove this Testimonial!');
        };

        $scope.sortBy = {};

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

        $scope.getTestimonials = function () {

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
                Notification.error('Oops! there was an error trying to load providers!');
            }

            TestimonialsService.query(params)
                .then(success, error);
        };

        vm.init = function () {
            $scope.getTestimonials();
        };

        vm.init();
    }]);
