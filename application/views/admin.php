<!DOCTYPE html>
<html lang="en" ng-app="appBooking">
    <head>
        <meta charset="utf-8">
        <title>Welcome to AppBooking</title>
        <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/vendors/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body ng-controller="crenelController">
        <header id="header">
            <div class="container-fluid">
                <div class="navbar-header">
                    AppBooking
                </div>

                <div class="navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="btn btn-default" href="/login/logout"><span class="glyphicon glyphicon-off"></span> {{'LOGOUT' | translate}}</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div id="container" ng-view></div>

        <footer id="footer">
            <form class="navbar-form admin-access">
                <h3>Accès Administrateur</h3>
                <div class="form-group">
                    <input type="email" class="form-control" ng-model="user.email" placeholder="Email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" ng-model="user.password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-default" ng-click="updateUser()">CHANGER</button>
            </form>
            <p>Design & Coding by Quentin THAI &copy; 2014</p>
            <p>Version beta 0.1.3</p>
            <p class="lastmodified">Last modified : 2014-11-19 01:37</p>
        </footer>

        <script src="js/vendors/angular.min.js"></script>
        <script src="js/vendors/angular-route.min.js"></script>
        <script src="js/vendors/angular-resource.min.js"></script>
        <script src="js/vendors/angular-sanitize.min.js"></script>
        <script src="js/vendors/angular-strap.min.js"></script>
        <script src="js/vendors/angular-strap.tpl.min.js"></script>
        <script src="js/vendors/angular-translate.min.js"></script>
        <script src="js/vendors/i18n/angular-locale_fr-fr.js"></script>
        <script src="js/vendors/moment.min.js"></script>
        <script src="js/admin/app.js"></script>
        <script src="js/admin/filters.js"></script>
        <script src="js/admin/controllers.js"></script>
        <script src="js/admin/services.js"></script>
        <script src="js/admin/directives.js"></script>
    </body>
</html>