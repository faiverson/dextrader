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
    .directive('clipboard', ['$document', function ($document) {
        return {
            restrict: 'A',
            scope: {
                onCopied: '&',
                onError: '&',
                text: '='
            },
            link: function (scope, element) {
                function createNode(text) {
                    var node = document.createElement('textarea');
                    node.style.position = 'absolute';
                    node.style.left = '-10000px';
                    node.textContent = text;
                    return node;
                }

                function copyNode(node) {
                    // Set inline style to override css styles
                    document.body.style.webkitUserSelect = 'initial';

                    var selection = document.getSelection();
                    selection.removeAllRanges();
                    node.select();

                    //document.execCommand('copy');
                    if (!document.execCommand('copy')) {
                        throw('failure copy');
                    }
                    selection.removeAllRanges();

                    // Reset inline style
                    document.body.style.webkitUserSelect = '';
                }

                function copyText(text) {
                    var node = createNode(text);
                    document.body.appendChild(node);
                    copyNode(node);
                    document.body.removeChild(node);
                }

                element.on('click', function (event) {
                    try {
                        copyText(scope.text);
                        if (angular.isFunction(scope.onCopied)) {
                            scope.$evalAsync(scope.onCopied());
                        }
                    } catch (err) {
                        if (angular.isFunction(scope.onError)) {
                            scope.$evalAsync(scope.onError({err: err}));
                        }
                    }
                });
            }
        };
    }])
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

                $elem.addClass('clickable');

                $elem.bind('click', function () {
                    $scope.sort($attrs.sortColumn, $elem);
                });

                $scope.sort = function (col, elem) {

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
    .directive('ngChref', ['$site-configs', function ($configs) {
        return {
            priority: 99,
            link: function (scope, element, attr) {
                attr.$observe('ngChref', function (value) {
                    if (!value) {
                        return;
                    }

                    attr.$set('href', $configs.SECURE_URL + value);
                });
            }
        };
    }])
    .directive('beforeExit', ['$document', function ($document) {
        return {
            restrict: 'E',
            scope: {
                onExit: '&'
            },
            link: function ($scope, $elem, $attrs) {
                $document.mousemove(function (e) {
                    if ((e.pageY - window.pageYOffset) <= 3) {
                        if (angular.isFunction($scope.onExit)) {
                            $scope.onExit();
                        }
                    }
                });
            }
        };
    }]);
