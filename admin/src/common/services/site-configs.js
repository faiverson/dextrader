angular.module('app.site-configs', [])

.provider('$site-configs', function () {
	var globals = {
		API_BASE_URL: 'http://localhost:8000/api/'
	};

	return {
		getItem: function (key) {
			return globals[key];
		},
		$get: function () {
			return globals;
		}
	};
});