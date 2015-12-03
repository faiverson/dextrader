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

                if (angular.isDefined($attrs.userCan)) {
                    if (!AuthService.userHasPermission($attrs.userCan)) {
                        $elem.addClass('user-disable-action');
                    } else {
                        $elem.removeClass('user-disable-action');
                    }
                }
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

                if (!helper.isFile(params.file)) {return;}
                if (!helper.isImage(params.file)) {return;}

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
    }]);
