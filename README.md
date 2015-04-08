# AppBooking
A simple booking webapplication.

In order to learn Angular JS, I've developed this application as an exercise.

This application depends on these API:
* Angular JS 1.x
* Bootstrap JS 3.x
* CodeIgniter 2.1+
* PHP 5.4+
* MySQL 5+

## To Set up the Application

* Clone the Git repository under Netbeans 8 if you want
* Create a database named `appbooking` under MySQL or MariaDB
* Restore the dump `appbooking.sql` that you'll find in the folder `/db`
* Update the connection to the database in the file `/application/config/database.php`
* Update the admin email address to receive notifications in the file `/application/config/config.php`
* Update the URL to validate subscriptions in the file `/application/config/config.php`
* Create a VHost under Apache HTTP if you want

Here is an example of VHost file :
```
NameVirtualHost *:80

<VirtualHost *:80>
	ServerAdmin webmaster@appbooking
	DocumentRoot "/webapp/folder/appbooking/public/"
	ServerName appbooking
	ErrorLog "logs/appbooking-error.log"
	CustomLog "logs/appbooking-access.log" common
</VirtualHost>
```

## To Log In as Admin

Suppose that you've accessed the application with the following url :
http://appbooking.net

To log in as admin, type the following url :
http://appbooking.net/admin

Then, use these authentication codes :
* login = admin@appbooking.net
* password = demo14

## Built-in Features

### As an Anonymous User

* I can display 3 months by default
* I can display the next month
* I can display only days with events
* I can display the Details View
* I can subscribe an event and an email notification is sent to admin user
* I can unsubscribe an event that requires a email validation from me

### As an Admin User

* I can display the previous month
* I can create/update/delete/duplicate an event
* I can cancel an event with email notification to all attendees
* I can postpone an event with email notification to all attendees
* I can subscribe a user with email notification to the target user
* I can unsubscribe a user who can't subscribe by himself again
* I can mark a user as absent with reason
* I can create an Family Event Type that enables me to subscribe spouse and children
* I can export an event in CSV format

### Event Details

* Event Type : Formation (Blue), Family (Red), Sport (Orange), Standard (Gray)
* Date
* Timeslot
* Location
* Description
* Place available

Please note that the application has been translated in French in hardcoded way... Bad I know ! ^_^'

## Screenshots

### Public Overview

![Public Overview](https://raw.github.com/quentinthai/appbooking/master/screenshots/public_overview.jpg)

### Public Detailview

![Public Detailview](https://raw.github.com/quentinthai/appbooking/master/screenshots/public_detailview.jpg)

### Admin Overview

![Admin Overview](https://raw.github.com/quentinthai/appbooking/master/screenshots/admin_overview.jpg)

### Admin Detailview

![Admin Detailview](https://raw.github.com/quentinthai/appbooking/master/screenshots/admin_detailview.jpg)
