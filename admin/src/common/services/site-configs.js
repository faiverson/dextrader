angular.module('app.site-configs', [])

.provider('$site-configs', function () {
	var globals = {
		API_BASE_URL: '@@API_URL',
		SITE_URL: '@@SITE_URL',
		SITE_NAME: '@@SITE_NAME',
		DASHBOARD_URL: '@@DASHBOARD_URL',
		SOCKET_HOST: '@@SOCKET_HOST'
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