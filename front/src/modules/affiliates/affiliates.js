angular.module('app.affiliates', ['ui.router', 'youtube-embed', 'app.affiliates-resources', 'ngFileSaver'])
    .config(function config($stateProvider) {
        $stateProvider
            .state('affiliates', {
                url: '/affiliates',
                templateUrl: 'modules/affiliates/affiliates.tpl.html',
                controller: 'AffiliatesCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('affiliates.upgrade', {
                url: '/upgrade',
                templateUrl: 'modules/affiliates/upgrade.tpl.html',
                controller: 'AffiliatesUpgradeCtrl',
                data: {
                    pageTitle: 'Affiliate - Upgrade'
                }
            })
            .state('affiliates.how_it_works', {
                url: '/how-it-works',
                templateUrl: 'modules/affiliates/how-it-works.tpl.html',
                controller: 'HowItWorksCtrl',
                data: {
                    pageTitle: 'Affiliates - How it works'
                }
            })
            .state('affiliates.links', {
                url: '/links',
                templateUrl: 'modules/affiliates/links.tpl.html',
                controller: 'MarketingLinksCtrl',
                data: {
                    pageTitle: 'Affiliates - Links'
                }
            })
            .state('affiliates.training', {
                url: '/training',
                templateUrl: 'modules/affiliates/training.tpl.html',
                controller: 'TrainingCtrl',
                data: {
                    pageTitle: 'Affiliates - Training'
                }
            })
            .state('affiliates.resources', {
                url: '/resources',
                templateUrl: 'modules/affiliates/resources.tpl.html',
                controller: 'ResourcesCtrl',
                data: {
                    pageTitle: 'Affiliates - Resources'
                }
            })
            .state('affiliates.commissions', {
                url: '/commissions',
                templateUrl: 'modules/affiliates/commissions.tpl.html',
                controller: 'CommissionsCtrl',
                data: {
                    pageTitle: 'Affiliates - Commissions'
                }
            })
            .state('affiliates.downline', {
                url: '/downline',
                templateUrl: 'modules/affiliates/downline.tpl.html',
                controller: 'DownlineCtrl',
                data: {
                    pageTitle: 'Affiliates - Downline'
                }
            })
            .state('affiliates.payments', {
                url: '/payments',
                templateUrl: 'modules/affiliates/payments.tpl.html',
                controller: 'PaymentsCtrl',
                data: {
                    pageTitle: 'Affiliates - Payments'
                }
            })
            .state('affiliates.marketing_stats', {
                url: '/stats',
                templateUrl: 'modules/affiliates/marketing.stats.tpl.html',
                controller: 'MarketingStatsCtrl',
                data: {
                    pageTitle: 'Affiliates - Stats'
                }
            });
    })

    .controller('AffiliatesCtrl', ['$scope', '$state', 'AuthService', function ($scope, $state, AuthService) {

        $scope.isLoggedIn = AuthService.isLoggedIn;

        if (!AuthService.isLoggedIn()) {
            $state.go('affiliates.upgrade');
        } else {
            $state.go('affiliates.how_it_works');
        }

    }])

    .controller('AffiliatesUpgradeCtrl', ['$state', function ($state) {

    }])

    .controller('HowItWorksCtrl', ['$scope', 'EWalletService', 'Notification', '$site-configs', function ($scope, EWalletService, Notification, $configs) {
        $scope.youTubeVideoId = "WoZiQeIcCR0";

        $scope.createEWallet = function () {
            EWalletService.createEWallet()
                .then(function (res) {
                        if (angular.isDefined(res.data.code) && res.data.code === 'USERNAME_EXISTS') {
                            window.location.href = $configs.EWALLET_LOGIN;
                        } else {
                            Notification.success('Account has created successfully!');
                            window.location.href = $configs.EWALLET_LOGIN;
                        }
                    },
                    function (err) {
                        Notification.error(err.data);
                    });
        };

        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])

    .controller('MarketingLinksCtrl', ['$scope', 'MarketingLinksService', 'Notification', 'AuthService', function ($scope, MarketingLinksService, Notification, AuthService) {
        var vm = this;

        vm.getMarketingLinks = function () {
            MarketingLinksService.query()
                .then(vm.success, vm.error);
        };

        vm.success = function (res) {

            var user = AuthService.getLoggedInUser().username;

            angular.forEach(res.data, function (mLink) {
                mLink.link += '?user=' + user;
            });

            $scope.mLinks = res.data;
        };

        vm.error = function (err) {
            Notification.error('Oops! Something went wrong, try again...');
        };

        vm.init = function () {
            vm.getMarketingLinks();
        };

        vm.init();

    }])

    .controller('TrainingCtrl', ['$scope', 'TrainingService', 'FileSaver', 'Notification', function ($scope, TrainingService, FileSaver, Notification) {

        var vm = this;

        vm.getTrainings = function () {
            TrainingService.queryAffiliates()
                .then(vm.successTrainingQuery, vm.errorTrainingQuery);
        };

        vm.successTrainingQuery = function (res) {
            $scope.trainings = res.data;

            if ($scope.trainings.length > 0) {
                $scope.setVideo($scope.trainings[0]);
            }
        };

        vm.errorTrainingQuery = function (err) {

        };

        vm.init = function () {
            vm.getTrainings();
        };

        vm.init();

        $scope.download = function (training) {
            TrainingService.download(training.training_id)
                .then(function (res) {
                        var blob = new Blob([res], {type: "application/octet-stream"});
                        FileSaver.saveAs(blob, training.filename);

                    },
                    function (err) {
                        Notification.error('File not found!');
                    });
        };

        $scope.setVideo = function (video) {

            $scope.nextVideo = false;

            //reset player status
            if (angular.isDefined($scope.currentVideo)) {
                $scope.currentVideo.playing = false;
            }

            $scope.currentVideo = video;

            var index = $scope.trainings.indexOf(video);
            if (index > -1 && (index + 1) < $scope.trainings.length) {
                $scope.nextVideo = $scope.trainings[(index + 1)];
            }
        };

        $scope.$on('youtube.player.playing', function ($event, player) {
            $scope.currentVideo.playing = true;
        });

        $scope.$on('youtube.player.paused', function ($event, player) {
            $scope.currentVideo.playing = false;
        });

        $scope.$on('youtube.player.ended', function ($event, player) {
            $scope.currentVideo.playing = false;
        });

        $scope.playerVars = {
            controls: 0,
            autoplay: 0,
            showinfo: 0
        };
    }])

    .controller('ResourcesCtrl', ['$scope', 'AffiliateResources', 'Notification', '$templateCache', '$filter', function ($scope, AffiliateResources, Notification, $templateCache, $filter) {
        var vm = this;

        $scope.oneAtATime = true;

        $scope.select = function (resource) {

            if (angular.isDefined($scope.selectedResource)) {
                $scope.selectedResource.selected = false;
            }

            $scope.selectedResource = resource;

            $scope.selectedResource.selected = true;
            angular.forEach($scope.selectedResource.items, function (item) {
                item.text = $filter('htmlToPlaintext')($templateCache.get(item.templateUrl));
            });
        };

        $scope.copySuccess = function () {
            Notification.success('Text copied to clipboard!');
        };

        $scope.copyError = function () {
            Notification.success('Oops! something went wrong! select the text and copy manually');
        };

        vm.init = function () {
            $scope.resources = AffiliateResources;

            $scope.select($scope.resources[0]);
        };

        vm.init();
    }])

    .controller('CommissionsCtrl', ['$scope', 'CommissionService', 'Notification', 'AuthService', function ($scope, CommissionService, Notification, AuthService) {

        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getCommissions();
            }
        };

        $scope.user = AuthService.getLoggedInUser();

        $scope.sortBy = {
            column: 'created_at',
            dir: 'desc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getCommissions();
            }
        };

        $scope.filters = {
            from: {
                format: 'dd MMM yyyy'
            },
            to: {
                format: 'dd MMM yyyy'
            },
            products: [
                { id: 1, name: 'IB' },
                { id: 2, name: 'IB PRO' }
            ],
            status: [
                'Pending', 'Ready to Pay', 'Paid'
            ],
            apply: function () {
                if(angular.isDefined(this.from.value)){
                    this.toApply.from = moment(this.from.value).format('YYYY-MM-DD');
                }

                if(angular.isDefined(this.to.value)){
                    this.toApply.to = moment(this.to.value).format('YYYY-MM-DD');
                }

                vm.getCommissions();
            },
            toApply: {}
        };

        $scope.calculateRetail = function (products) {
            var amount = 0;

            if(angular.isArray(products)){
                angular.forEach(products, function (prd) {
                    amount += prd.product_amount;
                });
            }

            return amount;
        };

        vm.getCommissionTotals = function () {
            function success(res) {
                $scope.commissionTotals = res.data;
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load commission totals!');
            }

            CommissionService.getCommissionTotals()
                .then(success, error);
        };

        vm.getCommissions = function () {
            var order = [];
            order[$scope.sortBy.column] = $scope.sortBy.dir;

            var params = {
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                limit: $scope.pagination.itemsPerPage,
                order: order,
                filter: $scope.filters.toApply
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.total;
                $scope.commissions = res.data.commissions;
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load commissions!');
            }

            CommissionService.getCommissions(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getCommissionTotals();
            vm.getCommissions();
        };

        vm.init();

    }])

    .controller('PaymentsCtrl', ['$scope', 'PaymentService', 'Notification', 'AuthService', function ($scope, PaymentService, Notification, AuthService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getCommissions();
            }
        };

        $scope.user = AuthService.getLoggedInUser();

        $scope.sortBy = {
            column: 'created_at',
            dir: 'desc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getCommissions();
            }
        };

        $scope.filters = {
            from: {
                format: 'dd MMM yyyy'
            },
            to: {
                format: 'dd MMM yyyy'
            },
            products: [
                { id: 1, name: 'IB' },
                { id: 2, name: 'IB PRO' }
            ],
            status: [
                'Pending', 'Ready to Pay', 'Paid'
            ],
            apply: function () {
                if(angular.isDefined(this.from.value)){
                    this.toApply.from = moment(this.from.value).format('YYYY-MM-DD');
                }

                if(angular.isDefined(this.to.value)){
                    this.toApply.to = moment(this.to.value).format('YYYY-MM-DD');
                }

                vm.getCommissions();
            },
            toApply: {}
        };

        vm.getPaymentTotals = function () {
            function success(res) {
                if(angular.isArray(res.data)){
                    $scope.paymentTotals = res.data[0];
                }
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load payment totals!');
            }

            PaymentService.getPaymentTotals()
                .then(success, error);
        };

        vm.getPayments = function () {
            var order = [];
            order[$scope.sortBy.column] = $scope.sortBy.dir;

            var params = {
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                limit: $scope.pagination.itemsPerPage,
                order: order,
                filter: $scope.filters.toApply
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.total;
                $scope.payments = res.data.payments;
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load payments!');
            }

            PaymentService.getPayments(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getPaymentTotals();
            vm.getPayments();
        };

        vm.init();
    }])

    .controller('DownlineCtrl', ['$scope', 'DownlineService', 'Notification', 'AuthService', function ($scope, DownlineService, Notification, AuthService) {

        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                vm.getDownlines();
            }
        };

        $scope.user = AuthService.getLoggedInUser();

        $scope.sortBy = {
            column: 'created_at',
            dir: 'desc',
            sort: function (col) {
                if (col === this.column) {
                    this.dir = this.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.column = col;
                    this.dir = 'asc';
                }

                vm.getDownlines();
            }
        };

        $scope.filters = {
            from: {
                format: 'dd MMM yyyy'
            },
            to: {
                format: 'dd MMM yyyy'
            },
            apply: function () {
                if(angular.isDefined(this.from.value)){
                    this.toApply.from = moment(this.from.value).format('YYYY-MM-DD');
                }

                if(angular.isDefined(this.to.value)){
                    this.toApply.to = moment(this.to.value).format('YYYY-MM-DD');
                }

                vm.getDownlines();
            },
            toApply: {}
        };

        vm.getDownlines = function () {
            var order = [];
            order[$scope.sortBy.column] = $scope.sortBy.dir;

            var params = {
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                limit: $scope.pagination.itemsPerPage,
                order: order,
                filter: $scope.filters.toApply
            };

            function success(res) {
                console.log(res);
                $scope.pagination.totalItems = res.data.total;
                $scope.users = res.data.users;
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load downline!');
            }

            DownlineService.query(params)
                .then(success, error);
        };

        vm.init = function () {
            vm.getDownlines();
        };

        vm.init();

    }])

    .controller('MarketingStatsCtrl', ['$scope', 'MarketingStatsService', 'Notification', 'MarketingLinksService', function ($scope, MarketingStatsService, Notification, MarketingLinksService) {
        var vm = this;

        $scope.pagination = {
            totalItems: 20,
            currentPage: 1,
            itemsPerPage: 10,
            pageChange: function () {
                $scope.getStats();
            }
        };

        $scope.sortBy = {};

        $scope.filters = {
            from: {
                format: 'dd MMM yyyy'
            },
            to: {
                format: 'dd MMM yyyy'
            },
            selectedFunnel: undefined,
            apply: function () {

                delete this.toApply.from;
                delete this.toApply.to;
                delete this.toApply.funnel;

                if(angular.isDefined(this.from.value)){
                    this.toApply.from = moment(this.from.value).format('YYYY-MM-DD');
                }

                if(angular.isDefined(this.to.value)){
                    this.toApply.to = moment(this.to.value).format('YYYY-MM-DD');
                }

                if(this.selectedFunnel && angular.isDefined(this.selectedFunnel)){
                    this.toApply.funnel = this.selectedFunnel.funnel_id;
                }

                $scope.getStats();
            },
            toApply: {}
        };

		$scope.formatDate = function(date){
			var dateOut = new Date(date);
			return dateOut;
		};

        $scope.getStats = function () {

            var params = {
                offset: ($scope.pagination.currentPage - 1) * $scope.pagination.itemsPerPage,
                limit: $scope.pagination.itemsPerPage,
                order: $scope.sortBy,
                filter: $scope.filters.toApply
            };

            function success(res) {
                $scope.pagination.totalItems = res.data.total;
                $scope.stats = res.data.stats;
            }

            function error(res) {
                Notification.error('Oops! there was an error trying to load stats!');
            }

            MarketingStatsService.queryStats(params)
                .then(success, error);
        };

        vm.getMarketingLinks = function () {
            MarketingLinksService.query()
                .then(function(res){
                    $scope.filters.funnels = res.data;
                }, vm.error);
        };

        vm.init = function () {
            $scope.getStats();
            vm.getMarketingLinks();
        };

        vm.init();
    }]);