<!DOCTYPE html>
<html ng-app="app" ng-controller="AppCtrl" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title ng-bind="pageTitle"></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/vendor.css">


    <!--<script type="text/javascript" src="js/app.js"></script>-->


</head>
<body><!--[if lt IE 8]>
<p class="browsehappy">
    You are using an <strong>outdated</strong> browser.
    Please <a href="http://browsehappy.com/">upgrade your browser</a>
    to improve your experience.
</p><![endif]-->

<div data-ng-include="'shared/header/header.tpl.html'"></div>
<div ui-view></div>
<div data-ng-include="'shared/footer/footer.tpl.html'"></div>


<script type="text/javascript" src="http://localhost:35729/livereload.js"></script>
<script type="text/javascript" src="js/vendor.js"></script>
<script type="text/javascript" src="js/templates-modules.js"></script>
<script type="text/javascript" src="js/templates-common.js"></script>
<!-- inject:js -->
<script type="text/javascript" src="/js/common/services/http-services.js"></script>
<script type="text/javascript" src="/js/common/services/site-configs.js"></script>
<script type="text/javascript" src="/js/modules/app.js"></script>
<script type="text/javascript" src="/js/modules/home/home.js"></script>
<script type="text/javascript" src="/js/modules/shared/header/header.js"></script>
<script type="text/javascript" src="/js/modules/shared/footer/footer.js"></script>
<!-- endinject -->
</body>
</html>