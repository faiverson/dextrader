angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', '$filter', '$rootScope', function ($http, $q, $configs, localStorageService, jwtHelper, $objects, $filter, $rootScope) {

        function login(username, password) {
            var endpoint = $configs.API_BASE_URL + 'login';
            var deferred = $q.defer();

            function success(res) {
                if (res.data.success) {

                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);
                    $rootScope.$broadcast("user-has-change");

                    deferred.resolve();
                } else {
                    deferred.reject(res);
                }
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http({
                url: endpoint,
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                withCredentials: false,
                data: $objects.toUrlString({
                    username: username,
                    password: password
                })
            }).then(success, error);


            return deferred.promise;
        }

        function isLoggedIn() {
            var token = localStorageService.get('token');

            return token !== null && angular.isDefined(token);
        }

        function getLoggedInUser() {
            var data = {};

            if (isLoggedIn()) {
                data = jwtHelper.decodeToken(localStorageService.get('token'));
            }

            return data;
        }

        function getUserPermissions() {
            var userData = getLoggedInUser();
            var permissions = [];

            if (angular.isUndefined(userData)) {
                return permissions;
            }

            angular.forEach(userData.roles, function (role) {
                permissions = permissions.concat(role.permissions);
            });

            return permissions;
        }

        function userHasPermission(perm) {
            var permissions = getUserPermissions();

            if (angular.isUndefined(perm)) {
                return true;
            }

            return ($filter('filter')(permissions, {name: perm}, true)).length > 0;
        }

        function logout() {
            var deferred = $q.defer();

            //TODO: implement logout request

            setTimeout(function () {
                localStorageService.clearAll();
                deferred.resolve();
            });

            return deferred.promise;
        }

        function forgotPassword(email) {
            var endpoint = $configs.API_BASE_URL + 'password';
            var deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http.post(endpoint, {email: email}).then(success, error);

            return deferred.promise;
        }

        function resetPassword(token, email, password, password_confirmation) {
            var endpoint = $configs.API_BASE_URL + 'password/reset';
            var deferred = $q.defer();

            function success(res) {
                if (res.data.success) {

                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);
                    $rootScope.$broadcast("user-has-change");

                    deferred.resolve();
                } else {
                    deferred.reject(res);
                }
            }

            function error(err) {
                deferred.reject(err.data);
            }

            $http.post(endpoint, {
                token: token,
                email: email,
                password: password,
                password_confirmation: password_confirmation
            }).then(success, error);

            return deferred.promise;
        }

        function setUserToken(token) {
            // Set the token into local storage
            localStorageService.set('token', token);
            $rootScope.$broadcast("user-has-change");
        }

        return {
            login: login,
            getLoggedInUser: getLoggedInUser,
            isLoggedIn: isLoggedIn,
            logout: logout,
            userHasPermission: userHasPermission,
            forgotPassword: forgotPassword,
            resetPassword: resetPassword,
            setUserToken: setUserToken
        };
    }])

    .factory('httpRequestInterceptor', ['localStorageService', function (localStorageService) {
        return {
            request: function ($config) {
                var header;
                if ($config.withCredentials !== false) {
                    $config.withCredentials = true;
                    header = 'Bearer ' + localStorageService.get('token');
                    $config.headers.Authorization = header;
                }
                return $config;
            }
        };
    }])

    .factory('UserService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'users';

        function getUsers() {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);
            return deferred.promise;
        }

        function save(data) {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            if (angular.isDefined(data.user_id)) {
                endpoint += '/' + data.user_id;
                $http.put(endpoint, data).then(success, error);
            } else {
                $http.post(endpoint, data).then(success, error);
            }

            return deferred.promise;
        }

        function getUser(id) {
            var deferred = $q.defer(),
                endpoint = service + '/';

            if (angular.isUndefined(id)) {
                deferred.reject('User id not found!');
            }

            endpoint += id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function soon(data) {
            var deferred = $q.defer(),
                endpoint = service + '/coming-soon';

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.post(endpoint, data).then(success, error);

            return deferred.promise;
        }

        return {
            getUsers: getUsers,
            saveUser: save,
            getUser: getUser,
            soon: soon
        };
    }])

    .factory('CreditCardService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'users/' + AuthService.getLoggedInUser().user_id + '/cards';

        function query() {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function getOne(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Card id is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.cc_id)) {
                endpoint += '/' + data.cc_id;

                $http.put(endpoint, data)
                    .then(success, error);
            } else {
                $http.post(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            getOne: getOne,
            save: save
        };
    }])

    .factory('BillingAddressService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'users/' + AuthService.getLoggedInUser().user_id + '/billing-address';

        function query() {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function getOne(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Address id is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.address_id)) {
                endpoint += '/' + data.address_id;

                $http.put(endpoint, data)
                    .then(success, error);
            } else {
                $http.post(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            getOne: getOne,
            save: save
        };
    }])

    .factory('SubscriptionService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'subscriptions/';

        function query() {
            var endpoint = service + AuthService.getLoggedInUser().user_id,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function getOne(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Subsctiption id is required!');
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.subscription_id)) {
                endpoint += data.subscription_id + '/users/' + AuthService.getLoggedInUser().user_id;

                $http.put(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            getOne: getOne,
            save: save
        };
    }])

    .factory('InvoiceService', ['$http', '$q', '$site-configs', 'AuthService', function ($http, $q, $configs, AuthService) {

        var service = $configs.API_BASE_URL + 'invoices/' + AuthService.getLoggedInUser().user_id;

        function query() {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;

        }

        function download(id) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isUndefined(id)) {
                deferred.reject('Subsctiption id is required!');
            }

            endpoint += '/download/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http({
                method: 'GET',
                url: endpoint,
                responseType: 'arraybuffer'
            }).then(success, error);

            return deferred.promise;
        }

        function save(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }


            if (angular.isDefined(data.address_id)) {
                endpoint += '/' + data.address_id;

                $http.put(endpoint, data)
                    .then(success, error);
            } else {
                $http.post(endpoint, data)
                    .then(success, error);
            }

            return deferred.promise;
        }

        return {
            query: query,
            download: download,
            save: save
        };
    }])

    .factory('MarketingLinksService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'marketing-links';

        function query() {
            var deferred = $q.defer(),
                endpoint = service;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            query: query
        };
    }])

    .factory('ProvidersService', ['$http', '$q', '$site-configs', '$objects', function ($http, $q, $configs, $objects) {
        var service = $configs.API_BASE_URL + 'providers';

        function query(params) {
            var deferred = $q.defer(),
                endpoint = service;

            if (angular.isDefined(params)) {
                endpoint += '?' + $objects.toUrlString(params);
            }

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            query: query
        };
    }])

    .factory('TrainingService', ['$http', '$q', '$site-configs', 'localStorageService', '$rootScope', function ($http, $q, $configs, localStorageService, $rootScope) {
        var service = $configs.API_BASE_URL + 'training';

        function queryAffiliates() {
            var deferred = $q.defer(),
                endpoint = service + '/affiliates';

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function queryDexIB() {
            var deferred = $q.defer(),
                endpoint = service + '/certification';

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        function unlockTraining(id) {
            var deferred = $q.defer(),
                endpoint = service + '/certification';


            if (angular.isUndefined(id)) {
                deferred.reject();
            }

            function success(res) {

                if (angular.isDefined(res.data.data) && angular.isDefined(res.data.data.token)) {
                    // Set the token into local storage
                    localStorageService.set('token', res.data.data.token);
                    $rootScope.$broadcast("user-has-change");
                }

                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.post(endpoint, {training_id: id}).then(success, error);

            return deferred.promise;
        }

        function download(id) {
            var deferred = $q.defer(),
                endpoint = service + '/certification/download';


            if (angular.isUndefined(id)) {
                deferred.reject();
            }

            endpoint += '/' + id;

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http({
                method: 'GET',
                url: endpoint,
                responseType: 'arraybuffer'
            }).then(success, error);

            return deferred.promise;
        }

        return {
            queryAffiliates: queryAffiliates,
            queryDexIB: queryDexIB,
            unlockTraining: unlockTraining,
            download: download
        };
    }])

    .factory('CommissionService', ['$http', '$q', '$site-configs', 'AuthService', '$objects', function ($http, $q, $configs, AuthService, $objects) {
        var service = $configs.API_BASE_URL + 'users/' + AuthService.getLoggedInUser().user_id + '/commissions';

        function getCommissionTotals() {
            var endpoint = service,
                deferred = $q.defer();

            setTimeout(function () {
                deferred.resolve({
                    data: {
                        'today': 1231,
                        'yesterday': 324.34,
                        'last_week': 123345,
                        'last_month': 423433,
                        'last_year': 124234,
                        'all_time': 3242344
                    }
                });
            }, 500);

            return deferred.promise;

        }

        function getCommissions(params) {
            var endpoint = service,
                deferred = $q.defer();

            if (angular.isObject(params)) {
                endpoint += '?' + $objects.serializeUrl(params);
            }

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
            getCommissionTotals: getCommissionTotals,
            getCommissions: getCommissions
        };
    }])

    .factory('EWalletService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        var service = $configs.API_BASE_URL + 'users';

        function createEWallet() {
            var endpoint = service + '/ewallet',
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.post(endpoint).then(success, error);

            return deferred.promise;

        }

        return {
            createEWallet: createEWallet
        };
    }])

    .factory('TestimonialsService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'testimonials';

        function query(params) {
            var endpoint = service,
                deferred = $q.defer();

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
            query: query
        };

    }])

    .factory('LiveSignalsService', ['$http', '$q', '$site-configs', '$objects', function ($http, $q, $configs, $objects) {
        var service = $configs.API_BASE_URL + 'signals/';

        function query(product, params) {
            var deferred = $q.defer(),
                endpoint = service;

            endpoint += product;

            if (angular.isDefined(params)) {
                endpoint += '?' + $objects.toUrlString(params);
            }

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(res) {
                deferred.reject(res);
            }

            $http.get(endpoint).then(success, error);

            return deferred.promise;
        }

        return {
            query: query
        };
    }])

    .factory('ChatService', ['AuthService', function (AuthService) {

        // jshint ignore:start
        window.zEmbed || function (e, t) {// jshint ignore:line
            var n, o, d, i, s, a = [], r = document.createElement("iframe");// jshint ignore:line
            window.zEmbed = function () {// jshint ignore:line
                a.push(arguments) // jshint ignore:line
            }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;// jshint ignore:line
            try {// jshint ignore:line
                o = s// jshint ignore:line
            } catch (c) {// jshint ignore:line
                n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s// jshint ignore:line
            }// jshint ignore:line
            o.open()._l = function () {// jshint ignore:line
                var o = this.createElement("script");// jshint ignore:line
                n && (this.domain = n), o.id = "js-iframe-async", o.src = e, this.t = +new Date, this.zendeskHost = t, this.zEQueue = a, this.body.appendChild(o)// jshint ignore:line
            }, o.write('<body onload="document._l();">'), o.close()// jshint ignore:line
        }("https://assets.zendesk.com/embeddable_framework/main.js", "ourcityinvestments.zendesk.com");// jshint ignore:line
        // jshint ignore:end

        function show() {
            zE.activate({hideOnClose: true});
        }

        function setup() {

            zE.hide();
            zE.setLocale('en');
            zE.identify({
                name: AuthService.getLoggedInUser().username,
                email: AuthService.getLoggedInUser().email,
                externalId: AuthService.getLoggedInUser().user_id
            });
        }

        zEmbed(setup);

        return {
            show: show
        };
    }])

    .factory('CheckoutService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'checkout';

        function send(data, user_id) {
            var endpoint = service + '/upgrade/' + user_id,
                deferred = $q.defer();

            function success(res) {
                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http({
                method: "POST",
                url: endpoint,
                data: data,
            }).then(success, error);

            return deferred.promise;

        }

        return {
            send: send
        };

    }])

    .factory('UserRolesService', ['$http', '$q', '$site-configs', function ($http, $q, $configs) {
        //var service = $configs.API_BASE_URL + 'users';
        function getRoles() {
            return [
                {id: 1, name: 'User'},
                {id: 5, name: 'Editor'},
                {id: 9, name: 'Admin'},
                {id: 10, name: 'Super Admin'}
            ];
        }

        return {
            getRoles: getRoles
        };
    }]);
