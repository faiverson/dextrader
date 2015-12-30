angular.module('app.shared-filters', [])
    .filter('htmlToPlaintext', function () {
        return function (text) {
            return text ? String(text).replace(/<[^>]+>/gm, '') : '';
        };
    })
    .filter('amParse', [function () {
        return function (value, format) {
            return moment(value, format);
        };
    }])
    .filter('dateFormat', [function () {
        function amDateFormatFilter(value, format) {
            if (!value && angular.isUndefined(value)) {
                return '';
            }

            var date = moment(value);
            if (!date.isValid()) {
                return '';
            }

            return date.format(format);
        }

        return amDateFormatFilter;
    }]);