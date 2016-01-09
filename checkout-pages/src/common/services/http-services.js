angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', '$filter', '$rootScope',
        function ($http, $q, $configs, localStorageService, jwtHelper, $objects, $filter, $rootScope) {

            function login(username, password) {
                var endpoint = $configs.API_BASE_URL + 'login';
                var deferred = $q.defer();

                function success(res) {
                    if (res.data.success) {
                        deferred.resolve(res.data);
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

                return token != null && angular.isDefined(token);
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

            return {
                login: login,
                getLoggedInUser: getLoggedInUser,
                isLoggedIn: isLoggedIn,
                logout: logout,
                userHasPermission: userHasPermission,
                forgotPassword: forgotPassword,
                resetPassword: resetPassword
            };
        }])

    .factory('InvoicesService', ['$http', '$q', '$site-configs', 'localStorageService', function ($http, $q, $configs, localStorageService) {

        function getInvoice(id) {
            return localStorageService.get(id);
        }

        function setInvoice(id, data) {
            return localStorageService.set(id, data);
        }

        return {
            getInvoice: getInvoice,
            setInvoice: setInvoice
        };
    }])

    .factory('CheckoutService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'checkout';

        function send(data, token) {
            var endpoint = service,
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
                headers: {'Authorization': 'Bearer ' + token}
            }).then(success, error);

            return deferred.promise;

        }

        function upgrade(data, user_id, token) {
            var endpoint = service,
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
                headers: {'Authorization': 'Bearer ' + token}
            }).then(success, error);

            return deferred.promise;

        }

        return {
            send: send,
            upgrade: upgrade
        };

    }])

    .factory('UserService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'users';

        function send(data) {
            var endpoint = service + '/signup',
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
                data: data
            }).then(success, error);

            return deferred.promise;

        }

        return {
            send: send
        };

    }])

    .factory('HitsService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'hits';

        function send(data, token) {
            var endpoint = service,
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
                headers: {'Authorization': 'Bearer ' + token}
            }).then(success, error);

            return deferred.promise;

        }

        return {
            send: send
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

    .factory('PageService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'pages';

        function getToken() {
            var endpoint = service,
                deferred = $q.defer(),
                data = {'domain': 'sales', 'password': 'sAles_dexTr4d3r'};

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
            getToken: getToken
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

        function setup(){

            zE.hide();
            zE.setLocale('en');
            zE.identify({
                name: AuthService.isLoggedIn() ? AuthService.getLoggedInUser().username : 'Anonymous',
                email: AuthService.isLoggedIn() ? AuthService.getLoggedInUser().username : 'Anonymous',
                externalId: AuthService.isLoggedIn() ? AuthService.getLoggedInUser().username : 'Anonymous'
            });
        }

        zEmbed(setup);

        return {
            show: show
        };
    }])

    .factory('CountriesService', ['$q', '$site-configs', '$http', function ($q, $config, $http) {
        var service = $config.API_BASE_URL + 'countries';

        function queryCountries(q) {
            var endpoint = service,
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
            var endpoint = service,
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
