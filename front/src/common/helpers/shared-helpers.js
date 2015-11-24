angular.module('app.shared-helpers', [])
    .factory('$objects', function () {
        return {
            'isEmpty': function (obj) {
                return Object.keys(obj).length === 0;
            },
            'cleanArray': function cleanArray(actual) {
                var newArray = [];
                for (var i = 0; i < actual.length; i++) {
                    if (actual[i]) {
                        newArray.push(actual[i]);
                    }
                }
                return newArray;
            },
            'toUrlString': function (obj) {
                var url = this.cleanArray(Object.keys(obj).map(function (k) {
                    if (!angular.isUndefined(obj[k]) && obj[k] !== "") {
                        return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]);
                    }
                })).join('&');
                return url;
            }
        };
    });

