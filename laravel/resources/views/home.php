<!DOCTYPE html><!--[if IEMobile 7]><html class="no-js iem7 oldie" ng-app="app" ng-controller="AppCtrl" class="no-js"><![endif]--><!--[if (IE 7)&!(IEMobile)]><html class="no-js ie7 oldie" lang="en" ng-app="app" ng-controller="AppCtrl" class="no-js"><![endif]--><!--[if (IE 8)&!(IEMobile)]><html class="no-js ie8 oldie" lang="en" ng-app="app" ng-controller="AppCtrl" class="no-js"><![endif]--><!--[if (IE 9)&!(IEMobile)]><html class="no-js ie9" lang="en" ng-app="app" ng-controller="AppCtrl" class="no-js"><![endif]--><!--[if (gt IE 9)|(gt IEMobile 7)]><!-->
<html class="no-js" lang="es" ng-app="app" ng-controller="AppCtrl" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title ng-bind="pageTitle"></title>
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width,initial-scale=1" name="viewport">
<meta name="author" content="Real Creators">
<meta name="description" content="">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="apple-touch-icon-120x120-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">
<!--[if IE]><link rel="shortcut icon" href="favicon.ico"><![endif]-->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="stylesheet" href="/front/css/styles.css">
</head>
<body class="{{bodyClass}}">
<base href="/">
<!--[if lt IE 8]><p class="browsehappy">
		You are using an <strong>outdated</strong> browser.
		Please <a href="http://browsehappy.com/">upgrade your browser</a>
		to improve your experience.
	</p><![endif]-->
<div data-ng-include="'modules/shared/header/header.tpl.html'"></div>
<div class="clear"></div>
<div ui-view></div>
<div data-ng-include="'modules/shared/footer/footer.tpl.html'"></div>
<script src="https://www.youtube.com/iframe_api"></script>
<script type="text/javascript" src="http://localhost:35729/livereload.js"></script>
<script type="text/javascript" src="/front/js/vendor.js"></script>
<script type="text/javascript" src="/front/js/templates.js"></script>
<script type="text/javascript" src="/front/js/modules/app.js"></script>
<script type="text/javascript" src="/front/js/common/directives/shared-directives.js"></script>
<script type="text/javascript" src="/front/js/common/filters/shared-filters.js"></script>
<script type="text/javascript" src="/front/js/common/services/http-services.js"></script>
<script type="text/javascript" src="/front/js/common/services/site-configs.js"></script>
<script type="text/javascript" src="/front/js/common/helpers/shared-helpers.js"></script>
<script type="text/javascript" src="/front/js/modules/affiliates/affiliates.js"></script>
<script type="text/javascript" src="/front/js/modules/auth/auth.js"></script>
<script type="text/javascript" src="/front/js/modules/dashboard/dashboard.js"></script>
<script type="text/javascript" src="/front/js/modules/dex-fx/dex-fx.js"></script>
<script type="text/javascript" src="/front/js/modules/dex-ib/dex-ib.js"></script>
<script type="text/javascript" src="/front/js/modules/dex-na/dex-na.js"></script>
<script type="text/javascript" src="/front/js/modules/disclosure/disclosure.js"></script>
<script type="text/javascript" src="/front/js/modules/privacy/privacy.js"></script>
<script type="text/javascript" src="/front/js/modules/terms-and-conditions/terms-and-conditions.js"></script>
<script type="text/javascript" src="/front/js/modules/user-profile/user-profile.js"></script>
<script type="text/javascript" src="/front/js/modules/affiliates/resources/resources.js"></script>
<script type="text/javascript" src="/front/js/modules/shared/footer/footer.js"></script>
<script type="text/javascript" src="/front/js/modules/shared/header/header.js"></script>
</body>
</html>