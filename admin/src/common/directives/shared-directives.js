angular.module('app.shared-directives', [])

    .directive('compareTo', function () {
        return {
            require: "ngModel",
            scope: {
                otherModelValue: "=compareTo"
            },
            link: function (scope, element, attributes, ngModel) {

                ngModel.$validators.compareTo = function (modelValue) {
                    return modelValue === scope.otherModelValue;
                };

                scope.$watch("otherModelValue", function () {
                    ngModel.$validate();
                });
            }
        };
    })
    .directive('userCan', ['AuthService', function (AuthService) {
        return {
            restrict: 'A',
            link: function ($scope, $elem, $attrs) {

                $scope.$on("user-has-change", function (nv, ov) {
                    evaluatePerm();
                });

                function evaluatePerm() {
                    if (angular.isDefined($attrs.userCan)) {
                        if (!AuthService.userHasPermission($attrs.userCan)) {
                            $elem.addClass('user-disable-action');
                        } else {
                            $elem.removeClass('user-disable-action');
                        }
                    }
                }

                evaluatePerm();
            }
        };
    }])
    .directive('sorting', [function () {
        return {
            restrict: 'A',
            scope: {
                currentDir: '='
            },
            link: function ($scope, $elem, $attrs) {
                $elem.addClass('sortable');
                $scope.$watchCollection('[currentDir]', function () {
                    $elem.removeClass('sortable-asc');
                    $elem.removeClass('sortable-desc');

                    if ($scope.currentDir === 'asc') {
                        $elem.addClass('sortable-asc');
                    } else if ($scope.currentDir === 'desc') {
                        $elem.removeClass('sortable-asc');
                        $elem.addClass('sortable-desc');
                    }
                    else {
                        $elem.removeClass('sortable-asc');
                        $elem.removeClass('sortable-desc');
                    }
                });
            }
        };
    }])
    .directive('sortable', [function () {
        return {
            restrict: 'A',
            scope: {
                currentSort: '=',
                currentDir: '='
            },
            link: function ($scope, $elem, $attrs) {

                $elem.addClass('sortable');

                $scope.$watchCollection('[currentSort, currentDir]', function () {
                    $elem.removeClass('sortable-asc');
                    $elem.removeClass('sortable-desc');

                    if ($scope.currentSort.toString() === $attrs.sortable) {
                        if ($scope.currentDir === 'asc') {
                            $elem.addClass('sortable-asc');
                        } else {
                            $elem.addClass('sortable-desc');
                        }
                    }
                });
            }
        };
    }])

    .directive('sortColumn', [function () {
        return {
            restrict: 'A',
            scope: {
                onSort: '&',
                sortData: '='
            },
            link: function ($scope, $elem, $attrs) {
                var vm = this;

                $elem.addClass('clickable');

                $elem.bind('click', function () {
                    vm.sort($attrs.sortColumn, $elem);
                });

                vm.sort = function (col, elem) {

                    elem.removeClass('sort-desc');
                    elem.removeClass('sort-asc');

                    if ($scope.sortData.hasOwnProperty(col)) {
                        if ($scope.sortData[col] === 'asc') {
                            $scope.sortData[col] = 'desc';
                        }
                        else if ($scope.sortData[col] === 'desc') {
                            delete $scope.sortData[col];
                        }
                    } else {
                        $scope.sortData[col] = 'asc';
                    }

                    if (angular.isDefined($scope.sortData[col])) {
                        elem.addClass('sort-' + $scope.sortData[col]);
                    }

                    if (angular.isFunction($scope.onSort)) {
                        $scope.onSort();
                    }
                };
            }
        };
    }])

    .directive('ngThumb', ['$window', function ($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function (item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function (file) {
                var type = '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function (scope, element, attributes) {
                if (!helper.support) {
                    return;

                }

                var params = scope.$eval(attributes.ngThumb);

                if (!helper.isFile(params.file)) {
                    return;
                }
                if (!helper.isImage(params.file)) {
                    return;
                }

                var canvas = element.find('canvas');
                var reader = new FileReader();

                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);


                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({width: width, height: height});
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }])
    .directive('ngCsrc', ['$site-configs', function ($configs) {
        return {
            priority: 99,
            link: function (scope, element, attr) {
                attr.$observe('ngCsrc', function (value) {
                    if (!value) {
                        return;
                    }

                    attr.$set('src', $configs.DASHBOARD_URL + value);
                });
            }
        };
    }]);
