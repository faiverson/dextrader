angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('SalesService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = 'sales';

        function send(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.post(endpoint, data).then(success, error);

            return deferred.promise;

        }

        return {
            send: send
        };

    }])
    .factory('CountriesService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'countries';

        function queryCountries(q) {
            var endpoint = service ,
                deferred = $q.defer();

            endpoint += '/' + q;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function queryCities(countryCode, q) {
            var endpoint = service ,
                deferred = $q.defer();

            endpoint += '/' + countryCode + '/cities/' + q;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        return {
            queryCountries: queryCountries,
            queryCities: queryCities
        };

    }]);
