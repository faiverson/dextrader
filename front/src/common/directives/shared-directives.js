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
                    if(!document.execCommand('copy')) {
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
    }]);
