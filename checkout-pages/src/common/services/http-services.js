angular.module('app.http-services', ['app.site-configs', 'angular-jwt', 'app.shared-helpers'])

    .factory('AuthService', ['$http', '$q', '$site-configs', 'localStorageService', 'jwtHelper', '$objects', '$filter', '$rootScope',
        function ($http, $q, $configs, localStorageService, jwtHelper, $objects, $filter, $rootScope) {
            var service = $configs.API_BASE_URL + 'pages';

            function userLogin(username, password) {
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

            function login() {
                var endpoint = service,
                    deferred = $q.defer(),
                    data = {'domain': 'sales', 'password': 'sAles_dexTr4d3r'};

                function success(res) {
                    if (res.data.success) {
                        // Set the token into local storage
                        localStorageService.set('token', res.data.data.token);

                        deferred.resolve();
                    } else {
                        deferred.reject(res);
                    }
                }

                function error(err) {
                    deferred.reject(err);
                }


                $http({
                    url: endpoint,
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    data: $objects.toUrlString(data),
                    withCredentials: false
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
                userLogin: userLogin,
                login: login,
                getLoggedInUser: getLoggedInUser,
                isLoggedIn: isLoggedIn,
                logout: logout,
                userHasPermission: userHasPermission,
                forgotPassword: forgotPassword,
                resetPassword: resetPassword
            };
        }])

    .factory('httpRequestInterceptor', ['$q', 'localStorageService', '$injector', function ($q, localStorageService, $injector) {
        var alreadyRequestToken = false;
        var requestsWaitingForToken = [];
        return {
            request: function ($config) {
                var header;
                var deferred = $q.defer();

                // Skip authentication for any requests ending in .html
                if ($config.url.substr($config.url.length - 5) === '.html') {
                    deferred.resolve($config);
                    return deferred.promise;
                }

                if ($config.withCredentials !== false) {
                    var AuthService = $injector.get('AuthService');
                    if (!AuthService.isLoggedIn()) {

                        requestsWaitingForToken.push({def: deferred, config: $config});

                        if (!alreadyRequestToken) {

                            AuthService.login()
                                .then(function (res) {

                                    requestsWaitingForToken.forEach(function (def) {
                                        def.config.withCredentials = true;
                                        header = 'Bearer ' + localStorageService.get('token');
                                        def.config.headers.Authorization = header;
                                        def.def.resolve(def.config);
                                    });

                                    alreadyRequestToken = false;
                                });

                            alreadyRequestToken = true;
                        }

                    } else {
                        $config.withCredentials = true;
                        header = 'Bearer ' + localStorageService.get('token');
                        $config.headers.Authorization = header;
                        deferred.resolve($config);
                    }
                } else {
                    deferred.resolve($config);
                }
                return deferred.promise; //$config;
            }
        };
    }])

    .factory('InvoicesService', ['$http', '$q', '$site-configs', 'localStorageService', function ($http, $q, $configs, localStorageService) {

        function getInvoices() {
            return localStorageService.get('invoices');
        }

        function setInvoice(data) {
            var invoices = localStorageService.get('invoices') || [];

			if(parseInt(invoices[0].user_id, 10) !== parseInt(data.user_id, 10) && invoices.length > 0 ) {
				invoices = [];
			}
            invoices.push(data);

            return localStorageService.set('invoices', invoices);
        }

        return {
            getInvoices: getInvoices,
            setInvoice: setInvoice
        };
    }])

    .factory('CheckoutService', ['$q', '$site-configs', '$http', 'localStorageService', function ($q, $config, $http, localStorageService) {
        var service = $config.API_BASE_URL + 'checkout';

        function send(data) {
            var endpoint = service,
                deferred = $q.defer();

            function success(res) {

				if (angular.isDefined(res.data.data) && angular.isDefined(res.data.data.token)) {
					localStorageService.set('user-token', res.data.data.token);
				}

                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.post(endpoint, data).then(success, error);

            return deferred.promise;

        }

        function upgrade(data, user_id) {
            var endpoint = service + '/up-down-upgrade/' + user_id,
                deferred = $q.defer();

            function success(res) {
				if (angular.isDefined(res.data.data) && angular.isDefined(res.data.data.token)) {
					localStorageService.set('user-token', res.data.data.token);
				}

                deferred.resolve(res.data);
            }

            function error(err) {
                deferred.reject(err);
            }

            $http.post(endpoint, data).then(success, error);

            return deferred.promise;

        }

        return {
            send: send,
            upgrade: upgrade
        };

    }])

    .factory('SpecialOffersService', ['$q', '$site-configs', '$http', '$filter', 'localStorageService', function ($q, $config, $http, $filter, localStorageService) {
        var service = $config.API_BASE_URL + 'offers';

        function query(funnelId, productIds, checkForOffers, type) {
            var endpoint = service,
                deferred = $q.defer(),
                tmpPrices = localStorageService.get('productPrices' + funnelId);

            if (angular.isUndefined(funnelId)) {
                deferred.reject('Funnel ID is required');
            }

            endpoint += '/' + funnelId;

            function success(res) {
                localStorageService.set('productPrices' + funnelId, res.data);

                deferred.resolve(processOffers(res.data, productIds, checkForOffers, type));
            }

            function error(err) {
                deferred.reject(err);
            }

            //if (tmpPrices != null && angular.isDefined(tmpPrices)) {
            //    deferred.resolve(processOffers(tmpPrices, productIds, checkForOffers));
            //} else {
                $http.get(endpoint).then(success, error);
            //}

            return deferred.promise;

        }

        function processOffers(res, productIds, checkForOffers, type) {
            var products = [];

            productIds.forEach(function (prd) {
                var offers = $filter('filter')(res.data.offers, {product_id: prd, type: type}, true);
                var product = $filter('filter')(res.data.products, {product_id: prd});

                if (offers.length > 0 && checkForOffers) {
                    var offer = offers[0];
                    offer.product = product[0];
                    products.push(offer);
                } else {
                    products.push(product[0]);
                }
            });

            return products;
        }

        return {
            query: query
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
                withCredentials: false,
                data: data
            }).then(success, error);

            return deferred.promise;

        }

        return {
            send: send
        };

    }])

	.factory('HitsService', ['$q', '$site-configs', '$http', '$objects', function ($q, $config, $http, $objects) {
		var service = $config.API_BASE_URL + 'hits';
		var loginService = $config.API_BASE_URL + 'pages';

		function login() {
			var endpoint = loginService,
				deferred = $q.defer(),
				data = {'domain': 'sales', 'password': 'sAles_dexTr4d3r'};

			function success(res) {
				if (res.data.success) {
					deferred.resolve(res.data.data.token);
				} else {
					deferred.reject(res);
				}
			}

			function error(err) {
				deferred.reject(err);
			}


			$http({
				url: endpoint,
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
				data: $objects.toUrlString(data),
				withCredentials: false
			}).then(success, error);

			return deferred.promise;
		}

		function send(data) {
			var endpoint = service,
				deferred = $q.defer();

			function success(res) {
				deferred.resolve(res.data);
			}

			function error(err) {
				deferred.reject(err);
			}

			login().then(function (token) {
				$http({
					method: "POST",
					withCredentials: false,
					url: endpoint,
					data: data,
					headers: {'Authorization': 'Bearer ' + token}
				}).then(success, error);
			});

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

    }])

    .factory('UserSettings', ['localStorageService', function (localStorageService) {

        function getUserEnroller() {
            var enroller = localStorageService.get('enroller');
            var exp_enroller = localStorageService.get('exp_enroller');

            if (enroller && exp_enroller) {
                if (moment().isAfter(moment(exp_enroller, 'YYYY-MM-DD'))) {
                    localStorageService.remove('enroller');
                    localStorageService.remove('exp_enroller');
                } else {
                    return enroller;
                }
            }
			return;
        }

        function setEnroller(enroller) {
            var exp_enroller = moment().add(14, 'days').format('YYYY-MM-DD');

            localStorageService.set('enroller', enroller);
            localStorageService.set('exp_enroller', exp_enroller);
        }

        return {
            setEnroller: setEnroller,
            userEnroller: getUserEnroller
        };
    }]);
