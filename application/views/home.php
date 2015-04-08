<!DOCTYPE html>
<html lang="en" ng-app="appBooking">
    <head>
        <meta charset="utf-8">
        <title>Welcome to AppBooking</title>
        <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/vendors/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/home.css">
    </head>
    <body ng-controller="crenelController">
        <header id="header">
            AppBooking
        </header>

        <div id="container" ng-view class="christmas"></div>
	<div class="parallax-window" data-parallax="scroll" data-image-src="/images/002.jpg"></div>

        <footer id="footer">
            <h3>AppBooking Beta Version</h3>
            <p>Design & Coding by Quentin THAI &copy; 2014</p>
            <p>Version beta 0.1.3</p>
            <p class="lastmodified">Last modified : 2014-11-19 01:37</p>
        </footer>

	<script src="js/vendors/jquery-2.1.1.min.js"></script>
        <script src="js/vendors/angular.min.js"></script>
        <script src="js/vendors/angular-route.min.js"></script>
        <script src="js/vendors/angular-resource.min.js"></script>
        <script src="js/vendors/angular-sanitize.min.js"></script>
        <script src="js/vendors/angular-strap.min.js"></script>
        <script src="js/vendors/angular-strap.tpl.min.js"></script>
        <script src="js/vendors/angular-translate.min.js"></script>
        <script src="js/vendors/i18n/angular-locale_fr-fr.js"></script>
        <script src="js/vendors/moment.min.js"></script>
	<script src="js/vendors/parallax.min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/filters.js"></script>
        <script src="js/controllers.js"></script>
        <script src="js/services.js"></script>
    </body>
</html>