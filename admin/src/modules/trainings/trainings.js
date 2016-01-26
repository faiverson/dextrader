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

            $scope.save = function () {
                $scope.$broadcast('show-errors-check-validity');

                if ($scope.trainingForm.$valid) {

                    $scope.training.time = moment($scope.video_time).format('HH:mm:ss');
                    $scope.training.unlock_at = moment($scope.video_time).diff(moment($scope.unlock_at), 'seconds');

                    TrainingsService.save($scope.training)
                        .then(vm.success, vm.error);
                }
            };

            $scope.parseYTId = function () {
                $scope.training.video_id = youtubeEmbedUtils.getIdFromURL($scope.url);
            };

            $scope.$on('youtube.player.ready', function ($event, player) {
                // play it again
                var defaultUnlockTime = (player.getDuration() - Math.round(player.getDuration() * 10 / 100, 0));

                $scope.video_time = moment().hour(0).minute(0).seconds(0).add(player.getDuration(), 's');
                $scope.training.time = moment().hour(0).minute(0).seconds(0).add(player.getDuration(), 's').format('HH:mm:ss');

                $scope.unlock_at = moment().hour(0).minute(0).seconds(0).add(defaultUnlockTime, 's');
                $scope.training.unlock_at = defaultUnlockTime;
            });

            vm.getTrainingForEdit = function (id) {
                TrainingsService.getOne(id)
                    .then(function (res) {
                        $scope.training = res.data;
                    });
            };

            vm.init = function () {
                if (angular.isDefined($stateParams.id)) {
                    vm.getTrainingForEdit($stateParams.id);
                }
            };

            vm.success = function (res) {
                Notification.success('Provider created successfully!');
                $state.go('trainings.list');
            };

            vm.error = function (err) {
                Notification.error("Ups! there was an error trying to save the provider!");
            };

            vm.init();
        }])

    .controller('TrainingsListCtrl', ['$scope', 'TrainingsService', 'Notification', '$uibModal', function ($scope, TrainingsService, Notification, $uibModal) {
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
                    tr.unlock_at = moment(tr.time, 'HH:mm:SS').subtract(tr.unlock_at, 'seconds').format('HH:mm:SS');
                });
                $scope.trainings = res.data;
            }

            function error(err) {
                Notification.error('Ups! there was an error trying to load providers!');
            }

            TrainingsService.query(params, $scope.type)
                .then(success, error);
        };

        vm.init = function () {
            $scope.getTrainings();
        };

        vm.init();
    }]);
