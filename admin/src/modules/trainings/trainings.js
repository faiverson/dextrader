angular.module('app.trainings', ['ui.router', 'youtube-embed'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('trainings', {
                url: '/trainings',
                templateUrl: 'modules/trainings/trainings.tpl.html',
                controller: 'TrainingsCtrl',
                data: {
                    pageTitle: 'Trainings',
                    permission: 'trainings',
                    redirectTo: 'dashboard'
                }
            })
            .state('trainings.list', {
                url: '/list',
                templateUrl: 'modules/trainings/trainings.list.tpl.html',
                controller: 'TrainingsListCtrl',
                data: {
                    pageTitle: 'Trainings - List'
                }
            })
            .state('trainings.new', {
                url: '/new',
                templateUrl: 'modules/trainings/trainings.form.tpl.html',
                controller: 'TrainingsFormCtrl',
                data: {
                    pageTitle: 'Trainings - New'
                }
            })
            .state('trainings.edit', {
                url: '/edit/:id',
                templateUrl: 'modules/trainings/trainings.form.tpl.html',
                controller: 'TrainingsFormCtrl',
                data: {
                    pageTitle: 'Trainings - Edit'
                }
            });
    })

    .controller('TrainingsCtrl', ['$scope', function ($scope) {

    }])
    .controller('TrainingsFormCtrl', ['$scope', '$state', '$stateParams', 'TrainingsService', 'Notification', 'youtubeEmbedUtils',
        function ($scope, $state, $stateParams, TrainingsService, Notification, youtubeEmbedUtils) {
            var vm = this;

            $scope.training = {};

            $scope.playerVars = {};

			$scope.selectedType = 0;
			$scope.selectedOrder= 0;

            $scope.save = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.trainingForm.$valid) {

                    $scope.training.type = $scope.selectedType;
                    $scope.training.list_order = $scope.selectedOrder;
                    $scope.training.time = moment($scope.video_time).format('HH:mm:ss');
                    $scope.training.unlock_at = (parseInt(moment($scope.unlock_at).format('HH')) * 60 *60) + (parseInt(moment($scope.unlock_at).format('mm')) *60) + parseInt(moment($scope.unlock_at).format('ss'));

                    TrainingsService.save($scope.training)
                        .then(vm.success, vm.error);
                }
            };

            $scope.parseYTId = function () {
                $scope.changeVideo = true;
                $scope.training.video_id = youtubeEmbedUtils.getIdFromURL($scope.url);
            };

            $scope.$on('youtube.player.ready', function ($event, player) {
                // play it again
                if(angular.isUndefined($scope.training.training_id) || $scope.changeVideo){
                    var defaultUnlockTime = (player.getDuration() - Math.round(player.getDuration() * 10 / 100, 0));

                    $scope.video_time = moment().hour(0).minute(0).seconds(0).add(player.getDuration(), 's');
                    $scope.training.time = moment().hour(0).minute(0).seconds(0).add(player.getDuration(), 's').format('HH:mm:ss');

                    $scope.unlock_at = moment().hour(0).minute(0).seconds(0).add(defaultUnlockTime, 's');
                    $scope.training.unlock_at = defaultUnlockTime;

                    $scope.changeVideo = false;
                }
            });

            vm.getTrainingForEdit = function (id) {
                TrainingsService.getOne(id)
                    .then(function (res) {
						var video_time;
						$scope.training = res.data;
						$scope.total = res.data.total;
						video_time = moment($scope.training.time, 'HH:mm:ss');
						$scope.selectedType = $scope.training.type;
						$scope.selectedOrder = $scope.training.list_order;
						$scope.video_time = moment().hour(video_time.format('HH')).minutes(video_time.format('mm')).seconds(video_time.format('ss'));
                        $scope.unlock_at = moment().hour(0).minutes(0).seconds(0).add($scope.training.unlock_at, 'seconds');
                    });
            };

            vm.init = function () {
				TrainingsService.query({}, 'orders')
					.then(function (response) {
						var total = response.data;
						$scope.types = [];
						angular.forEach(total, function(total, key) {
							var i,
								array = [];

							if (!angular.isDefined($stateParams.id)) {
								total += 1;
							}

							for(i = total; i > 0; i--) {
								array.push(i);
							}
							$scope.types.push({
								id: key.toLowerCase(),
								name: key,
								order_list: array
							});
						});

						$scope.selectedType = $scope.types[0].id;
						$scope.selectedOrder = $scope.types[0].order_list[0];
					});



                if (angular.isDefined($stateParams.id)) {
                    vm.getTrainingForEdit($stateParams.id);
                }
            };

			$scope.changeType = function() {
				angular.forEach($scope.types, function(item, key) {
					if(item.id === $scope.selectedType) {
						$scope.selectedOrder = item.order_list[0];
					}
				});
			};

            vm.success = function (res) {
                Notification.success('Training created successfully!');
                $state.go('trainings.list');
            };

            vm.error = function (response) {
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
			};

            vm.init();
        }])

    .controller('TrainingsListCtrl', ['$scope', 'TrainingsService', 'Notification', 'modalService', function ($scope, TrainingsService, Notification, modalService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getTrainings();
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

                vm.getTrainings();
            }
        };

        $scope.type = 'certification';

        $scope.getTrainings = function () {

            var params = {
                start: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                length: $scope.pagination.itemsPerPage,
                sortBy: $scope.sortBy.column,
                sortDir: $scope.sortBy.dir
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.totalItems;

                angular.forEach(res.data, function (tr) {
                    tr.unlock_at = moment().hour(0).minutes(0).seconds(0).add(tr.unlock_at, 'seconds').format('HH:mm:ss');
                });
                $scope.trainings = res.data;
            }

            function error(err) {
                Notification.error('Oops! there was an error trying to load providers!');
            }

            TrainingsService.query(params, $scope.type)
                .then(success, error);
        };

        $scope.openDeleteConfirm = function (id) {
            var modalOptions = {
                closeButtonText: 'Cancel',
                actionButtonText: 'Delete Training?',
                headerText: 'Delete Signal?',
                bodyText: 'Are you sure you want to delete this Training?'
            };

            modalService.showModal({}, modalOptions)
                .then(function (result) {
                    TrainingsService.destroy(id).then(vm.successDelete, vm.errorDelete);
                });
        };

        vm.successDelete = function (res) {
            $scope.getTrainings();
            Notification.success('Training was removed successfully!');
        };

        vm.errorDelete = function (err) {
            Notification.error('Oops! there was an error trying to remove this Training!');
        };

        vm.init = function () {
            $scope.getTrainings();
        };

        vm.init();
    }]);
