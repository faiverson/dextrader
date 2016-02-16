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
            },
            'serializeUrl': function (obj, prefix) {
                var str = [];
                for (var p in obj) {
                    if (obj.hasOwnProperty(p)) {
                        var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
                        str.push(typeof v === "object" ?
                            this.serializeUrl(v, k) :
                        encodeURIComponent(k) + "=" + encodeURIComponent(v));
                    }
                }
                return str.join("&");
            },
			setParameters: function(obj) {
				if ( ! angular.isObject( obj) ) {
					return( ( obj== null ) ? "" : obj.toString() );
				}
				var query = '',
					name, value, fullSubName, subName, subValue, innerObj, i;

				for(name in obj) {
					value = obj[name];
					if(value instanceof Array) {
						for(i in value) {
							subValue = value[i];
							fullSubName = name + '[' + i + ']';
							innerObj = {};
							innerObj[fullSubName] = subValue;
							query += this.setParameters(innerObj) + '&';
						}

					} else if(value instanceof Object) {
						for(subName in value) {

							subValue = value[subName];
							fullSubName = name + '[' + subName + ']';
							innerObj = {};
							innerObj[fullSubName] = subValue;
							query += this.setParameters(innerObj) + '&';
						}
					}
					else if(value !== undefined && value !== null) {
						query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
					}
				}

				return query.length ? query.substr(0, query.length - 1) : query;
			}
        };
    });
